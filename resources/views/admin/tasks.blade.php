@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <h4 class="text-2xl font-bold mb-6">Task Board</h4>

    @if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <!-- Add Task Form -->
    <form action="{{ route('admin.tasks.store') }}" method="POST" class="mb-6">
        @csrf
        <div class="wg-box">
            <!-- <div class="flex gap-4"> -->
            <fieldset class="title">
                <div class="body-title">Title <span class="tf-color-1">*</span></div>
                <input type="text" name="title" placeholder="Task title" class="border p-2 rounded" required>
            </fieldset>
            <fieldset class="description">
                <div class="body-title">Description <span class="tf-color-1">*</span></div>
                <textarea name="description" placeholder="Task description" class="border p-2 rounded" rows="2"></textarea>
            </fieldset>
            <fieldset class="assigned_to">
                <select name="assigned_to" class="border p-2 rounded">
                    <option value="">Assign to</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </fieldset>
            <button type="submit" class="btn btn-outline-primary">
                Add Task
            </button>
            <!-- </div> -->
        </div>
    </form>

    <div class="row" style="margin-top: 16px;">
        @foreach(['todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done'] as $key => $label)
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header font-weight-bold">{{ $label }}</div>
                <ul class="list-group list-group-flush">
                    @foreach($tasks->where('status', $key) as $task)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $task->title }}
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.tasks.show', $task->id) }}"
                                class="btn btn-info btn-sm mr-1"
                                title="View task">
                                👁
                            </a>

                            <!-- Status dropdown -->
                            <form action="{{ route('admin.tasks.update', $task) }}" method="POST" class="mr-1">
                                @csrf
                                @method('PUT')
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                    <option value="todo" {{ $task->status=='todo'?'selected':'' }}>To Do</option>
                                    <option value="in_progress" {{ $task->status=='in_progress'?'selected':'' }}>In Progress</option>
                                    <option value="done" {{ $task->status=='done'?'selected':'' }}>Done</option>
                                </select>
                            </form>
                            <!-- Delete button -->
                            <form action="{{ route('admin.tasks.delete', $task) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">X</button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection