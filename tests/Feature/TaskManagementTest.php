<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_tasks_and_escapes_task_content(): void
    {
        $task = Task::factory()->create([
            'title' => '<script>alert(1)</script>',
            'description' => 'Review the next release',
        ]);

        $this->get(route('tasks.index'))
            ->assertSee($task->title)
            ->assertSee('Review the next release')
            ->assertSee('href="/tasks/create"', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_task_create_edit_and_detail_pages_are_available(): void
    {
        $task = Task::factory()->create(['title' => 'Plan the week']);

        $this->get(route('tasks.create'))
            ->assertSee('Task name')
            ->assertSee('action="/tasks"', false);
        $this->get(route('tasks.edit', $task))
            ->assertSee('Plan the week')
            ->assertSee('action="/tasks/'.$task->id.'"', false);
        $this->get(route('tasks.show', $task))->assertSee('Plan the week');
    }

    public function test_valid_task_is_created_with_pending_status(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Prepare presentation',
            'description' => 'Collect the latest numbers',
            'due_date' => '2026-10-01',
            'priority' => 'high',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('tasks', [
            'title' => 'Prepare presentation',
            'description' => 'Collect the latest numbers',
            'status' => 'pending',
            'priority' => 'high',
        ]);
    }

    public function test_task_creation_rejects_a_missing_title(): void
    {
        $response = $this->from(route('tasks.create'))->post(route('tasks.store'), [
            'description' => 'A description without a title',
            'priority' => 'normal',
        ]);

        $response->assertRedirectToRoute('tasks.create');
        $response->assertSessionHasErrors(['title']);
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_task_details_can_be_updated(): void
    {
        $task = Task::factory()->create(['title' => 'Old title']);

        $response = $this->put(route('tasks.update', $task), [
            'title' => 'Updated title',
            'description' => 'Updated notes',
            'due_date' => null,
            'priority' => 'low',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated title',
            'description' => 'Updated notes',
            'priority' => 'low',
        ]);
    }

    public function test_task_status_can_be_marked_completed(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $response = $this->patch(route('tasks.status', $task), ['status' => 'completed']);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completed']);
    }

    public function test_task_status_rejects_values_outside_the_supported_choices(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $response = $this->from(route('tasks.index'))
            ->patch(route('tasks.status', $task), ['status' => 'in_progress']);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'pending']);
    }

    public function test_task_list_can_be_filtered_and_searched(): void
    {
        Task::factory()->create(['title' => 'Prepare weekly report', 'status' => 'pending']);
        Task::factory()->create(['title' => 'Submit report', 'status' => 'completed']);
        Task::factory()->create(['title' => 'Schedule dentist', 'status' => 'pending']);

        $this->get(route('tasks.index', ['filter' => 'pending', 'q' => 'report']))
            ->assertSee('Prepare weekly report')
            ->assertDontSee('Submit report')
            ->assertDontSee('Schedule dentist');
    }

    public function test_task_can_be_deleted(): void
    {
        $task = Task::factory()->create();

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirectToRoute('tasks.index');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
