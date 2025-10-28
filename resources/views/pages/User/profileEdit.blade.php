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
                    <h4 class="card-title">User Profile Edit</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="account-inner">
                                <div class="davtar">
                                    <span class="avatar"> <img class="round" id="profile-image2" src="{{ Auth::user()->image ? url('storage/app/public/profile/'.Auth::user()->image) :'https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/avatars/1.png'}}" alt="avatar" > </span>
                                    <div class="content_profile">
                                        <h5>{{ Auth::user()->name }}</h5>
                                        <!-- <p>Bridging the Future of Industry.</p> -->
                                        <p>{{ Auth::user()->email }}</p>
                                        {{-- <p>Amaz@innovative-net.com</p> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <br>
                                <label>{{Auth::user()->name}}</label>
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Type</label>
                                <br>
                                <label>{{MasterFormsHelper::get_user_type(Auth::user()->user_type)}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <br>
                                <label>{{Auth::user()->email}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection