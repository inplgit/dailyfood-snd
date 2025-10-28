
<?php
use App\Models\ShopVisit;
use App\Models\SaleOrder;
use App\Models\Route;
use App\Models\Shop;
?>
<div class="table-responsive">
<table id="dataTable" class="table table-bordered">
    <thead>
  <tr class="text-center">
   <th>S.NO</th>
   <th>Invoice No</th>
   <th>Invoice Date</th>
   <th>CIty </th>
   <th>Distributor </th>
   <th>TSO </th>
  <th>	Route</th>
    <th>Shop</th>
 <th>Item</th>
      <th>Execution</th>
 <th>QTY </th>
 <th>Amount </th>



   {{-- <th>	%age </th> --}}
    </tr>
</thead>
<tbody>
@php
    $total_qty = 0;
    $total_amount = 0;
 @endphp
    @foreach($data as $key => $row)
        @php
        $total_qty += $row->qty;
        $total_amount += $row->total;
        @endphp
        <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $row->invoice_no }}</td>
            <td>{{ $row->dc_date }}</td>
            <td>{{ $row->city }}</td>
            <td>{{ $row->distributor_name }}</td>
            <td>{{ $row->tso }}</td>
            <td>{{ $row->route_name }}</td>
            <td>{{ $row->shop_name }}</td>
            <td>{{ $row->product_name }}</td>
            <td>{{ ($row->excecution) ? 'Execute' : 'Not Execute' }}</td>
            <td>{{ $row->qty }}</td>
            <td>{{ $row->total }}</td>


        </tr>
    @endforeach
<tr style="background-color: lightgray;font-size: large;font-weight: bold" >
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>{{ number_format($total_qty,2) }}</td>
    <td>{{ number_format($total_amount,2) }}</td>
</tr>
</tbody>
</table>
</div>
