<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectTaskMember;
use App\Models\ProjectTaskComment;
use App\Models\ProjectTaskAttachment;
use App\Models\ProjectTaskLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ProjectMobileController extends Controller
{
    /**
     * Get NIK of authenticated employee.
     */
    private function getNik()
    {
        return DB::table('users_karyawan')->where('id_user', Auth::user()->id)->value('nik');
    }

    /**
     * Display a listing of projects for the mobile user.
     */
    public function index()
    {
        $nik = $this->getNik();
        if (empty($nik)) {
            return Redirect::back()->with(messageError('Data karyawan Anda belum ditautkan ke user login.'));
        }

        // Get projects where employee is a member
        $projects = Project::whereHas('members', function($q) use ($nik) {
            $q->where('nik', $nik);
        })
        ->with(['category', 'members.karyawan'])
        ->orderBy('end_date', 'asc')
        ->get();

        // Tasks assigned to employee across all projects
        $tasksCount = ProjectTask::whereHas('members', function($q) use ($nik) {
            $q->where('nik', $nik);
        })
        ->where('status', '!=', 'completed')
        ->count();

        return view('project.mobile.index', compact('projects', 'tasksCount'));
    }

    /**
     * Display the specified project with tasks assigned to the employee.
     */
    public function show($id)
    {
        try {
            $idDec = Crypt::decrypt($id);
            $nik = $this->getNik();

            $project = Project::with(['category', 'members.karyawan'])->findOrFail($idDec);

            // Verify membership
            $isMember = DB::table('project_members')->where('project_id', $idDec)->where('nik', $nik)->exists();
            if (!$isMember) {
                return Redirect::route('myproject.index')->with(messageError('Anda tidak memiliki akses ke project ini.'));
            }

            // Tasks in this project assigned to this employee
            $myTasks = ProjectTask::where('project_id', $idDec)
                ->whereHas('members', function($q) use ($nik) {
                    $q->where('nik', $nik);
                })
                ->orderBy('due_date', 'asc')
                ->get();

            // Other tasks in this project
            $otherTasks = ProjectTask::where('project_id', $idDec)
                ->whereDoesntHave('members', function($q) use ($nik) {
                    $q->where('nik', $nik);
                })
                ->whereNull('parent_id') // only parent tasks
                ->orderBy('due_date', 'asc')
                ->get();

            return view('project.mobile.show', compact('project', 'myTasks', 'otherTasks'));
        } catch (\Exception $e) {
            return Redirect::route('myproject.index')->with(messageError($e->getMessage()));
        }
    }

    /**
     * Show task details on mobile.
     */
    public function showTask($id)
    {
        try {
            $idDec = Crypt::decrypt($id);
            $nik = $this->getNik();

            $task = ProjectTask::with([
                'project',
                'parent',
                'subtasks',
                'members.karyawan',
                'comments.karyawan',
                'attachments.karyawan',
                'logs.karyawan'
            ])->findOrFail($idDec);

            // Verify that employee belongs to project
            $isMember = DB::table('project_members')->where('project_id', $task->project_id)->where('nik', $nik)->exists();
            if (!$isMember) {
                return Redirect::route('myproject.index')->with(messageError('Anda tidak memiliki akses ke task ini.'));
            }

            return view('project.mobile.task_show', compact('task'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    /**
     * Mobile progress update helper.
     */
    public function updateProgress(Request $request, $id)
    {
        try {
            $idDec = Crypt::decrypt($id);
            $nik = $this->getNik();

            $isAssigned = DB::table('project_task_members')->where('task_id', $idDec)->where('nik', $nik)->exists();
            if (!$isAssigned) {
                return Redirect::back()->with(messageError('Anda tidak ditugaskan pada task ini sehingga tidak dapat memperbarui progress.'));
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Akses ditolak atau data tidak valid.'));
        }

        return app(ProjectTaskController::class)->updateProgress($request, $id);
    }

    /**
     * Mobile store comment.
     */
    public function storeComment(Request $request, $id)
    {
        try {
            $idDec = Crypt::decrypt($id);
            $nik = $this->getNik();

            $task = ProjectTask::findOrFail($idDec);
            $isMember = DB::table('project_members')->where('project_id', $task->project_id)->where('nik', $nik)->exists();
            if (!$isMember) {
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke project ini.'));
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Akses ditolak atau data tidak valid.'));
        }

        return app(ProjectTaskController::class)->storeComment($request, $id);
    }

    /**
     * Mobile upload attachment.
     */
    public function storeAttachment(Request $request, $id)
    {
        try {
            $idDec = Crypt::decrypt($id);
            $nik = $this->getNik();

            $isAssigned = DB::table('project_task_members')->where('task_id', $idDec)->where('nik', $nik)->exists();
            if (!$isAssigned) {
                return Redirect::back()->with(messageError('Anda tidak ditugaskan pada task ini sehingga tidak dapat mengunggah lampiran.'));
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Akses ditolak atau data tidak valid.'));
        }

        return app(ProjectTaskController::class)->storeAttachment($request, $id);
    }
}
