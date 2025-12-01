@extends('layouts.master')
@section('title', "create channel")
@section('content')

<div class="container">
    <h2>Add Channel</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('channels.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
      
        <button type="submit" class="btn btn-success mt-2">Save</button>
    </form>
</div>
@endsection
