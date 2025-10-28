@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'Attendence Report Detail')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Attendence Report Detail: {{$tsoName}}</h4>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.No#</th>
                                <th>ChecKin</th>
                                {{-- <th>ChecKin Location</th> --}}
                                <th>ChecKout</th>
                                {{-- <th>ChecKin Location</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendences as $Key => $attendence)
                                <tr>
                                    <td>{{ ++$Key }}</td>
                                    <td>{{ $attendence->in }}</td>
                                    {{-- <td>{{ $attendence->in }}</td> --}}
                                    <td>{{ $attendence->out }}</td>
                                    {{-- <td>{{ $attendence->out }}</td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection