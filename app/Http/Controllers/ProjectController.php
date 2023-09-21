<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Phase;
use App\Models\User;
use App\Models\Task;
use App\Models\ProjectMember;
use App\Http\Requests\ProjectRequest;
use App\Enums\ProjectStatusEnum;
use App\Enums\PhaseStatusEnum;
use App\Enums\TaskStatusEnum;
use Carbon\Carbon;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\Session;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $projects = Project::where('status', 'en_cours')->paginate(5);

        session(['typeProject' => 'project']);

        return view('projects.index', compact('projects','users'));
    }

    public function tasks (string $id, string $phase, string $phase_id)
    {
        //Recuperation du projet, phase et users pour la creation et le renvoie en arrière ou devant
        $project = Project::findOrFail($id);
        $phase = Phase::findOrFail($phase_id);

        $users = User::whereIn('id', function ($query) use ($id){
            $query->select('user_id')
                  ->from('project_members')
                  ->where('project_id', $id);
        })->get();

        //Taches expirés
        $now = now()->addHours(3);
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

        //Donner l'active tab
        $session_active = session('active_tab');

        if ($session_active == null)
        {
            session(['active_tab' => 'today']);
            $session_active = 'today';
        }

        switch ($session_active)
        {
            case 'past':
                $tasks = Task::where('phase_id', $phase_id)
                              ->where(function ($query) {
                                $query->where('status', TaskStatusEnum::Terminé)
                                      ->where('status', TaskStatusEnum::nonAboutti)
                                      ->whereDate('confirmed_date', '<', $now);
                              })
                              ->orWhere(function ($query) {
                                $query->where('status', TaskStatusEnum::Expiré)
                                      ->whereDate('end_date', '<', $now);
                              })
                              ->paginate(5);
                break;
            case 'today':
                $tasks = Task::where('phase_id', $phase_id)
                            ->where(function ($query) use ($now) {
                                $query->whereIn('status', [TaskStatusEnum::enCours, TaskStatusEnum::enAttente])
                                      ->whereDate('start_date', '<=', $now)
                                      ->whereDate('end_date', '>=', $now);
                            })
                            ->orWhere(function ($query) use ($now) {
                                $query->whereIn('status', [TaskStatusEnum::Terminé, TaskStatusEnum::nonAboutti])
                                      ->whereDate('confirmed_date', $now);
                            })
                            ->orWhere(function ($query) use ($now) {
                                $query->where('status', TaskStatusEnum::Expiré)
                                      ->whereDate('end_date', $now);
                            })
                               ->paginate(5);
                break;
            case 'future':
                $tasks = Task::where('phase_id', $phase_id)
                               ->whereDate('start_date', '>', $now)
                               ->paginate(5);
                break;
        }

       $min_date = $project->start_date;

        return view('projects.tasks', compact('tasks','phase','project','users','min_date'));
    }

    public function taskCreate (TaskRequest $request, $id, $phase, $phase_id)
    {
        $task = Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => TaskStatusEnum::enAttente,
            'user_id' => $request->user,
            'phase_id' => $phase_id,
        ]);

        $now = now()->addHours(3);

        if ($task->end_date < $now)
        {
            $task->update([
                'status' => TaskStatusEnum::Expiré,
            ]);
        }

        if ($task->start_date < $now && $task->status == 'en_attente')
        {
            $task->update([
                'status' => TaskStatusEnum::enCours,
            ]);
        }

        return redirect()->route('projects.tasks.index', ['id' => $id, 'phase' => $phase, 'phase_id' => $phase_id])->with('success', 'La tâche a bien été créé');
    }

    public function tasksSearch (Request $request, $id, $phase, $phase_id)
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
                $tasks = Task::where('phase_id', $phase_id)
                    ->whereDate('start_date', '=', $request->search)
                    ->paginate(5);
                break;
            case 'end_date':
                $tasks = Task::where('phase_id', $phase_id)
                    ->whereDate('end_date', '=', $request->search)
                    ->paginate(5);
                break;
            case 'status':
                $tasks = Task::where('phase_id', $phase_id)
                    ->where('status', $request->search)
                    ->paginate(5);
                break;
            case 'name':
                $result = transform_string($search);
                $tasks = Task::where('phase_id', $phase_id)
                    ->where(function($query) use ($filter, $result) {
                     $query->where('name', 'like', $result['upper'] . '%')
                           ->orWhere('name', 'like', $result['lower'] . '%');
                        })
                    ->paginate(5);
                break;
        }

        $project = Project::findOrFail($id);
        $phase = Phase::findOrFail($phase_id);
        $users = User::whereIn('id', function ($query) use ($id){
                    $query->select('user_id')
                          ->from('project_members')
                          ->where('project_id', $id);
                })->get();


        $search = 1;

        return view('projects.tasks', compact('tasks','phase','project','users','search'));
    }

    public function taskUpdate (TaskRequest $request, $id, $phase, $phase_id)
    {
        $task = Task::findOrFail($request->id);

        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('projects.tasks.index', ['id' => $id, 'phase' => $phase, 'phase_id' => $phase_id])->with('success', 'La tâche a bien été modifié');
    }

    public function fetchTasks (Request $request, $id, $phase, $phase_id, $active_tab)
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

    public function getTaskInfo(Request $request, $id, $phase, $phase_id, $task_id)
    {
        $task = Task::findOrFail($task_id);

        $users = User::whereIn('id', function ($query) use ($id){
            $query->select('user_id')
                  ->from('project_members')
                  ->where('project_id', $id);
        })->get();

        return response()->json([
            'task' => $task,
            'user' => $task->user,
            'users' => $users,
        ]);
    }

    public function taskDelete ($id, $phase, $phase_id, $task_id)
    {
        $task = Task::findOrFail($task_id);

        $task->delete();

        return redirect()->back()->with('delete', 'La tâche a bien été supprimée');
    }

    public function getProjectInfo($id)
    {
        $project = Project::findOrFail($id);

        $usersNoGroup = User::whereNotIn('id', function ($query) use ($id){
            $query->select('user_id')
                  ->from('project_members')
                  ->where('project_id', $id);
        })->get();

        $usersOnGroup = User::whereIn('id', function ($query) use ($id){
            $query->select('user_id')
                  ->from('project_members')
                  ->where('project_id', $id);
        })->get();

        return response()->json([
            'project' => $project,
            'usersNoGroup' => $usersNoGroup,
            'usersOnGroup' => $usersOnGroup
        ]);
    }


    public function create(ProjectRequest $request)
    {
        $i = $request->input('countProjectMembers');

        $users = array();

        while ($i > 0)
        {   
            $id = $request->input("member$i");
            $user = User::findorFail($id);
            array_push($users, $user);
            $i--;
        }

        $users = array_reverse($users);

        $actualDateTime = Carbon::now()->addHours(3)->format('Y-m-d H:i:s');        

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $actualDateTime,
            'status' => ProjectStatusEnum::enCours,
        ]);

        foreach ($users as $user)
        {
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
            ]);
        }

        $prospection = Phase::create([
            'name' => 'Prospection',
            'status' => PhaseStatusEnum::enCours,
            'project_id' => $project->id,
        ]);

        $execution = Phase::create([
            'name' => 'Execution',
            'status' => PhaseStatusEnum::enAttente,
            'project_id' => $project->id,
        ]);

        $recouvrement = Phase::create([
            'name' => 'Recouvrement',
            'status' => PhaseStatusEnum::enAttente,
            'project_id' => $project->id,
        ]);

        return to_route('projects.index')->with('success', 'Le projet a bien été créé');
    }   

   
    public function search(Request $request)
    {
        $searchSelect = $request->input('searchSelect');
        $searchInput = $request->input('searchInput');

        switch($searchSelect)
        {
            case 'user':
                $users = User::all();
                $projects = Project::whereHas('projectmembers', function ($query) use ($searchInput){
                    $query->where('user_id', $searchInput);
                })->where('status', 'en_cours')
                  ->paginate(5);
                break;

            case 'name':
                $users = User::all();
                $projects = Project::where('name', 'like', "$searchInput%")
                            ->where('status', 'en_cours')
                            ->paginate(5);
                break;

            case 'start_date':
                $users = User::all();
                $projects = Project::whereDate('start_date', $searchInput)
                            ->where('status', 'en_cours')
                            ->paginate(5);
                break;

            default:
                return redirect()->back()->with('delete', 'Mauvaise entrée en donnée');
        }
        $search = 1;

        return view('projects.index', compact('projects', 'users', 'search'));
    }

    
    public function update(ProjectRequest $request)
    {
        $project_id = $request->id;
        $project = Project::findOrFail($project_id);

        $i = $request->input('countProjectMembersEdit');

        $users = array();

        while ($i > 0) 
        {
            $user_id = $request->input("member$i");
            $user = User::findOrFail($user_id);
            array_push($users, $user);
            $i--;
        }

        $users = array_reverse($users);

        ProjectMember::where('project_id', $project_id)->delete();

        foreach ($users as $user)
        {
            ProjectMember::create([
                'project_id' => $project_id,
                'user_id' => $user->id,
            ]);
        }

        $project->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return back()->with('success', 'Le projet a bien été modifié');
    }

    
    public function show(Request $request,  $id)
    {

        Session::forget('active_tab');

        $project = Project::findOrFail($id);

        $phases = Phase::whereHas('project', function ($query) use ($id) {
            $query->where('id', $id);
        })->get();

        foreach ($phases as $phase)
        {
            if ($phase->name == 'Prospection')
            {
                $countTasksProspectionenCours = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::enCours)->count();
                $countTasksProspectionenAttente = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::enAttente)->count();
                $countTasksProspectionnonAboutti = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::nonAboutti)->count();
                $countTasksProspectionTerminé = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::Terminé)->count();
                $countTasksProspectionExpiré = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::Expiré)->count();
            }   

            if ($phase->name == 'Execution')
            {   
                $countTasksExecutionenCours = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::enCours)->count();
                $countTasksExecutionenAttente = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::enAttente)->count();
                $countTasksExecutionnonAboutti = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::nonAboutti)->count();
                $countTasksExecutionTerminé = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::Terminé)->count();
                $countTasksExecutionExpiré = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::Expiré)->count();
            }

            if ($phase->name == 'Recouvrement')
            {
                $countTasksRecouvrementenCours = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::enCours)->count();
                $countTasksRecouvrementenAttente = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::enAttente)->count();
                $countTasksRecouvrementnonAboutti = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::nonAboutti)->count();
                $countTasksRecouvrementTerminé = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::Terminé)->count();
                $countTasksRecouvrementExpiré = Task::where('phase_id', $phase->id)->where('status', TaskStatusEnum::Expiré)->count();
            }
        }        

        return view('projects.show', compact('project', 'phases', 'countTasksProspectionenCours', 'countTasksProspectionenAttente', 'countTasksProspectionnonAboutti', 'countTasksProspectionTerminé', 'countTasksProspectionExpiré', 'countTasksExecutionenCours', 'countTasksExecutionenAttente', 'countTasksExecutionnonAboutti', 'countTasksExecutionTerminé', 'countTasksExecutionExpiré', 'countTasksRecouvrementenCours', 'countTasksRecouvrementenAttente', 'countTasksRecouvrementnonAboutti', 'countTasksRecouvrementTerminé', 'countTasksRecouvrementExpiré'));
    }

    public function confirmPhase ($id, $phase_id)
    {
        $now = now()->addHours(3);
        $phaseInConfirmation = Phase::findOrFail($phase_id);

        //Confiramtion de la phase en question
        $phaseInConfirmation->update([
                'status' => PhaseStatusEnum::Terminé,
            ]);

        if ($phaseInConfirmation->name == 'Prospection')
        {
            //phases relié au projet seulement l'execution et le recouvrement
            $phases = Phase::where('project_id', $id)->where(function ($query) {
                $query->where('name', 'Execution')
                      ->orWhere('name', 'Recouvrement');
            })->get();

            foreach ($phases as $phase)
            {
                $phase->update([
                    'status' => PhaseStatusEnum::enCours,
                ]);
            }
        }
        else if ($phaseInConfirmation->name == 'Execution')
        {
            
        }
        else if ($phaseInConfirmation->name == 'Recouvrement')
        {
            $project = Project::findOrFail($id);

            $project->update([
                'status' => ProjectStatusEnum::Terminé,
                'end_date' => $now,
            ]);

            return redirect()->route('projects.archive');
        }

        return redirect()->route('projects.show', ['id' => $id]);
    }   

    public function cancelProject ($id)
    {
        $project = Project::findOrFail($id);

        $project->update([
            'status' => ProjectStatusEnum::nonAboutti,
        ]);

        return redirect()->route('projects.archive');
    }    
    
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);

        // Suppression des membres du projet
        $project->projectmembers()->delete();

        // Suppression des tâches de chaque phase
        foreach ($project->phases as $phase)
        {
            $phase->tasks()->delete();
        }

        // Suppression des phases du projet
        $project->phases()->delete();

        // Suppression du projet lui-même
        $project->delete();

        return back()->with('delete', 'Le projet a bien été supprimé');
    }

    public function tasksToday () 
    {
        //Taches expirés
        $now = now()->addHours(3);
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

        $tasks = Task::whereHas('phase', function ($query) {
                        $query->whereHas('project', function ($query) {
                            $query->where('status', ProjectStatusEnum::enCours);
                        });
                    })
                    ->where(function ($query) use ($now) {
                        $query->whereIn('status', [TaskStatusEnum::enCours, TaskStatusEnum::enAttente])
                              ->whereDate('start_date', '<=', $now)
                              ->whereDate('end_date', '>=', $now);
                    })
                    ->orWhere(function ($query) use ($now) {
                        $query->whereIn('status', [TaskStatusEnum::Terminé, TaskStatusEnum::nonAboutti])
                              ->whereDate('confirmed_date', $now);
                    })
                    ->orWhere(function ($query) use ($now) {
                        $query->where('status', TaskStatusEnum::Expiré)
                              ->whereDate('end_date', $now);
                    })
                    ->paginate(5);

        return view('projects.tasksToday', compact('tasks'));
    }

    public function taskInforForToday ($task_id)
    {
         $task = Task::findOrFail($task_id);

         $id = $task->phase->project->id;

        $users = User::whereIn('id', function ($query) use ($id){
            $query->select('user_id')
                  ->from('project_members')
                  ->where('project_id', $id);
        })->get();

        $user = User::findOrFail($task->user->id);

        return response()->json([
            'task' => $task,
            'user' => $task->user,
            'users' => $users,
        ]);
    }


}
