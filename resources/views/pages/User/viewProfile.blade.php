@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'Add User')
@section('content')
<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">User Profile</h4>
                </div>
                <div class="card-body">
                    <div class="row">


                        <div class="col-md-12">

                            <div class="account-inner">
                                <div class="davtar">
                                    <span class="avatar"> <img class="round" id="profile-image2" src="{{ $user_data->image ? url('storage/app/public/profile/'.$user_data->image) :'https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/avatars/1.png'}}" alt="avatar" > </span>
                                    <div class="content_profile">
                                        <h5>{{ $user_data->name }}</h5>
                                        <!-- <p>Bridging the Future of Industry.</p> -->
                                        <p>{{ $user_data->email }}</p>
                                        {{-- <p>Amaz@innovative-net.com</p> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <br>
                                <label>{{$user_data->name}}</label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Designation</label>
                                <br>
                                <label>{{MasterFormsHelper::get_user_type($user_data->user_type)}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <br>
                                <label>{{$user_data->email}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Compnay Name</label>
                                <br>
                                <label>{{$user_data->user_detail->company_name?? '--'}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>User Code</label>
                                <br>
                                <label>{{$user_data->user_detail->user_code?? '--'}}</label>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee ID</label>
                                <br>
                                <label>{{$user_data->user_detail->employee_id?? '--'}}</label>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <br>
                                <label>{{$user_data->user_detail->phone_number?? '--'}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address</label>
                                <br>
                                <label>{{$user_data->user_detail->address ?? '--'}}</label>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>CNIC</label>
                                <br>
                                <label>{{$user_data->user_detail->cnic ?? '--'}}</label>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="form-group">
                                <label>City</label>
                                <br>
                                <label>{{$user_data->user_detail->cities->name ?? '--'}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>State</label>
                                <br>
                                <label>{{$user_data->user_detail->state ?? '--'}}</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Country</label>
                                <br>
                                <label>{{$user_data->user_detail->country ?? '--'}}</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date of Joining</label>
                                <br>
                                <label>{{$user_data->user_detail->date_of_join ?? '--'}}</label>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date of Leaving</label>
                                <br>
                                <label>{{$user_data->user_detail->date_of_leaving ?? '--'}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
