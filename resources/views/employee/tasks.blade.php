@extends('layouts.employee')
@section('content')
<div class="container py-4">

    <h3 class="mb-4">My Tasks</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th width="180">Update Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->description }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $task->status)) }}</td>

                    <td>
                        <form action="{{ route('employee.tasks.update', $task->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <select name="status" class="form-select">
                                <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>To Do</option>
                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                            </select>

                            <button class="btn btn-primary btn-sm mt-2">Update</button>
                        </form>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
