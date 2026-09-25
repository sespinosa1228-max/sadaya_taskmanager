<div class="form-grid">
    <div class="field full">
        <label class="field-label" for="title">Task name</label>
        <input class="field-input" id="title" name="title" value="{{ old('title', $task->title) }}" maxlength="160" required autofocus placeholder="What needs doing?">
        @error('title')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="field full">
        <label class="field-label" for="description">Notes <span style="font-weight:400;color:#817b77">(optional)</span></label>
        <textarea class="field-textarea" id="description" name="description" maxlength="2000" placeholder="Add a few details...">{{ old('description', $task->description) }}</textarea>
        @error('description')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
        <label class="field-label" for="due_date">Due date <span style="font-weight:400;color:#817b77">(optional)</span></label>
        <input class="field-input" id="due_date" type="date" name="due_date" value="{{ old('due_date', $task->due_date?->toDateString()) }}">
        @error('due_date')<span class="field-error">{{ $message }}</span>@enderror
    </div>
    <div class="field">
        <label class="field-label" for="priority">Priority</label>
        <select class="field-select" id="priority" name="priority" required>
            @foreach (['low' => 'Low', 'normal' => 'Normal', 'high' => 'High'] as $value => $label)
                <option value="{{ $value }}" @selected(old('priority', $task->priority ?? 'normal') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('priority')<span class="field-error">{{ $message }}</span>@enderror
    </div>
</div>
<div class="form-actions">
    <a class="button secondary" href="{{ route('tasks.index', [], false) }}">Cancel</a>
    <button class="button" type="submit">{{ $task->exists ? 'Save changes' : 'Add task' }}</button>
</div>