<?php 
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')


<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add  Route TSO Wise</h4>
                </div>
                <div class="card-body">

    <form id="list_data"  method="get" action="{{ Request::url('') }}">
    
        <div class="row">
        <div class="col-md-6">
          
                <label>Distributor Name</label>
               <select  onchange="get_tso()" class="form-control" id="distribuotr_id" name="distribuotr_id">
                <option value="">select</option>
                @foreach ( $master->get_all_distributor_user_wise() as $row )
                   <option value="{{ $row->id }}">{{ $row->distributor_name }}</option> 
                @endforeach
                
               </select>
            </div>
         
            <div onchange="get_ajax_data()" class="col-md-6">
                <label>TSO Name</label>
               <select class="form-control" name="tso_id" id="tso_id">
                <option value="">select</option>
               </select>
            </div>
        </div>

        
    </form>
</br>
   <span id="data">
      </div>
     </div>
    </div>
</div>
</section>
    
         



@endsection
@section('script')
   
@endsection
