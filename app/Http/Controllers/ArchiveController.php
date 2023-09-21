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

class ArchiveController extends Controller
{
    public function index ()
    {
        $users = User::where('role', '=', '1')->get();
        $projects = Project::where('status', '!=', 'en_cours')->paginate(5);

        session(['typeProject' => 'archive']);

        return view('projects.archive ', compact('projects', 'users'));
    }

    public function search(Request $request)
    {
        $searchSelect = $request->input('searchSelect');
        $searchInput = $request->input('searchInput');

        $users = User::where('role', '=', '1')->get();

        switch($searchSelect)
        {
            case 'user':
                $projects = Project::whereHas('projectmembers', function ($query) use ($searchInput){
                    $query->where('user_id', $searchInput);
                })->where('status', '!=', 'en_cours')
                  ->paginate(5);
                break;

            case 'name':
                $projects = Project::where('name', 'like', "$searchInput%")
                            ->where('status', '!=', 'en_cours')
                            ->paginate(5);
                break;

            case 'start_date':
                $projects = Project::whereDate('start_date', $searchInput)
                            ->where('status', '!=', 'en_cours')
                            ->paginate(5);
                break;

            case 'status':
                $projects = Project::where('status', $searchInput)
                                     ->paginate(5);
                break;
            default:
                return redirect()->back()->with('delete', 'Mauvaise entrée en donnée');
        }
        $search = 1;

        return view('projects.archive', compact('projects', 'users', 'search'));
    }

}
