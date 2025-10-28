@extends('layouts.master')
@section('title', "SND || Update Designation")
@section('content')
<?php 
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>

<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Update Receipt Voucher</h4>
                </div>
                <div class="card-body">
                    <form id="subm" method="POST" action="{{ route('receipt-voucher.update', $record->id) }}" class="form">
                        @csrf
                        @method('patch')
                        <div class="row">                                                       
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>Distributor</label>
                                    <select class="form-control" name="distributor_id" id="distributor_id">
                                        <option value="">Select a Distributor: </option>
                                        @foreach ($distributors as $distributor)                                                            
                                            <option {{ $record->distributor_id == $distributor->id ? 'selected' : '' }} value="{{ $distributor->id }}">{{ $distributor->distributor_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>  
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>Delivery Man</label>
                                    <select class="form-control" name="delivery_man_id" id="delivery_man_id">
                                        <option value="">Select a Delivery Man: </option>
                                        @foreach ($deliveryMan as $user)                                                            
                                            <option {{ $record->delivery_man_id == $user->id ? 'selected' : '' }} value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>  
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>TSO</label>
                                    <select class="form-control" name="tso_id" id="tso_id">
                                        @foreach ( $master->get_all_tso_by_distributor_id($record->distributor_id) as $key => $row)
                                        <option @if($row->id==$record->tso_id) selected @endif value="{{ $record->tso_id }}">{{ $record->tso->name }}</option>
                                        @endforeach
                                       
                                    </select>
                                </div>
                            </div>  
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>Route</label>
                                    <select class="form-control" name="route_id" id="route_id">
                                        <option value="">Select a Route: </option>
                                        @foreach ($routes as $route)                                                            
                                            <option {{ $record->route_id == $route->id ? 'selected' : '' }} value="{{ $route->id }}">{{ $route->route_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>  
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>Shop</label>
                                    <select class="form-control" name="shop_id" id="shop_id">
                                        <option value="{{ $record->shop_id }}">{{ $record->shop->company_name }}</option>                                        
                                    </select>
                                </div>
                            </div>  
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Old Receivable<strong>*</strong></label>
                                    <input readonly type="text" class="form-control" placeholder="Designation Name"/>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Issue Date<strong>*</strong></label>
                                    <input name="issue_date" value="{{ $record->issue_date ?? old('issue_date') }}" type="date" class="form-control" placeholder="issue_date"/>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Amount<strong>*</strong></label>
                                    <input name="amount" value="{{ $record->amount ?? old('amount') ?? 0 }}" type="text" class="form-control" placeholder="amount"/>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>Mode of Payment</label>
                                    <select id="mode_of_payment" name="mode_of_payment" class="select2 form-control form-control-lg">
                                        <option {{ $record->mode_of_payment == 'cash' ? 'selected' : '' }} value="cash">Cash</option>
                                        <option {{ $record->mode_of_payment == 'cheque' ? 'selected' : '' }} value="cheque">Cheque</option>
                                    </select>
                                </div>
                                <div class="bank-details {{ $record->mode_of_payment == 'cheque' ? '' : 'd-none' }}">
                                    <label>Bank Name</label>
                                    <input name="bank" value="{{ $record->bank }}" type="text" class="form-control" placeholder="bank"/>
                                    <label>Check#</label>
                                    <input name="cheque_no" value="{{ $record->cheque_no }}" type="text" class="form-control" placeholder="cheque_no"/>
                                    <label>Date</label>
                                    <input name="cheque_date" value="{{ $record->cheque_date }}" type="date" class="form-control" placeholder="cheque_date"/>
                                </div>
                            </div>  
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>Execution</label>
                                    <select name="execution" class="select2 form-control form-control-lg">
                                        <option {{ $record->execution ? 'selected' : '' }} value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Details</label>
                                    <input name="detail" value="{{ $record->detail ?? old('detail') }}" type="text" class="form-control" placeholder="detail"/>
                                </div>
                            </div>  

                            <div class="col-md-12 seprator">
                                <hr>
                            </div>                           

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-1">Update Item</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#mode_of_payment').on('change', function(){
                if ($(this).val() == 'cheque') {
                    $('.bank-details').removeClass('d-none');
                } else {
                    $('.bank-details').addClass('d-none');
                }
            });
            $(document).on('change', '#distributor_id', function(){                               
                var options = '<option value="">Select a TSO </option>';
                var id = $(this).val()
                $.ajax({
                    type: "get",
                    url: "{{ route('tso.by.distributor') }}",
                    data: {id:id},                    
                    cache: false,
                    success: function(data)
                    {  
                        data.res.forEach(element => {                         
                            options += `<option value="${element.id}">${element.name}</option>`
                        });
                        $('#tso_id').html(options);

                    }
                });
            });
            $(document).on('change', '#tso_id', function(){                               
                var options = '<option value="">Select a Shop </option>';
                var id = $(this).val()
                $.ajax({
                    type: "get",
                    url: "{{ route('shop.by.tso') }}",
                    data: {id:id},                    
                    cache: false,
                    success: function(data)
                    {  
                        data.res.forEach(element => {                         
                            options += `<option value="${element.id}">${element.company_name}</option>`
                        });
                        $('#shop_id').html(options);

                    }
                });
            });
        });
    </script>
@endsection

