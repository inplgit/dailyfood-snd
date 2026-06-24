@extends('layouts.master')
@section('title', "SND || Route Radius Configurations")
@section('content')

<section id="basic-datatable">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title">Route Radius Configurations</h4>
                    <a href="{{ route('route-radius-configurations.create') }}" class="btn btn-primary"><i data-feather="plus"></i> Add Configuration</a>
                </div>
                <div class="card-body mt-2">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped custom-datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Distributor</th>
                                    <th>TSO</th>
                                    <th>Radius (meters)</th>
                                    <th>Assigned Routes</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($configurations as $config)
                                <tr>
                                    <td>{{ $config->id }}</td>
                                    <td>{{ $config->distributor ? $config->distributor->distributor_name : 'N/A' }}</td>
                                    <td>{{ $config->tso ? $config->tso->name : 'N/A' }}</td>
                                    <td>{{ $config->radius }}</td>
                                    <td>
                                        <span class="badge badge-light-primary">{{ $config->routes->count() }} Routes</span>
                                    </td>
                                    <td>{{ $config->createdBy ? $config->createdBy->name : 'N/A' }}</td>
                                    <td>{{ $config->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('route-radius-configurations.edit', $config->id) }}" class="btn btn-sm btn-warning">
                                            <i data-feather="edit-2"></i> Edit
                                        </a>
                                        <form action="{{ route('route-radius-configurations.destroy', $config->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this configuration?');">
                                                <i data-feather="trash-2"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.custom-datatable').DataTable();
    });
</script>
@endsection
