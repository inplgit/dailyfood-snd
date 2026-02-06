@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
    use Carbon\Carbon;
    use App\Models\Route;
    use App\Models\Shop;
    use App\Models\User;
    use App\Models\ShopVisit;
@endphp

<style>
    .card-head h4 {
        font-size: 18px !important;
        font-weight: bold !important;
        color: #000;
        line-height: normal;
        border: none !important;
    }

    .card-head p {
        font-size: 14px !important;
        color: #000;
        line-height: normal;
        padding: 0px 0px 16px 0px;
        border: none !important;
    }

    .names-para {
        display: flex;
        justify-content: left;
        gap: 20px;
    }

    .names-para2 {
        display: flex;
        gap: 20px;
        justify-content: right;
    }

    .table-bordered>thead>tr>th,
    .table-bordered>tbody>tr>th,
    .table-bordered>tfoot>tr>th {
        vertical-align: bottom;
        border-bottom: 2px solid #ddd;
        background: #dfe5ec;
        border: none !important;
        leading-trim: both;
        font-size: 11px !important;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        text-align: left;
        padding: 5px 5px;
    }

    .table-bordered>thead>tr>td,
    .table-bordered>tbody>tr>td,
    .table-bordered>tfoot>tr>td {
        padding: 5px 5px;
        vertical-align: middle;
        font-size: 11px;
        color: #000;
        font-style: normal;
        font-weight: 500 !important;
        line-height: normal;
        border-left: none !important;
        border-right: none;
        border-bottom: 1px solid #000 !important;
        text-align: left;
    }

    @media print {
        table {
            min-width: 1600px;
            table-layout: auto !important;
        }

        body {
            zoom: 80%;
        }
    }
