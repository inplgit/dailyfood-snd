@extends('layouts.master')
@section('title', "Edit Channel")
@section('content')

<div class="container">
    <h2>Edit Channel</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('channels.update', $channel->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $channel->name }}" required>
        </div>
      
        <button type="submit" class="btn btn-success mt-2">Update</button>
    </form>
</div>
@endsection
