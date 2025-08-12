<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_projects_index_page()
    {
        $projects = Project::factory()->count(3)->create();

        $response = $this->get(route('projects.index'));

        $response->assertStatus(200)
                 ->assertViewIs('projects.index')
                 ->assertViewHas('projects', fn ($viewProjects) => $viewProjects->count() === 3);
    }

    /** @test */
    public function it_displays_the_project_create_page()
    {
        $response = $this->get(route('projects.create'));

        $response->assertStatus(200)
                 ->assertViewIs('projects.create');
    }

    /** @test */
    public function it_can_store_a_project()
    {
        $data = [
            'name' => 'Test Project',
            'description' => 'This is a test project.',
        ];

        $response = $this->post(route('projects.store'), $data);

        $response->assertRedirect(route('projects.index'))
                 ->assertSessionHas('success', 'Project created successfully.');
        $this->assertDatabaseHas('projects', ['name' => 'Test Project']);
    }

   

    /** @test */
    public function it_displays_the_project_edit_page()
    {
        $project = Project::factory()->create();

        $response = $this->get(route('projects.edit', $project));

        $response->assertStatus(200)
                 ->assertViewIs('projects.edit')
                 ->assertViewHas('project', $project);
    }

    /** @test */
    public function it_can_update_a_project()
    {
        $project = Project::factory()->create();
        $data = [
            'name' => 'Updated Project',
            'description' => 'Updated description.',
        ];

        $response = $this->put(route('projects.update', $project), $data);

        $response->assertRedirect(route('projects.index'))
                 ->assertSessionHas('success', 'Project updated successfully.');
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project',
            'description' => 'Updated description.',
        ]);
    }

    /** @test */
    public function it_can_delete_a_project()
    {
        $project = Project::factory()->create();

        $response = $this->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'))
                 ->assertSessionHas('success', 'Project deleted successfully.');
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    /** @test */
    public function it_displays_the_tasks_index_page_without_filter()
    {
        $project = Project::factory()->create();
        $tasks = Task::factory()->count(3)->create(['project_id' => $project->id]);

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200)
                 ->assertViewIs('tasks.index')
                 ->assertViewHas('tasks', fn ($viewTasks) => $viewTasks->count() === 3)
                 ->assertViewHas('projects')
                 ->assertViewHas('selectedProjectId', null);
    }

    /** @test */
    public function it_displays_the_tasks_index_page_with_project_filter()
    {
        $project1 = Project::factory()->create();
        $project2 = Project::factory()->create();
        $tasks1 = Task::factory()->count(2)->create(['project_id' => $project1->id]);
        $tasks2 = Task::factory()->count(1)->create(['project_id' => $project2->id]);

        $response = $this->get(route('tasks.index', ['project_id' => $project1->id]));

        $response->assertStatus(200)
                 ->assertViewIs('tasks.index')
                 ->assertViewHas('tasks', fn ($viewTasks) => $viewTasks->count() === 2)
                 ->assertViewHas('selectedProjectId', (string) $project1->id);
    }
   

    /** @test */
    public function it_can_store_a_task()
    {
        $project = Project::factory()->create();
        $data = [
            'name' => 'Test Task',
            'priority' => 1,
            'project_id' => $project->id,
        ];

        $response = $this->post(route('tasks.store'), $data);

        $response->assertRedirect(route('tasks.index'))
                 ->assertSessionHas('success', 'Task created successfully.');
        $this->assertDatabaseHas('tasks', [
            'name' => 'Test Task',
            'priority' => 1,
            'project_id' => $project->id,
        ]);
    }

    /** @test */
    public function it_displays_the_task_edit_page()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->get(route('tasks.edit', $task));

        $response->assertStatus(200)
                 ->assertViewIs('tasks.edit')
                 ->assertViewHas('task', $task)
                 ->assertViewHas('projects');
    }

    /** @test */
    public function it_can_update_a_task()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        $newProject = Project::factory()->create();
        $data = [
            'name' => 'Updated Task',
            'priority' => 2,
            'project_id' => $newProject->id,
        ];

        $response = $this->put(route('tasks.update', $task), $data);

        $response->assertRedirect(route('tasks.index'))
                 ->assertSessionHas('success', 'Task updated successfully.');
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task',
            'priority' => 2,
            'project_id' => $newProject->id,
        ]);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'))
                 ->assertSessionHas('success', 'Task deleted successfully.');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function it_can_reorder_tasks()
    {
        $project = Project::factory()->create();
        $task1 = Task::factory()->create(['project_id' => $project->id, 'priority' => 1]);
        $task2 = Task::factory()->create(['project_id' => $project->id, 'priority' => 2]);
        $task3 = Task::factory()->create(['project_id' => $project->id, 'priority' => 3]);

        $newOrder = [
            ['id' => $task3->id, 'priority' => 1], // Move task3 to position 1
            ['id' => $task1->id, 'priority' => 2], // Move task1 to position 2
            ['id' => $task2->id, 'priority' => 3]  // Move task2 to position 3
        ];

        $response = $this->postJson(route('tasks.reorder'), [
            'order' => $newOrder,
            'project_id' => $project->id
        ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        // Verify the new order in the database
        $this->assertDatabaseHas('tasks', ['id' => $task3->id, 'priority' => 1]);
        $this->assertDatabaseHas('tasks', ['id' => $task1->id, 'priority' => 2]);
        $this->assertDatabaseHas('tasks', ['id' => $task2->id, 'priority' => 3]);

        // Verify that only these tasks were updated
        $this->assertEquals(3, Task::where('project_id', $project->id)->count());
    }
}