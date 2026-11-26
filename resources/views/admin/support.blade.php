@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <h3 class="text-2xl font-bold mb-4">Support</h3>

    <div class="wg-box">
        <table class="table-auto w-full mt-6 border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-3 border text-center" style="width: 80px;">User ID</th>
                    <th class="px-4 py-3 border">User Name</th>
                    <th class="px-4 py-3 border">Subject</th>
                    <th class="px-4 py-3 border">Created At</th>
                    <th class="px-4 py-3 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="border px-4 py-3 text-center">{{ $user->id }}</td>
                    <td class="border px-4 py-3">{{ $user->name }}</td>
                    <td class="border px-4 py-3">{{ $user->email }}</td>
                    <td class="border px-4 py-3">{{ $user->created_at->format('d M Y H:i') }}</td>
                    <td class="border px-4 py-3">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-500">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection