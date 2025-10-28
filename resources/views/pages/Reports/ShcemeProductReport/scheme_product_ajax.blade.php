
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
   <th>Date</th>
   <th>CIty </th>
   <th>Distributor </th>
   <th>TSO </th>
  <th>	Route</th>
 <th>Shop</th>
   <th>	Item</th>
   <th>Availability </th>



   {{-- <th>	%age </th> --}}
    </tr>
</thead>
<tbody>
@php $total = 0; @endphp
    @foreach($data as $key => $row)
        <tr>
            <td>{{ ++$key }}</td>
            <td>{{ date("d-m-Y", strtotime($row->dc_date)) }}</td>
            <td>{{ $row->city }}</td>
            <td>{{ $row->distributor_name }}</td>
            <td>{{ $row->tso }}</td>
            <td>{{ $row->route_name }}</td>
            <td>{{ $row->shop_name }}</td>
            <td>{{ $row->product_name }}</td>
            <td>{{ $row->offer_qty }}</td>

        </tr>
    @endforeach

</tbody>
</table>
</div>
