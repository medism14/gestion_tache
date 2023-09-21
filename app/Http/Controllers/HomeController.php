<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Enums\TaskStatusEnum;
use App\Enums\ProjectStatusEnum;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function logout(Request $request)
    {
        // Votre code de déconnexion ici

        
        auth()->logout();

        // Redirigez l'utilisateur vers la page d'accueil ou une autre page après la déconnexion
        return redirect('/');
    }

    public function register () 
    {
        return view('auth.register');
    }
    public function index()
    {   
        //Verification de l'expiration des tâches ou autres 

        $now = now()->addHours(3);

        //Taches expirés
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

        $count_users = User::count();

        //Pour l'utilisateur
            $count_todayTasks = Task::where('user_id', auth()->user()->id)
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
                               ->count();

            $count_inProgessTasks = Task::where('user_id', auth()->user()->id)
                               ->where('status', TaskStatusEnum::enCours)
                               ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                              ->count();

            $count_onWaitingTasks = Task::where('user_id', auth()->user()->id)
                               ->where('status', TaskStatusEnum::enAttente)
                               ->whereHas('phase', function ($query) {
                                $query->whereHas('project', function ($query) {
                                    $query->where('status', ProjectStatusEnum::enCours);
                                });
                              })
                              ->count();

                //Les 7 derniers jours
                $now = now()->addHours(3);
                $today = $now->format('Y-m-d');

                $yesterday = now()->subHours(27);
                $yesterday = $yesterday->format('Y-m-d');

                $two_day_before = now()->subHours(51);
                $two_day_before = $two_day_before->format('Y-m-d');

                $three_day_before = now()->subHours(75);
                $three_day_before = $three_day_before->format('Y-m-d');

                $four_day_before = now()->subHours(99);
                $four_day_before = $four_day_before->format('Y-m-d');

                $five_day_before = now()->subHours(123);
                $five_day_before = $five_day_before->format('Y-m-d');

                $six_day_before = now()->subHours(147);
                $six_day_before = $six_day_before->format('Y-m-d');

                $seven_day_before = now()->subHours(171);
                $seven_day_before = $seven_day_before->format('Y-m-d');

                // dd($yesterday, $two_day_before, $three_day_before, $four_day_before, $five_day_before, $six_day_before, $seven_day_before);

                //////////////////////////////////////////////////////
                //Pourcentages de tâche accomplis hier
                //////////////////////////////////////////////////////


                $count_tasks = Task::where('status', '=', 'attente')
                                     ->where('user_id', auth()->user()->id)
                                     ->count();

                $count_tasks_yesterday_expirate = Task::where('status', TaskStatusEnum::Expiré)
                                                        ->whereDate('end_date', '=', $yesterday)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $count_tasks_yesterday_finished = Task::where(function ($query) {
                                                        $query->where('status', TaskStatusEnum::nonAboutti)
                                                              ->orWhere('status', TaskStatusEnum::Terminé);
                })
                                                        ->whereDate('confirmed_date', '=', $yesterday)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $total_yesterday = $count_tasks_yesterday_expirate + $count_tasks_yesterday_finished;

                $pourcentage_tasks_yesterday = 100;
                if ($total_yesterday != 0) {
                    $pourcentage_tasks_yesterday = $count_tasks_yesterday_finished / $total_yesterday;
                    $pourcentage_tasks_yesterday = $pourcentage_tasks_yesterday * 100;
                }

                //////////////////////////////////////////////////////
                //Pourcentages de tâche accomplis 2 jour avant
                //////////////////////////////////////////////////////


                $count_tasks_two_day_before_expirate = Task::where('status', TaskStatusEnum::Expiré)
                                                        ->whereDate('end_date', '=', $two_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $count_tasks_two_day_before_finished = Task::where(function ($query) {
                                                        $query->where('status', TaskStatusEnum::nonAboutti)
                                                              ->orWhere('status', TaskStatusEnum::Terminé);
                })
                                                        ->whereDate('confirmed_date', '=', $two_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $total_two_day_before = $count_tasks_two_day_before_expirate + $count_tasks_two_day_before_finished;

                $pourcentage_tasks_two_day_before = 100;
                if ($total_two_day_before != 0) {
                    $pourcentage_tasks_two_day_before = $count_tasks_two_day_before_finished / $total_two_day_before;
                    $pourcentage_tasks_two_day_before = $pourcentage_tasks_two_day_before * 100;
                }

                //////////////////////////////////////////////////////
                //Pourcentages de tâche accomplis 3 jour avant
                //////////////////////////////////////////////////////


                $count_tasks_three_day_before_expirate = Task::where('status', TaskStatusEnum::Expiré)
                                                        ->whereDate('end_date', '=', $three_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $count_tasks_three_day_before_finished = Task::where(function ($query) {
                                                        $query->where('status', TaskStatusEnum::nonAboutti)
                                                              ->orWhere('status', TaskStatusEnum::Terminé);
                })
                                                        ->whereDate('confirmed_date', '=', $three_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $total_three_day_before = $count_tasks_three_day_before_expirate + $count_tasks_three_day_before_finished;

                $pourcentage_tasks_three_day_before = 100;
                if ($total_three_day_before != 0) {
                    $pourcentage_tasks_three_day_before = $count_tasks_three_day_before_finished / $total_three_day_before;
                    $pourcentage_tasks_three_day_before = $pourcentage_tasks_three_day_before * 100;
                }

                //////////////////////////////////////////////////////
                //Pourcentages de tâche accomplis 4 jour avant
                //////////////////////////////////////////////////////


                $count_tasks_four_day_before_expirate = Task::where('status', TaskStatusEnum::Expiré)
                                                        ->whereDate('end_date', '=', $four_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $count_tasks_four_day_before_finished = Task::where(function ($query) {
                                                        $query->where('status', TaskStatusEnum::nonAboutti)
                                                              ->orWhere('status', TaskStatusEnum::Terminé);
                })
                                                        ->whereDate('confirmed_date', '=', $four_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $total_four_day_before = $count_tasks_four_day_before_expirate + $count_tasks_four_day_before_finished;

                $pourcentage_tasks_four_day_before = 100;
                if ($total_four_day_before != 0) {
                    $pourcentage_tasks_four_day_before = $count_tasks_four_day_before_finished / $total_four_day_before;
                    $pourcentage_tasks_four_day_before = $pourcentage_tasks_four_day_before * 100;
                }

                //////////////////////////////////////////////////////
                //Pourcentages de tâche accomplis 5 jour avant
                //////////////////////////////////////////////////////


                $count_tasks_five_day_before_expirate = Task::where('status', TaskStatusEnum::Expiré)
                                                        ->whereDate('end_date', '=', $five_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $count_tasks_five_day_before_finished = Task::where(function ($query) {
                                                        $query->where('status', TaskStatusEnum::nonAboutti)
                                                              ->orWhere('status', TaskStatusEnum::Terminé);
                })
                                                        ->whereDate('confirmed_date', '=', $five_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $total_five_day_before = $count_tasks_five_day_before_expirate + $count_tasks_five_day_before_finished;

                $pourcentage_tasks_five_day_before = 100;
                if ($total_five_day_before != 0) {
                    $pourcentage_tasks_five_day_before = $count_tasks_five_day_before_finished / $total_five_day_before;
                    $pourcentage_tasks_five_day_before = $pourcentage_tasks_five_day_before * 100;
                }

                //////////////////////////////////////////////////////
                //Pourcentages de tâche accomplis 6 jour avant
                //////////////////////////////////////////////////////


                $count_tasks_six_day_before_expirate = Task::where('status', TaskStatusEnum::Expiré)
                                                        ->whereDate('end_date', '=', $six_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $count_tasks_six_day_before_finished = Task::where(function ($query) {
                                                        $query->where('status', TaskStatusEnum::nonAboutti)
                                                              ->orWhere('status', TaskStatusEnum::Terminé);
                })
                                                        ->whereDate('confirmed_date', '=', $six_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $total_six_day_before = $count_tasks_six_day_before_expirate + $count_tasks_six_day_before_finished;

                $pourcentage_tasks_six_day_before = 100;
                if ($total_six_day_before != 0) {
                    $pourcentage_tasks_six_day_before = $count_tasks_six_day_before_finished / $total_six_day_before;
                    $pourcentage_tasks_six_day_before = $pourcentage_tasks_six_day_before * 100;
                }

                //////////////////////////////////////////////////////
                //Pourcentages de tâche accomplis 7 jour avant
                //////////////////////////////////////////////////////


                $count_tasks_seven_day_before_expirate = Task::where('status', TaskStatusEnum::Expiré)
                                                        ->whereDate('end_date', '=', $seven_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $count_tasks_seven_day_before_finished = Task::where(function ($query) {
                                                        $query->where('status', TaskStatusEnum::nonAboutti)
                                                              ->orWhere('status', TaskStatusEnum::Terminé);
                })
                                                        ->whereDate('confirmed_date', '=', $seven_day_before)
                                                        ->where('user_id', auth()->user()->id)
                                                        ->count();

                $total_seven_day_before = $count_tasks_seven_day_before_expirate + $count_tasks_seven_day_before_finished;

                $pourcentage_tasks_seven_day_before = 100;
                if ($total_seven_day_before != 0) {
                    $pourcentage_tasks_seven_day_before = $count_tasks_seven_day_before_finished / $total_seven_day_before;
                    $pourcentage_tasks_seven_day_before = $pourcentage_tasks_seven_day_before * 100;
                }

        
        //Pour l'administrateur
            $count_projectInProgess = Project::where('status', 'en_cours')->count();

            $count_inProgessTasks_admin = Task::whereHas('phase', function ($query) {
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
                ->count();

            return view('home', compact('count_users', 'count_projectInProgess', 'count_inProgessTasks_admin', 'count_todayTasks', 'count_inProgessTasks', 'count_onWaitingTasks', 'pourcentage_tasks_yesterday', 'pourcentage_tasks_two_day_before', 'pourcentage_tasks_three_day_before', 'pourcentage_tasks_four_day_before', 'pourcentage_tasks_five_day_before', 'pourcentage_tasks_six_day_before', 'pourcentage_tasks_seven_day_before'));

        
    }
}
