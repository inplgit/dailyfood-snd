@extends('layouts.master')

@section('title', "SND || Add New Designation")
@section('content')




<section id="multiple-column-form">

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Receipt Voucher(Sales)</h4>
                </div>
                <div class="card-body">
                    <form id="subm" method="POST" action="{{ route('receipt-voucher.store') }}" class="form">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label class="control-label">Distributor</label>
                                    <select class="form-control select2"  name="distributor_id" id="distributor_id">
                                        <option value="">Select a Distributor: </option>
                                        @foreach ($distributors as $distributor)
                                            <option value="{{ $distributor->id }}">{{ $distributor->distributor_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label class="control-label">Delivery Man</label>
                                    <select class="form-control" name="delivery_man_id" id="delivery_man_id">
                                        <option value="">Select a Delivery Man: </option>
                                        @foreach ($deliveryMan as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label class="control-label">TSO</label>
                                    <select onchange="get_route_by_tso()" class="form-control select2" name="tso_id" id="tso_id">
                                        <option value="">Select a TSO: </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label class="control-label">Route</label>
                                    <select class="form-control select2" name="route_id" id="route_id">
                                        <option value="">Select a Route: </option>
                                        @foreach ($routes as $route)
                                            <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label class="control-label">Shop</label>
                                    <select onchange="get_shop_outstanding()" class="form-control select2" name="shop_id" id="shop_id">
                                        <option value="">Select a Shop: </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Old Receivable<strong>*</strong></label>
                                    <input readonly id="receivable" type="text" class="form-control" placeholder="Designation Name"/>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Issue Date<strong>*</strong></label>
                                    <input value="{{ date('Y-m-d') }}" name="issue_date" value="{{ old('issue_date') }}" type="date" class="form-control" placeholder="issue_date"/>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Amount<strong>*</strong></label>
                                    <input name="amount" value="{{ old('amount') ?? 0 }}" type="text" class="form-control" placeholder="amount"/>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label class="control-label">Mode of Payment</label>
                                    <select id="mode_of_payment" name="mode_of_payment" class="select2 form-control form-control-lg">
                                        <option value="cash">Cash</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>
                                <div class="bank-details d-none">
                                    <label class="control-label">Bank Name</label>
                                    <input name="bank" value="{{ old('bank') }}" type="text" class="form-control" placeholder="bank"/>
                                    <label class="control-label">Check#</label>
                                    <input name="cheque_no" value="{{ old('cheque_no') }}" type="text" class="form-control" placeholder="cheque_no"/>
                                    <label class="control-label">Date</label>
                                    <input name="cheque_date" value="{{ old('cheque_date') }}" type="date" class="form-control" placeholder="cheque_date"/>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label class="control-label">Execution</label>
                                    <select disabled name="execution" class="select2 form-control form-control-lg">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Details</label>
                                    <input name="detail" value="{{ old('detail') }}" type="text" class="form-control" placeholder="detail"/>
                                </div>
                            </div>

                            <div class="col-md-12 seprator">
                                <hr>
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
@section('script')
{{-- <script>
    var latitude = 24.8607343; // Example latitude
    var longitude = 67.0011364; // Example longitude

</script> --}}
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
                        console.log(data);
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
                            var so_amount = parseFloat(element?.shop_outstanding?.so_amount);
                            var sr_amount = parseFloat(element?.shop_outstanding?.sr_amount);
                            var rv_amount = parseFloat(element?.shop_outstanding?.rv_amount);
                            var balance_amount = parseFloat(element?.balance_amount);
                            var debit_credit = parseFloat(element?.debit_credit);
                            if (debit_credit == 1) {
                                var outstanding = so_amount +sr_amount + balance_amount -rv_amount;
                            }
                            else{
                                var outstanding = so_amount +sr_amount - balance_amount -rv_amount;
                            }

                            if (isNaN(outstanding))
                            {
                                outstanding = 0;
                            }

                            options += `<option data-outstanding="${outstanding}" value="${element.id}">${element.company_name}</option>`
                        });
                        $('#shop_id').html(options);

                    }
                });
            });
        });

        function get_shop_outstanding()
        {
            var outstanding = $('#shop_id option:selected').data('outstanding');;
            $('#receivable').val(outstanding);
        }


    </script>
           <script>
            $(document).ready(function() {

                $('.select2').select2();
            });
        </script>
@endsection
