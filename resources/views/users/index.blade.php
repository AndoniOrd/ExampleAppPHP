@extends('layouts.app')

@section('content')
<div class="container">
    <h1>User List</h1>
    
    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <a href="{{ route('users.create') }}">Create New User</a>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Account Status</th>
                <th>Creation Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->first_name }}</td>
                <td>{{ $user->last_name }}</td>
                <td>{{ $user->email_address }}</td>
                <td>{{ $user->role }}</td>
                <td>{{ $user->account_status }}</td>
                <td>{{ $user->creation_date }}</td>
                <td>
                    <a href="{{ route('users.show', $user) }}">View</a> |
                    <a href="{{ route('users.edit', $user) }}">Edit</a> |
                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
