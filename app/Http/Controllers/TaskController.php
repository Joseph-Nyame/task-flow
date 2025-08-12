<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\DTOs\TaskDTO;
use App\Models\Project;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Actions\Tasks\CreateTask;
use App\Actions\Tasks\ManageTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::orderBy('name')->get();
        
        $selectedProjectId = $request->query('project_id');
        
        $tasks = Task::when($selectedProjectId, function ($query) use ($selectedProjectId) {
                return $query->where('project_id', $selectedProjectId);
            })
            ->with('project') // Eager load projects to avoid N+1 queries
            ->orderBy('priority')
            ->get();
        
        return view('tasks.index', compact('projects', 'tasks', 'selectedProjectId'));
    }


    public function create(): View
    {
        $projects = Project::all();
        return view('tasks.create', compact('projects'));
    }

    public function store(CreateTaskRequest $request, CreateTask $createTask): RedirectResponse
    {
        $dto = TaskDTO::fromRequest($request);
        $createTask->execute($dto);
        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task): View
    {
        $projects = Project::all();
        return view('tasks.edit', compact('task', 'projects'));
    }

    public function update(UpdateTaskRequest $request, Task $task, ManageTask $manageTask): RedirectResponse
    {
        $dto = TaskDTO::fromUpdateRequest($request,$task);
        $manageTask->update($task, $dto);
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task, ManageTask $manageTask): RedirectResponse
    {
        $manageTask->delete($task);
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    
   public function reorder(Request $request)
    {
        $order = $request->order;
        
        foreach ($order as $item) {
            Task::where('id', $item['id'])->update(['priority' => $item['priority']]);
        }
        
        return response()->json(['success' => true]);
    }
}