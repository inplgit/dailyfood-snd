@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')


                <section id="multiple-column-form">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">ADD Sales Return</h4>
                                </div>
                                <div class="card-body">
                                    <form method="get" action="{{ route('sales_return.create') }}"  id="list_data" class="form">

                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Type So No</label>
                                                    <input type="text" name="so_no" class="form-control" placeholder="SO No"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="generate text-left">
                                               
                                                    <button onclick="get_ajax_data()" type="button" class="btn btn-primary mr-1">Generate</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <form method="POST" id="subm" action="{{ route('sales_return.store') }}">
                                        @csrf
                                    <span id="data">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Basic Floating Label Form section end -->

@endsection

@section('script')
<script>
function calc(qty ,already_returnd,key)
    {

       var return_qty = parseFloat($('#return'+key).val());
        var total_qty = qty - already_returnd;
        (return_qty > total_qty) ?  $('#return'+key).val(0) : 0;

    }
</script>
@endsection
