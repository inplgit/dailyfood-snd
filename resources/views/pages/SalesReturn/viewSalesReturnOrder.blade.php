<div style="" id="content" class="container print">
    <hr>
    <div class="model_content_custom">
        <div class="head_main">
            <h1 class="for-print">View Sales Return</h1>
        </div>
        <div class="logo_snd">
            <a class="navbar-brand" href="{{ url('dashboard') }}">
                <span class="brand-logo">
                    <img src="{{ url('/public/assets/images/logo2.png') }}">
                </span>
            </a>
        </div>
        <br>

        <div class="row align-items-center">
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 well">
                <table class="table table-bordered saleOrder_table">
                    <tr><th colspan="2"><h4>Sales Return</h4></th></tr>
                    <tr>
                        <th style="width: 30%;">Sale Return No:</th>
                        <td>{{ $so->return_no }}</td>
                    </tr>
                    <tr>
                        <th>Sale Return Date:</th>
                        <td>{{ date("d-m-Y", strtotime($so->return_date)) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 well">
                <table class="table table-bordered saleOrder_table">
                    <tr>
                        <th>Distributor:</th>
                        <td>{{ $so->distributor->distributor_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>TSO:</th>
                        <td>{{ $so->tso->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Shop:</th>
                        <td>{{ $so->shop->company_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Invoice Type:</th>
                        <td>Cash</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Return Product Details --}}
        <div class="row mt-3">
            <div class="col-lg-12">
                <table class="table table-bordered Order_Details">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Product</th>
                            <th>Flavour</th>
                            <th>Return Qty</th>
                            <th>Retail Price</th>
                            <th>Return Amount</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total_amount = 0; @endphp
                        @foreach ($so->returnDetails as $key => $item)
                            @php
                                $retail = $item->product->retailPrice->retail_price ?? 0;
                                $return_amount = $item->quantity * $retail;
                                $total_amount += $return_amount;
                            @endphp
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                <td>Special</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($retail, 2) }}</td>
                                <td>{{ number_format($return_amount, 2) }}</td>
                                <td>{{ $item->reason ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">Total Return Amount:</th>
                            <th colspan="2">{{ number_format($total_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


