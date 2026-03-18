<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
echo MasterFormsHelper::PrintHead($from, $to, 'New Load Sheet', $tso_id);
?>

@if(count($so_data) > 0)
    <table class="table table-bordered table-striped" style="font-size: 14px;">
        <thead style="background:#e9ecef;">
            <tr>
                <th style="width:22%; text-align:center;">Employee / TSO Name</th>
                <th style="width:28%; text-align:center;">Product Name</th>
                <th style="width:8%;  text-align:center;">Rate</th>
                <th style="width:6%;  text-align:center;">Qty</th>
                <th style="width:6%;  text-align:center;">FOC</th>
                <th style="width:6%;  text-align:center;">Avl</th>
                <th style="width:8%;  text-align:center;">Sample</th>
                <th style="width:10%; text-align:center;">Amount</th>
                <th style="width:6%;  text-align:center;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grand_qty    = 0;
                $grand_foc    = 0;
                $grand_avl    = 0;
                $grand_sample = 0;
                $grand_amount = 0;
            @endphp

            @foreach($so_data as $employee_name => $products)
                <!-- Employee Group Header -->
                <tr style="background:#d4e6f1; font-weight:bold;">
                    <td colspan="9">{{ $employee_name }}</td>
                </tr>

                @php
                    $emp_qty    = 0;
                    $emp_foc    = 0;
                    $emp_avl    = 0;
                    $emp_sample = 0;
                    $emp_amount = 0;
                @endphp

                @foreach($products as $product)
                    <tr>
                        <td style="padding-left: 30px; color:#555;"> <!-- indent under employee --></td>
                        <td>{{ $product->product_name }}</td>
                        <td style="text-align:right;">{{ number_format($product->rate, 2) }}</td>
                        <td style="text-align:center;">{{ number_format($product->qty) }}</td>
                        <td style="text-align:center;">{{ number_format($product->foc) }}</td>
                        <td style="text-align:center;">{{ number_format($product->avl) }}</td>
                        <td style="text-align:center;">{{ number_format($product->sample) }}</td>
                        <td style="text-align:right;">{{ number_format($product->amount, 2) }}</td>
                        <td style="text-align:center;">{{ $product->remarks ?? '0' }}</td>
                    </tr>

                    @php
                        $emp_qty    += $product->qty;
                        $emp_foc    += $product->foc;
                        $emp_avl    += $product->avl;
                        $emp_sample += $product->sample;
                        $emp_amount += $product->amount;
                    @endphp
                @endforeach

                <!-- Subtotal per Employee -->
                <tr style="background:#f8f9fa; font-weight:600;">
                    <td style="text-align:right; padding-right:15px;">{{ $employee_name }} Total</td>
                    <td></td>
                    <td></td>
                    <td style="text-align:center;">{{ number_format($emp_qty) }}</td>
                    <td style="text-align:center;">{{ number_format($emp_foc) }}</td>
                    <td style="text-align:center;">{{ number_format($emp_avl) }}</td>
                    <td style="text-align:center;">{{ number_format($emp_sample) }}</td>
                    <td style="text-align:right;">{{ number_format($emp_amount, 2) }}</td>
                    <td></td>
                </tr>

                @php
                    $grand_qty    += $emp_qty;
                    $grand_foc    += $emp_foc;
                    $grand_avl    += $emp_avl;
                    $grand_sample += $emp_sample;
                    $grand_amount += $emp_amount;
                @endphp
            @endforeach

            <!-- Grand Total -->
            <tr style="background:#dee2e6; font-weight:bold; font-size:15px;">
                <td style="text-align:right; padding-right:15px;">Grand Total</td>
                <td></td>
                <td></td>
                <td style="text-align:center;">{{ number_format($grand_qty) }}</td>
                <td style="text-align:center;">{{ number_format($grand_foc) }}</td>
                <td style="text-align:center;">{{ number_format($grand_avl) }}</td>
                <td style="text-align:center;">{{ number_format($grand_sample) }}</td>
                <td style="text-align:right;">{{ number_format($grand_amount, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

@else
    <div class="alert alert-danger text-center">
        No Record Found
    </div>
@endif