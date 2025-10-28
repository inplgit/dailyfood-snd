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
                    <h4 class="card-title">Shop View</h4>
                </div>
                <div class="card-body">
                    <div class="row">


                        <div class="col-md-12">

                            <div class="account-inner">
                                <div class="davtar">
                                 <span class="avatar">
    <img class="round" id="profile-image2"
         src="{{ $show_data->image ? 'https://laziza.inplsnd.com/storage/shop_image/' . rawurlencode($show_data->image) : 'https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/avatars/1.png' }}"
         alt="avatar">
</span>

                                    <div class="content_profile">
                                        <h5>{{ $show_data->company_name }}</h5>
                                        <!-- <p>Bridging the Future of Industry.</p> -->
                                        <p>{{ $show_data->mobile_no }}</p>
                                        {{-- <p>Amaz@innovative-net.com</p> --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Shop Name</label>
                                <br>
                                <label>{{$show_data->company_name}}</label>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mobile No</label>
                                <br>
                                <label>{{$show_data->mobile_no}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Owner Name</label>
                                <br>
                                <label>{{$show_data->contact_person?? '--'}}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>CNIC</label>
                                <br>
                                <label>{{$show_data->cnic?? '--'}}</label>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
