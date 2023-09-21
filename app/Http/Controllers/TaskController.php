<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Enums\ProjectStatusEnum;
use App\Enums\TaskStatusEnum;
use Carbon\Carbon;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\Session;

class TaskController extends Controller
{
    public function index ()
    {
        $now = now()->addHours(3);

        $session_active = session('active_tab');

        switch ($session_active)
        {
            case 'past':
                $tasks = Task::where('user_id', auth()->user()->id)
                              ->where(function ($query) {
                                $query->where('status', TaskStatusEnum::Terminé)
                                      ->where('status', TaskStatusEnum::nonAboutti)
                                      ->whereDate('confirmed_date', '<', $now);
                              })
                              ->orWhere(function ($query) {
                                $query->where('status', TaskStatusEnum::Expiré)
                                      ->whereDate('end_date', '<', $now);
                              })
                              ->where(function ($query) {
                                $query->where('status', TaskStatusEnum::enCours)
                                      ->Orwhere('status', TaskStatusEnum::enAttente);
                               })
                              ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                              ->paginate(5);
                break;
            case 'today':
                $tasks = Task::where('user_id', auth()->user()->id)
                               ->where(function ($query) use ($now) {
                                    $query->whereIn('status', [TaskStatusEnum::enCours, TaskStatusEnum::enAttente])
                                          ->whereDate('start_date', '<=', $now)
                                          ->whereDate('end_date', '>=', $now);
                                })
                               ->where(function ($query) {
                                $query->where('status', TaskStatusEnum::enCours)
                                      ->Orwhere('status', TaskStatusEnum::enAttente);
                               })
                               ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                               ->paginate(5);
                break;
            case 'future':
                $tasks = Task::where('user_id', auth()->user()->id)
                               ->whereDate('start_date', '>', $now)
                               ->where(function ($query) {
                                $query->where('status', TaskStatusEnum::enCours)
                                      ->Orwhere('status', TaskStatusEnum::enAttente);
                               })
                               ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                               ->paginate(5);
                break;
        }


        return view('tasks.index', compact('tasks'));
    }

    public function changementSession ()
    {
        session(['active_tab' => 'today']);

        $now = now()->addHours(3);

        //Taches expirés
        $now = now()->addHours(3)->format('Y-m-d H:i:s');
        $tasksExpirate = Task::where('end_date', '<', $now)->where('status', TaskStatusEnum::enCours)->get();
        foreach ($tasksExpirate as $task)
        {
            $task->update([
                'status' => TaskStatusEnum::Expiré,
            ]);
        }

        //Tâches en execution
        $tasksEnCours = Task::where('start_date', '<', $now)->where('status', TaskStatusEnum::enAttente)->get();
        foreach ($tasksEnCours as $task)
        {
            $task->update([
                'status' => TaskStatusEnum::enCours,
            ]);
        }
        return redirect()->route('tasks.index');
    }

    public function fetchTasks (Request $request, $active_tab)
    {
        session(['active_tab' => $active_tab]);

        // Réinitialiser la pagination à 1
        $page = 1;

        $session_active = session(['active_tab']);

        return response()->json([
            'active_tab' => $session_active,
            'page' => $page,
        ]);
    }

    public function search (Request $request)
    {
        $page = $request->query('page');

        $page = intval($page);

        if ($page != null) {

             $search = session('search');
             $filter = session('filter');

        }else {
            $search = $request->get('search');
            $filter = $request->get('filter');

            session(['search' => $search]);
            session(['filter' => $filter]);
        }

        function transform_string($string) {
          $result = [
            'lower' => strtolower($string),
            'upper' =>strtoupper($string),
            'first_word' =>ucfirst(strtolower($string))
          ];
          return $result;
        }

        switch ($request->filter)
        {
            case 'start_date':
                $tasks = Task::where('user_id', auth()->user()->id)
                    ->whereDate('start_date', '=', $request->search)
                    ->where(function ($query) {
                                $query->where('status', TaskStatusEnum::enCours)
                                      ->Orwhere('status', TaskStatusEnum::enAttente);
                               })
                    ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                    ->paginate(5);
                break;
            case 'end_date':
                $tasks = Task::where('user_id', auth()->user()->id)
                    ->whereDate('end_date', '=', $request->search)
                    ->where(function ($query) {
                                $query->where('status', TaskStatusEnum::enCours)
                                      ->Orwhere('status', TaskStatusEnum::enAttente);
                               })
                    ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                    ->paginate(5);
                break;
            case 'status':
                $tasks = Task::where('user_id', auth()->user()->id)
                    ->where('status', $request->search)
                    ->where(function ($query) {
                                $query->where('status', TaskStatusEnum::enCours)
                                      ->Orwhere('status', TaskStatusEnum::enAttente);
                               })
                    ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                    ->paginate(5);
                break;
            case 'name':
                $result = transform_string($search);
                $tasks = Task::where('user_id', auth()->user()->id)
                    ->where(function($query) use ($filter, $result) {
                     $query->where('name', 'like', $result['upper'] . '%')
                           ->orWhere('name', 'like', $result['lower'] . '%');
                        })
                    ->where(function ($query) {
                                $query->where('status', TaskStatusEnum::enCours)
                                      ->Orwhere('status', TaskStatusEnum::enAttente);
                               })
                    ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                    ->paginate(5);
                break;
        }

        $search = 1;

        return view('tasks.index', compact('tasks', 'search'));
    }

    public function searchDashboard ($type)
    {
        $now = now()->addHours(3);
        switch ($type)
        {
            case 'todayTasks':
                $tasks = Task::where('user_id', auth()->user()->id)
                               ->whereDate('start_date', '<=', $now)
                               ->whereDate('end_date', '>=', $now)
                               ->where(function ($query) {
                                $query->where('status', TaskStatusEnum::enCours)
                                      ->Orwhere('status', TaskStatusEnum::enAttente);
                               })
                               ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                               ->paginate(5);
                break;
            case 'inProgessTasks':
                $tasks = Task::where('user_id', auth()->user()->id)
                               ->where('status', TaskStatusEnum::enCours)
                               ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                              ->paginate(5);
                break;
            case 'onWaitingTasks':
                $tasks = Task::where('user_id', auth()->user()->id)
                               ->where('status', TaskStatusEnum::enAttente)
                               ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                              ->paginate(5);
                break;
        }

        $search = 1;

        return view('tasks.index', compact('tasks', 'search'));
    }

    public function getTaskInfo (Request $request, $task_id)
    {
        $task = Task::findOrFail($task_id);

        $user = $task->user;

        return response()->json([
            'task' => $task,
            'user' => $user,
        ]);
    }

    public function TaskConfirm (Request $request)
    {
        $comment = $request->confirm_comment; 
        $id = $request->id;

        $now = now()->addHours(3);

        $task = Task::findOrFail($id);

        $task->update([
            'confirmed_date' => $now,
            'status' => TaskStatusEnum::Terminé,
            'comment' => $comment,
        ]);

        return redirect()->route('tasks.index')->with('success', 'La tache a bien étée confirmé');
    }

    public function TaskCancel (Request $request)
    {
        $comment = $request->cancel_comment; 
        $id = $request->id;

        $now = now()->addHours(3);

        $task = Task::findOrFail($id);

        $task->update([
            'confirmed_date' => $now,
            'status' => TaskStatusEnum::nonAboutti,
            'comment' => $comment,
        ]);

        return redirect()->route('tasks.index')->with('delete', 'La tache a bien étée annulé');

    }
}
