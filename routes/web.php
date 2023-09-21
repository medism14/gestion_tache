<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ArchiveController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Models\User;



Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/deconnexion', [HomeController::class, 'logout'])->name('deco');


Route::match(['get','post'],'/new_user', [UserController::class, 'create'])->name('user_create');


// Routes accessibles uniquement aux utilisateurs authentifiés
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');


        ///////////////////////////////////////////////
        ///////////////////USERS///////////////////////
        ///////////////////////////////////////////////

    Route::get('/informations_personnels', [UserController::class, 'PersonnalInformation'])->name('personnal.informations');

        ///////////////////////////////////////////////
        /////////////////////TASKS/////////////////////
        ///////////////////////////////////////////////
    
    Route::match(['get','post'], '/changement_session', [TaskController::class, 'changementSession'])->name('changementSession');

    Route::prefix('/gestion_tache')->name('tasks.')->group (function () {
        Route::match(['get','post'], '', [TaskController::class, 'index'])->name('index');
        Route::match(['get','post'], '/fetchTasks/{active_tab}', [TaskController::class, 'fetchTasks'])->name('fetchTasks');
        Route::match(['get','post'], '/search', [TaskController::class, 'search'])->name('search');
        Route::match(['get','post'], '/search/{type}', [TaskController::class, 'searchDashboard'])->name('searchDashboard');
        Route::match(['get','post'], '/get_task_info/{task_id}', [TaskController::class, 'getTaskInfo'])->name('info');
        Route::match(['get','post'], '/task_confirm', [TaskController::class, 'taskConfirm'])->name('confirm');
        Route::match(['get','post'], '/task_cancel', [TaskController::class, 'taskCancel'])->name('cancel');
    });





});

        

// Routes accessibles uniquement aux utilisateurs ayant le role administrateur
Route::middleware('admin')->group(function ()
{
        ///////////////////////////////////////////////
        ////////////////////USERS//////////////////////
        ///////////////////////////////////////////////

    Route::match(['get','post'],'/gestion_utilisateur', [UserController::class, 'index'])->name('users');
    Route::match(['get','post'],'/search_user', [UserController::class, 'search'])->name('user_search');
    Route::match(['get','post'],'/edit_user', [UserController::class, 'edit'])->name('user_update');
    Route::match(['get','post'],'/delete_user/{id}', [UserController::class, 'delete'])->name('user_delete');
    Route::get('/get_user_info/{id}', [UserController::class, 'getUserInfo']);

        ///////////////////////////////////////////////
        ///////////////DASHBOARD TASKS/////////////////
        ///////////////////////////////////////////////

    Route::match(['get', 'post'], '/tasksToday', [ProjectController::class, 'tasksToday'])->name('tasks.today');
        Route::match(['get', 'post'], '/get_task_info/{task_id}', [ProjectController::class, 'taskInforForToday'])->name('tasks.info.today');

        ///////////////////////////////////////////////
        /////////////////////PROJECTS//////////////////
        ///////////////////////////////////////////////

    Route::prefix('/gestion_projet')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::post('/new_project', [ProjectController::class, 'create'])->name('create');
        Route::match(['get', 'post'] ,'/edit_project', [ProjectController::class, 'update'])->name('update');
        Route::get('/delete_project/{id}', [ProjectController::class, 'destroy'])->name('destroy');
        Route::post('/project_search', [ProjectController::class, 'search'])->name('search');
        Route::get('/get_project_info/{id}', [ProjectController::class, 'getProjectInfo'])->name('info');
        Route::get('/cancel_project/{id}', [ProjectController::class, 'cancelProject'])->name('cancel');
        Route::get('/archivages_projet', [ArchiveController::class, 'index'])->name('archive');
        Route::post('/archivages_search', [ArchiveController::class, 'search'])->name('archive.search');
        

        Route::prefix('/show_project/{id}')->group (function () {
            Route::get('/', [ProjectController::class, 'show'])->name('show');
            Route::get('/confirm_phase/{phase_id}', [ProjectController::class, 'confirmPhase'])->name('phase.confirm');

            Route::prefix('/{phase}/{phase_id}/tasks')->name('tasks.')->group (function () {
                Route::match(['get', 'post'], '', [ProjectController::class, 'tasks'])->name('index');
                Route::match(['get', 'post'], '/task_search', [ProjectController::class, 'tasksSearch'])->name('search');
                Route::match(['get','post'], '/task_create', [ProjectController::class, 'taskCreate'])->name('create');
                Route::match(['get', 'post'], '/task_update', [ProjectController::class, 'taskUpdate'])->name('update');
                Route::match(['get', 'post'], '/task_confirm/{task_id}', [ProjectController::class, 'taskConfirm'])->name('confirm');
                Route::match(['get', 'post'], '/task_delete/{task_id}', [ProjectController::class, 'taskDelete'])->name('delete');
                Route::match(['get', 'post'], '/get_task_info/{task_id}', [ProjectController::class, 'getTaskInfo'])->name('info');
                Route::match(['get', 'post'], '/fetchTasks/{active_tab}', [ProjectController::class, 'fetchTasks'])->name('activeTab');
            });
        });  
        
    });    

});



