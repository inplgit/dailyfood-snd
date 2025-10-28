
<?php
use App\Models\ShopVisit;
use App\Models\SaleOrder;
use App\Models\Route;
use App\Models\Shop;
use App\Helpers\MasterFormsHelper;
$from = Request::get('from');
$to = Request::get('to');
?>
<div class="table-responsive">
<table id="dataTable" class="table table-bordered">
    <thead>
  <tr class="text-center">
   <th>S.NO</th>
   <th>SO NO</th>
   <th>SO DATE</th>
   <th>CIty </th>
   <th>Distributor </th>
   <th>TSO </th>
   <th>Shop</th>
   <th>	Route</th>
   <th>SO Amount </th>
   <th>view </th>

   {{-- <th>	%age </th> --}}
    </tr>
</thead>
<tbody>
@php $total = 0; @endphp
    @foreach($order_list as $key => $row)
        @php
            $sales_amount  = ($type==0) ? MasterFormsHelper::Order_list_total_amount(0,$row->id) :  MasterFormsHelper::Order_list_total_amount(1,$row->tso_id,$from,$to) ;
           $total += $sales_amount; @endphp
        <tr class="">
        <td>{{ ++$key }}</td>
        <td>{{ strToUpper($row->invoice_no) }}</td>
        <td>{{ date('d-m-Y',strtotime($row->dc_date)) }}</td>
        <td>{{ $row->city }}</td>
        <td>{{ $row->distributor_name }}</td>
        <td>{{ $row->tso }}</td>
        <td>{{ $row->shop_name }}</td>
        <td>{{ $row->route_name }}</td>
        <td>{{ number_format($sales_amount,2)??0 }}</td>


        <td>
                <a target="_blank" href="{{ route('sale.show',$row->id) }}" class="btn btn-primary btn-sm">View</a>
        </td>
        </tr>
    @endforeach
<tr style="background-color: lightgray;font-size: large;font-weight: bold" class="text-center">
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>{{ number_format($total,2) }}</td>
    <td></td>
</tr>
</tbody>
</table>
</div>
