
<?php
use App\Models\ShopVisit;
use App\Models\SaleOrder;
use App\Models\Route;
use App\Models\Shop;
use App\Models\UsersLocation;
use App\Helpers\MasterFormsHelper;

$master = new MasterFormsHelper();
$user_allocate  = $master->get_assign_user()->toArray();

?>
<div class="container-fluid">
    <br/>
    <div class="row">
        <div class="col-sm-12 text-center">
            <h3>TSO Payments Recovery Report</h3>
            @if($from_date != '' && $to_date != '')
            <h5>{{date('d-M-Y',strtotime($from_date))}} to {{date('d-M-Y',strtotime($to_date))}}</h5>
            @endif
        </div>          
    </div>    
<div class="table-responsive">
<table id="dataTable" class="table table-bordered">
    <thead>
        <tr>
   <th>S.No</th>
   <th>Voucher No</th>
   <th>Date	</th>
   <th>Shop </th>
   <th>Route</th>
   <th>	TSO</th>
   <th>Distributor</th>
   	<th>Total Amount </th>
  
    </tr>
</thead>
<tbody>
    @php
    $i=1;
    $total=0;
    @endphp
    @foreach($receipt_vouchers as $val)
    
    <tr>
        <td title="{{ $val->id }}">{{$i++}}</td>
        <td>{{$val->id}}</td>
        <td>@if($val->issue_date!=''){{ date('d-m-Y',strtotime($val->issue_date))}} @endif </td>
        <td>{{$val->shop->company_name }}</td>
        <td>{{$val->route->route_name }}</td>
        <td>{{$val->tso->name }}</td>
        <td>{{$val->distributor->distributor_name }}</td>
        <td>{{ number_format($val->amount,2) }}</td>
        @php 
        $total+=$val->amount;
        @endphp

    </tr>
    @endforeach
  

    <tr style="background-color: darkgray;font-weight: bold" class="bold">
        <td>Total</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ number_format($total ,2) }}</td>
    </tr>
</tbody>
</table>
</div>
</div>