</style>
<form id="list_data" method="get" action="{{ route('sale.index') }}"></form>
<div class="row col-12" id="table-bordered">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-head">
                <div class="row">
                    <!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h4>Order Booker Productive Status Report</h4>
                        <p>Karachi-A-UA Enterprises - KHI - KHI -01</p>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-left">
                        <div class="names-para">
                            <p>Principal</p>
                            <p>Iftekhar Ahmed & Co</p>
                        </div>
                    </div> -->
                    @php
                        $fromDate = $date;
                        $toDate = $to;
                        $formattedDate = Carbon::parse($date)->format('d-M-Y');
                        $formattedEndDate = Carbon::parse($to)->format('d-M-Y');
                    @endphp
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
                        <div class="names-para2">
                            <p><strong>From</strong></p>
                            <p>{{ $formattedDate }}</p>
                            <p><strong>To</strong></p>
                            <p>{{ $formattedEndDate }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive print">
                <div style="width: 100%;">
                    <table id="myTable1" class="table table-bordered" style="min-width:1600px;">
                        <thead>
                            <tr>
                                <th>Sr. #</th>
                                <th>Outlet Name</th>
                                <th style="width: 12%; white-space: nowrap;">Sync Date Time</th>
                                <th style="width: 12%; white-space: nowrap;">No Order Reason</th>
                                <th style="width: 12%; white-space: nowrap;" class="text-center">Booked CTN-Units</th>
                                <th style="width: 12%; white-space: nowrap;" class="text-center">MTD CTN-Units</th>
                                <th style="width: 12%; white-space: nowrap;" class="text-center">Check in Time</th>
                                <th style="width: 12%; white-space: nowrap;" class="text-center">CheckOut Time</th>
                                <th style="width: 12%; white-space: nowrap;" class="text-center">Time Spent</th>
                                <th style="width: 12%; white-space: nowrap;" class="text-center">Outlet Latitude</th>
                                <th style="width: 12%; white-space: nowrap;" class="text-center">Outlet Longitude</th>
                                <th style="width: 12%; white-space: nowrap;" class="text-center">Visit Latitude</th>
                                <th style="width: 12%; white-space: nowrap;" class="text-center">Visit Longitude</th>
                                <th style="width: 14%; white-space: nowrap;" class="text-center">Dist From Original Loc (m)</th>
                            </tr>
                        </thead>
                        <tbody id="data">
                            @foreach ($d_data as $distributor_name => $tso_data)
                                <tr>
                                    <th colspan="14" style="background:#dfe5ec; font-size:16px; font-weight:bold;">
                                        {{ $distributor_name }}
                                    </th>
                                </tr>
                                @foreach ($tso_data as $tso_name => $routes)
                                    <tr>
                                        <th colspan="4"
                                            style="background:transparent;border-bottom:3px solid #000 !important;font-size:15px !important;color:#000;font-weight:700;">
                                            {{ $tso_name }}</th>
                                        <th colspan="5"
                                            style="background:transparent;border-bottom:3px solid #000 !important;font-size:15px !important;color:#000;font-weight:700;">
                                            Total Distance in KM: 44.92</th>
                                        <th colspan="3"
                                            style="background:transparent;border-bottom:3px solid #000 !important;font-size:15px !important;color:#000;font-weight:700;">
                                            10-72</th>
                                        <th colspan="2"
                                            style="background:transparent;border-bottom:3px solid #000 !important;font-size:15px !important;color:#000;font-weight:700;">
                                            11-120</th>
                                    </tr>

                                    @php
                                        $count = 0;
                                        //   $endcount = 0 ;
                                    @endphp
                                    @foreach ($routes as $route_name => $shops)
                                        @php
                                            // dd($shops);
                                            // if($count == 0)
                                            // {
                                            //     $endcount = count($shops) - 1;
                                            // }

                                            $date = $date;
                                            $timestamp = strtotime($date);
                                            $day = date('l', $timestamp);

                                            $today_day = Carbon::parse($date)->format('l');

                                            $distributor_id = $shops[$count]['distributor_id'];
                                            $tso_id = $shops[$count]['tso_id'];
                                            // $today_route_ids = Route::where('distributor_id', $distributor_id)
                                            //     ->where('tso_id', $tso_id)
                                            //     ->where('id', $shops[$count]['route_id'])
                                            //     ->where('status', 1)
                                            //     ->where('day', $today_day)
                                            //     ->pluck('id');

                                            // $today_routes_count = Shop::status()
                                            //     ->whereIn('route_id', $today_route_ids)
                                            //     ->where('tso_id', $tso_id)
                                            //     ->where('distributor_id', $distributor_id)
                                            //     ->count();

                                            // $sales_count = DB::table('sale_orders')
                                            //     ->join('shops', 'shops.id', '=', 'sale_orders.shop_id')
                                            //     ->whereBetween('sale_orders.dc_date', [$fromDate, $toDate])
                                            //     ->where('sale_orders.tso_id', $tso_id)
                                            //     ->where('sale_orders.distributor_id', $distributor_id)
                                            //     ->where('sale_orders.status', 1)
                                            //     ->where('shops.route_id', $shops[$count]['route_id']);

                                            $total_visited = ShopVisit::join('shops', 'shops.id', '=', 'shop_visits.shop_id')
                                                ->where('shop_visits.user_id', $shops[$count]['user_id'])
                                                ->whereBetween('shop_visits.visit_date', [$fromDate, $toDate])
                                                ->where('shops.route_id', $shops[$count]['route_id']) // <-- ROUTE FILTER
                                                ->count('shop_visits.id');
                                            // $count++;
                                            // $total_order_shop = $sales_count->count() ?? 0;
                                            $total_order_shop = count($shops) ?? 0;

                                            $formattedDate = Carbon::parse($date)->format('d-M-Y h:i A');

                                            $user = User::find($shops[$count]['user_id']);

                                            // $total_order_shop = $user
                                            //     ->salesOrder()
                                            //     ->where('sale_orders.status', 1) // Specify table for status
                                            //     ->whereDate('sale_orders.created_at', $date) // Specify table for created_at
                                            //     ->where('sale_orders.tso_id', $tso_id) // Specify table for tso_id
                                            //     ->join('shops', 'sale_orders.shop_id', '=', 'shops.id')
                                            //     ->whereIn('shops.route_id', $today_route_ids)
                                            //     ->groupBy('sale_orders.shop_id')
                                            //     ->get()
                                            //     ->count();

                                        @endphp


                                        <tr>
                                            <th colspan="4"
                                                style=" background:transparent;border-bottom:3px solid #000 !important;font-size:15px !important;color:#000;font-weight:700;">
                                                {{ $route_name }} </th>
                                            <td colspan="3" style="border-bottom:3px solid #000 !important;">
                                                Total Outlets: {{ $total_order_shop + $total_visited ?? 0 }}
                                            </td>
                                            <td colspan="4" style="border-bottom:3px solid #000 !important;">
                                                Productive Outlets: {{ $total_order_shop ?? 0 }}
                                            </td>
                                            <td colspan="3" style="border-bottom:3px solid #000 !important;">
                                                Non Productive Outlets:
                                                {{ $total_visited }}
                                                {{-- {{ max(0, ($today_routes_count ?? 0) - ($total_order_shop ?? 0)) }} --}}
                                            </td>

                                        </tr>


                                        <tr>
                                            <th colspan="1" style="background:transparent; border:none !important;">
                                            </th>
                                            <th colspan="1" style="background:transparent; border:none !important;">
                                            </th>
                                            <th colspan="7"
                                                style="background:transparent;border-bottom:3px solid #000 !important;font-size:15px !important;color:#000;font-weight:700;">
                                                ({{ $formattedDate }} ~ {{ $formattedEndDate }}) / {{ $today_day }}
                                            </th>
                                            <th colspan="2"
                                                style="background:transparent;font-size:15px !important;color:#000;font-weight:700;">
                                            </th>
                                            <th colspan="4"
                                                style="background:transparent;font-size:15px !important;color:#000;font-weight:700;">
                                                Total Hours: 0 hours 0 minutes</th>
                                        </tr>

                                        @foreach ($shops as $key => $shop)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td style="width: 12%; white-space: nowrap;">
                                                    {{ $shop['shop_name'] ?? '-' }}
                                                    ({{ $shop['shop_id'] ?? '-' }})
                                                </td>
                                                <td style="width: 12%; white-space: nowrap;">
                                                    {{ $shop['sync_date_time'] ?? '-' }}</td>
                                                <td style="width: 12%; white-space: nowrap;">
                                                    {{ $shop['remark'] ?? '-' }}</td>
                                                <td style="width: 12%; white-space: nowrap;" class="text-center">
                                                    @if ($shop['remark'] == null)
                                                        -
                                                    @else
                                                        {{ $shop['total_qty'] }}
                                                    @endif

                                                </td>
                                                <td style="width: 12%; white-space: nowrap;" class="text-center">
                                                    {{ $shop['total_qty'] }}</td>
                                                <td style="width: 12%; white-space: nowrap;" class="text-center">
                                                    {{ $shop['check_in'] ?? '-' }}</td>
                                                <td style="width: 12%; white-space: nowrap;" class="text-center">
                                                    {{ $shop['check_out'] ?? '-' }}</td>
                                                <td style="width: 12%; white-space: nowrap;" class="text-center">
                                                    {{-- {{ $shop['total_time_spent'] ?? '-' }} --}}
                                                    @php
                                                        $secs = isset($shop['diff_seconds']) ? intval($shop['diff_seconds']) : null;
                                                    @endphp

                                                    @if (is_null($secs) || $secs === 0)
                                                        -
                                                    @else
                                                        @php
                                                            $minutes = floor($secs / 60);
                                                            $seconds = $secs % 60;
                                                        @endphp
                                                        {{ $minutes }} minutes {{ $seconds }} seconds
                                                    @endif

                                                    {{-- @php
                                                        if (!empty($shop['check_in']) && !empty($shop['check_out'])) {
                                                            $checkIn = \Carbon\Carbon::parse($shop['check_in']);
                                                            $checkOut = \Carbon\Carbon::parse($shop['check_out']);
                                                            $difference = $checkIn->diff($checkOut);
                                                            echo $difference->format('%i minutes %s seconds');
                                                        } else {
                                                            echo '-';
                                                        }
                                                    @endphp --}}
                                                </td>

                                                <td style="width: 12%; white-space: nowrap;" class="text-center">
                                                    {{ $shop['Outlet_Latitude'] ?? '-' }}</td>
                                                <td style="width: 12%; white-space: nowrap;" class="text-center">
                                                    {{ $shop['Outlet_Longitude'] ?? '-' }}</td>

                                                <td style="width: 12%; white-space: nowrap;" class="text-center">
                                                    {{ $shop['Visit_Latitude'] ?? '-' }}</td>
                                                <td style="width: 12%; white-space: nowrap;" class="text-center">
                                                    {{ $shop['Visit_Longitude'] ?? '-' }}</td>
                                                <td style="width: 12%; white-space: nowrap;" class="text-center">-</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach

                        </tbody>


                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function downloadCSV() {
        let table = document.getElementById("myTable1");
        let rows = table.querySelectorAll("tr");
        let csv = [];

        rows.forEach(row => {
            let cols = row.querySelectorAll("th, td");
            let rowData = [];
            cols.forEach(col => {
                // Escape double quotes and commas
                let text = col.innerText.replace(/"/g, '""');
                rowData.push('"' + text + '"');
            });
            csv.push(rowData.join(","));
        });

        // Convert to CSV string
        let csvString = csv.join("\n");

        // Create Blob and download
        let blob = new Blob([csvString], {
            type: "text/csv;charset=utf-8;"
        });
        let link = document.createElement("a");
        let url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "Order_Booker_Productive_Status_Report.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<script>
    document.getElementById("printBtn").addEventListener("click", function() {
        const tableHtml = document.getElementById("table-bordered").innerHTML;

        const printWindow = window.open("", "_blank", "width=1920,height=1080");
        printWindow.document.write(`
        <html>
        <head>
            <title>Order Booker Productive Status Report</title>
            <style>
                @page {
                    size: A3 landscape;
                    margin: 10mm;
                }

                * {
                    box-sizing: border-box;
                }

                body {
                    font-family: Arial, sans-serif;
                    font-size: 10px;
                    margin: 0;
                    padding: 10px;
                    overflow-x: auto;
                }

                table {
                    width: 100%;
                    min-width: 1600px;
                    border-collapse: collapse;
                    table-layout: auto;
                }

                th, td {
                    border: 1px solid #000;
                    padding: 4px;
                    font-size: 10px;
                    text-align: center;
                    white-space: normal;
                }

                th {
                    background: #dfe5ec;
                }

                .text-center {
                    text-align: center;
                }

                @media print {
                    html, body {
                        width: 100%;
                        overflow: visible !important;
                        zoom: 80%;
                    }
                }
            </style>
        </head>
        <body>
            ${tableHtml}
            <script>
                window.onload = function() {
                    window.print();
                    window.close();
                };
            <\/script>
        </body>
        </html>
    `);
        printWindow.document.close();
    });
</script>
