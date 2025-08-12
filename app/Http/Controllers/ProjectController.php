<?php

namespace App\Http\Controllers;

use App\Actions\Projects\CreateProject;
use App\Actions\Projects\ManageProject;
use App\DTOs\ProjectDTO;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(CreateProjectRequest $request, CreateProject $createProject): RedirectResponse
    {
        $dto = ProjectDTO::fromRequest($request);
        $createProject->execute($dto);
        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $project->load('tasks');
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project, ManageProject $manageProject): RedirectResponse
    {
        $dto = ProjectDTO::fromUpdateRequest($request, $project);
        $manageProject->update($project, $dto);
        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project, ManageProject $manageProject): RedirectResponse
    {
        $manageProject->delete($project);
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}