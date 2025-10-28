

<style>
.table-bordered > thead > tr > th,.table-bordered > tbody > tr > th,.table-bordered > tfoot > tr > th{vertical-align:inherit !important;text-align:left !important;padding:5px 5px !important;font-size:11px !important;}
.userlittab > thead > tr > td,.userlittab > tbody > tr > td,.userlittab > tfoot > tr > td{vertical-align:inherit !important;text-align:left !important;padding:5px 5px !important;font-size:11px !important;}
.dates-info-head p{text-align:center;margin-bottom:20px;color:#000 !important;}
strong{font-weight:bold !important;color:#000!important;}
</style>

<div class="table-responsive printBody">
   @if(isset($from) && isset($to))
            @php
                $cityName = $city ? \App\Models\City::find($city)->name : 'All';
                $distributorName = $distributor_id ? \App\Models\Distributor::find($distributor_id)->distributor_name : 'All';
                $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
            @endphp

            <div class="dates-info-head" >
                <p>
                    <strong>Laziza International</strong><br>
                    <strong>Non Dispatch Order Report</strong><br>
                    <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                    <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                    <b>City:</b> {{ $cityName }} |
                    <b>Distributor:</b> {{ $distributorName }} |
                    <b>TSO:</b> {{ $tsoName }}
                </p>
            </div>
        @endif 

<table class="table table-bordered filterTable">
    <thead>
        <tr class="text-center">
            <th>S.NO</th>
            <th>Invoice No</th>
            <th>Invoice Date</th>
            <th>Shop</th>
            <th>Route</th>
            <th>Order Booker</th>
            <th>Manager</th>
            <th>Distributor</th>
            <th>Item Name</th>
            <th>Total Pcs</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $total_qty = 0;
            $total_amount = 0;
        @endphp

        @foreach($data as $key => $row)
            @php 
                $total_qty += $row->total_qty;
                $total_amount += $row->total_amount;
            @endphp
            <tr>
                <td>{{ ++$key }}</td>
                <td>{{ $row->invoice_no }}</td>
                <td>{{ $row->dc_date }}</td>
                <td>{{ $row->shop_name }}</td>
                <td>{{ $row->route_name }}</td>
                <td>{{ $row->tso }}</td>
                <td>{{ $row->manager_name }}</td>
                <td>{{ $row->distributor_name }}</td>
                <td>{{ $row->product ?? '' }}</td>
                <td>{{ number_format($row->total_qty, 2) }}</td>
                <td>{{ number_format($row->total_amount, 2) }}</td>
            </tr>
        @endforeach

        <tr style="background-color: lightgray; font-size: large; font-weight: bold">
            <td class="text-right">Total</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         
            <td></td>
            <td>{{ number_format($total_qty, 2) }}</td>
            <td>{{ number_format($total_amount, 2) }}</td>
        </tr>
    </tbody>
</table>


    </table>
</div>
