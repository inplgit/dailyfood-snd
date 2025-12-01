@extends('layouts.master')
@section('title', " channel")
@section('content')

<div class="container">
    <h2>Channels</h2>
    <a href="{{ route('channels.create') }}" class="btn btn-primary mb-2">Add Channel</a>

    <!-- @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif -->

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($channels as $key => $channel)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $channel->name }}</td>
                <td>
                    <a href="{{ route('channels.edit', $channel->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('channels.destroy', $channel->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
