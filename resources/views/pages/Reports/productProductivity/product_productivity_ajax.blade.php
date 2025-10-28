
<?php
    use App\Models\Shop;
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
?>
<div class="table-responsive">
<table id="dataTable" class="table table-bordered">
    <thead>
  <tr class="text-center">
   <th>S.NO</th>
   <th>Product Name </th>
   <th>Product Flavour </th>
   <th>UOM </th>
   <th>Distributor Name</th>
   <th>TSO Name</th>
   <th>Total Shop</th>
   <th>Sales Shop</th>
   <th>Remaing Shop</th>



   {{-- <th>	%age </th> --}}
    </tr>
</thead>
<tbody>
    @php
        $total = 0;
        $uniqueDays = [];

        $period = new DatePeriod(
            new DateTime($from),
            new DateInterval('P1D'),  // Interval of 1 day
            (new DateTime($to))->modify('+1 day')  // Inclusive of end date
        );

        foreach ($period as $new_date) {
            $dayName = $new_date->format('l');  // Get day name
            if (!in_array($dayName, $uniqueDays)) {
                $uniqueDays[] = $dayName;  // Add only unique day names
            }
        }

    @endphp
    @foreach($data as $key => $row)
        @php
            $today_shop = Shop::status()
                // ->whereIn('route_id', $route)
                // ->where(['distributor_id' => $tso['distributor_id'] , 'tso_id' => $tso['id']])
                ->where(['distributor_id' => $row->distributor_id , 'tso_id' => $row->tso_id])
                ->whereHas('Route', function ($query) use ($uniqueDays ) {
                    $query->whereHas('RouteDay', function ($subQuery) use ($uniqueDays) {
                        $subQuery->whereIn('day', $uniqueDays);
                    });
                })->get()->count();
        @endphp
        <tr>
            <td>{{ ++$key }}</td>
            <td>{{ $master->get_product_name_by_id($row->product_id) }}</td>
            <td>{{ $master->get_flavour_name($row->flavour_id) }}</td>
            <td>{{ $master->uom_name($row->sale_type) }}</td>
            <td>{{ $master->get_distributor_name($row->distributor_id) }}</td>
            <td>{{ $master->get_tso_name($row->tso_id) }}</td>
            <td>{{$today_shop}}</td>
            <td>{{ $row->distinct_shop_count }}</td>
            <td>{{$today_shop - $row->distinct_shop_count}}</td>

        </tr>
    @endforeach

</tbody>
</table>
</div>
