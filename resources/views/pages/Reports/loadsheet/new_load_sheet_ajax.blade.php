<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
echo MasterFormsHelper::PrintHead($from, $to, 'New Load Sheet', $tso_id);
?>
<style>
.table{border-collapse:collapse;width:100%;font-size:13px;}
.table th{background:#f1f3f5;color:#333;font-weight:600;border:1px solid #dcdcdc;text-align:center;padding:8px;}
.table td{border:1px solid #e0e0e0;padding:7px;vertical-align:middle;}
/* Employee Header */
.employee-row{background:#e6f2ff;font-weight:600;color:#2c3e50;}
/* Product Rows */
.product-row:hover td{background:#f9f9f9;}
/* Indent product name */
.product-name{padding-left:20px;color:#555;}
/* Subtotal */
.subtotal-row{background:#f7f7f7;font-weight:600;}
/* Grand Total */
.grand-total-row{background:#dfe6e9;font-weight:bold;font-size:14px;}
/* Alignment */
.text-right{text-align:right;}
.text-center{text-align:center;}
</style>

@if(count($so_data) > 0)
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <td  style="  border-bottom: none !important;"></td>
                <td  style=" border-bottom: none !important;"></td>
                <th style="background:#dfe5ec !important;"></th> 
                <th style="background:#dfe5ec !important;"></th> 
                <th style="background:#dfe5ec !important;"></th> 
                <th colspan="2" style="text-align:center; border-bottom: none !important;background:#dfe5ec !important;">Grand Total</th>
                <th style="background:#dfe5ec !important;"></th> 
                <th style="background:#dfe5ec !important;"></th> 
            </tr>
            <tr>
                <td style="width:22%;border: 1px solid #000 !important;">Employee / TSO Name</td>
                <td style="width:28%; border: 1px solid #000 !important;">Product Name</td>
                <th style="width:8%; text-align:right;background:#dfe5ec !important;">Rate</th>
                <th style="width:6%;text-align:right; background:#dfe5ec !important;">Qty</th>
                <th style="width:6%;text-align:right;background:#dfe5ec !important;">FOC</th>
                <th style="width:6%; text-align:right;background:#dfe5ec !important;">Avl</th>
                <th style="width:8%; text-align:right;background:#dfe5ec !important;">Sample</th>
                <th style="width:10%; text-align:right;background:#dfe5ec !important;">Amount</th>
                <th style="width:6%; text-align:right;background:#dfe5ec !important;">Remarks</th>
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

                @php
                    $emp_qty    = 0;
                    $emp_foc    = 0;
                    $emp_avl    = 0;
                    $emp_sample = 0;
                    $emp_amount = 0;
                    $rowspan = count($products) + 1; // products + total row
                    $first = true;
                @endphp

                @foreach($products as $product)
                    <tr>
                        @if($first)
                            <!-- Employee Name (ROWSPAN) -->
                            <td rowspan="{{ $rowspan }}" style="font-weight:600; vertical-align: middle;border: 1px solid #000 !important;">
                                {{ $employee_name }}
                            </td>
                            @php $first = false; @endphp
                        @endif

                        <td style="border: 1px solid #000 !important;">{{ $product->product_name }}</td>
                        <td class="text-right" style="border-bottom: 1px solid #000 !important;">{{ number_format($product->rate, 2) }}</td>
                        <td class="text-right" style="border-bottom: 1px solid #000 !important;">{{ number_format($product->qty) }}</td>
                        <td class="text-right" style="border-bottom: 1px solid #000 !important;">{{ number_format($product->foc) }}</td>
                        <td class="text-right" style="border-bottom: 1px solid #000 !important;">{{ number_format($product->avl) }}</td>
                        <td class="text-right" style="border-bottom: 1px solid #000 !important;">{{ number_format($product->sample) }}</td>
                        <td class="text-right" style="border-bottom: 1px solid #000 !important;">{{ number_format($product->amount, 2) }}</td>
                        <td class="text-right" style="border-bottom: 1px solid #000 !important;">{{ $product->remarks ?? '0' }}</td>
                    </tr>

                    @php
                        $emp_qty    += $product->qty;
                        $emp_foc    += $product->foc;
                        $emp_avl    += $product->avl;
                        $emp_sample += $product->sample;
                        $emp_amount += $product->amount;
                    @endphp
                @endforeach

                <!-- Employee Total -->
                <tr style=" font-weight:600;">
                    <!-- <td>{{ $employee_name }} Total</td> -->
                    <!-- <td></td>
                    <td></td>
                    <td class="text-right">{{ number_format($emp_qty) }}</td>
                    <td class="text-right">{{ number_format($emp_foc) }}</td>
                    <td class="text-right">{{ number_format($emp_avl) }}</td>
                    <td class="text-right">{{ number_format($emp_sample) }}</td>
                    <td class="text-right">{{ number_format($emp_amount, 2) }}</td>
                    <td></td> -->
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
            <tr style=" font-weight:bold;">
                <!-- <td colspan="2" style="text-align:left;">Grand Total</td> -->
                <th  style="text-align:left;border: 1px solid #000 !important; border-right: none !important;background:#dfe5ec !important;">{{ $employee_name }} Total</th>
                <th style="text-align:left;border: 1px solid #000 !important;  border-left: none !important;background:#dfe5ec !important;"></th>
                   <td style="border-top: 1px solid #000 !important;"></td>
                <td class="text-right" style="border-top: 1px solid #000 !important;">{{ number_format($grand_qty) }}</td>
                <td class="text-right" style="border-top: 1px solid #000 !important;">{{ number_format($grand_foc) }}</td>
                <td class="text-right" style="border-top: 1px solid #000 !important;">{{ number_format($grand_avl) }}</td>
                <td class="text-right" style="border-top: 1px solid #000 !important;">{{ number_format($grand_sample) }}</td>
                <td class="text-right" style="border-top: 1px solid #000 !important;">{{ number_format($grand_amount, 2) }}</td>
                <td style="border-top: 1px solid #000 !important;"></td>
            </tr>

        </tbody>
    </table>



@else
    <div class="alert alert-danger text-center">
        No Record Found
    </div>
@endif