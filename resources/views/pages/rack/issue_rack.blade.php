@php

    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'SND || Caregory')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Issue rack </h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('issue_rack_to_shop') }}" id="subm" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Distributor Name </label>
                                        <select required onchange="get_tso()" class="form-control" name="distributor_id"
                                            id="distribuotr_id" required>
                                            <option value="">select</option>
                                            @foreach ($master->get_all_distributor_user_wise() as $row)
                                                <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select class="form-control" onchange="get_shop_by_tso()" id="tso_id" name="tso_id" required>
                                            <option value="">select</option>
                                            {{-- @foreach ($master->get_all_tso() as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Shop</label>
                                        <select class="form-control" id="shop_id" name="shop_id" required>
                                            <option value="">select</option>

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Rack</label>
                                        <select class="form-control" id="rack" name="rack_id" required>
                                            <option value="">select</option>
                                            @foreach ($racks as $row)
                                                <option value="{{$row->id}}">{{$row->rack_code}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1">Create Item</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection
