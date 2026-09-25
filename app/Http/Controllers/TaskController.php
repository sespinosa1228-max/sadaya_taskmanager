<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'filter' => ['nullable', Rule::in(['all', 'pending', 'completed'])],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $filter = $validated['filter'] ?? 'all';
        $search = $validated['q'] ?? '';

        $tasks = Task::query()
            ->when($filter !== 'all', fn ($query) => $query->where('status', $filter))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("case when status = 'pending' then 0 else 1 end")
            ->orderByRaw('due_date is null')
            ->orderBy('due_date')
            ->latest()
            ->get();

        return view('tasks.index')->with([
            'tasks' => $tasks,
            'filter' => $filter,
            'search' => $search,
            'totalTasks' => Task::count(),
            'pendingTasks' => Task::where('status', 'pending')->count(),
            'completedTasks' => Task::where('status', 'completed')->count(),
        ]);
    }

    public function create(): View
    {
        return view('tasks.create', ['task' => new Task]);
    }

    public function store(Request $request): RedirectResponse
    {
        Task::create($this->validatedTask($request));

        return redirect()->away(route('tasks.index', [], false))
            ->with('success', 'Task added to your list.');
    }

    public function show(Task $task): View
    {
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task): View
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $task->update($this->validatedTask($request));

        return redirect()->away(route('tasks.index', [], false))
            ->with('success', 'Task details updated.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'completed'])],
        ]);

        $task->update($validated);

        return redirect()->away(route('tasks.index', [], false))
            ->with('success', 'Task status updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->away(route('tasks.index', [], false))
            ->with('success', 'Task removed.');
    }

    /**
     * @return array{title: string, description: ?string, due_date: ?string, priority: string}
     */
    private function validatedTask(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high'])],
        ]);
    }
}
