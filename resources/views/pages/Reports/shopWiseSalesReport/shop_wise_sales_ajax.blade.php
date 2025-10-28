
<?php
    use App\Models\ShopVisit;
    use App\Models\SaleOrder;
    use App\Models\Route;
    use App\Models\Shop;
?>
<div class="table-responsive printBody">
    @if(isset($from) && isset($to))
        @php
            // $cityName = $city ? \App\Models\City::find($city)->name : 'All';
            $distributorName = $distributor_id ? \App\Models\Distributor::find($distributor_id)->distributor_name : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp

        <div class="dates-info-head text-center" >
            <p>
                <strong>Laziza International</strong><br>
                <strong>Shop Wise Sales Report</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                {{-- <b>City:</b> {{ $cityName }} | --}}
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>
        </div>
    @endif
    <table class="table table-bordered filterTable">
        <thead>
            <tr class="text-center">
                <th>S.NO</th>
                <th>Shop Code</th>
                <th>Shop</th>
                <th>Route</th>
                <th>Order Booker</th>
                <th>Manager</th>
                <th>Distributor </th>
                <th>Qty (Units)</th>
                <th>Executed Amount W/O Disc</th>
                <th>Sales Value</th>
                <th>Sales Return</th>
                <th>Net Sales</th>
            </tr>
        </thead>
        <tbody>
        @php
            $total_qty = 0;
            $total_rate = 0;
            $total_sale_return = 0;
            $total_net_sale = 0;
        @endphp
            @foreach($data as $key => $row)
                @php
                    $returned_qty = \App\Helpers\MasterFormsHelper::get_returned_qty_by_sale_order_id($row->distributor_id, $row->tso_id, $row->shop_id, $from, $to);
                    $total_qty += $row->qty;
                    $total_rate += $row->rate;
                    $total_sale_return += $returned_qty ?? 0;
                    $total_net_sale += $row->rate ?? 0;
                @endphp
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $row->shop_code }}</td>
                    <td>{{ $row->shop_name }}</td>
                    <td>{{ $row->route_name }}</td>
                    <td>{{ $row->tso }}</td>
                    <td>{{ $row->manager ?? '' }}</td>
                    <td>{{ $row->distributor_name }}</td>
                    <td>{{ $row->qty }}</td>
                    <td>{{ $row->rate }}</td>
                    <td>0</td>
                    <td>{{ $returned_qty }}</td>
                    <td>{{ $row->rate }}</td>
                </tr>
            @endforeach
            <tfoot>
                <tr style="background-color: lightgray;font-size: large;font-weight: bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($total_qty,2) }}</td>
                    <td>{{ number_format($total_rate,2) }}</td>
                    <td></td>
                    <td>{{ number_format($total_sale_return,2) }}</td>
                    <td>{{ number_format($total_net_sale,2) }}</td>
                </tr>
            </tfoot>
        </tbody>
    </table>
</div>
