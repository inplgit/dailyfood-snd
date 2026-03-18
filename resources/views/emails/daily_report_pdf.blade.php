<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        h1, h2, h3 { color: #0056b3; margin-top: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        .table th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 2rem; }
        .header-box { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0056b3; padding-bottom: 10px;}
        .city-block { border: 1px solid #0056b3; padding: 15px; margin-bottom: 30px; border-radius: 5px; }
        .city-header { background-color: #0056b3; color: white; padding: 8px; margin: -15px -15px 15px -15px; border-radius: 4px 4px 0 0; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="header-box">
        <h1>Daily Summary Report</h1>
        <p>Date: {{ $dateStr }}</p>
    </div>

    @foreach($sections as $index => $data)
        <div class="city-block {{ !$loop->last ? 'page-break' : '' }}">
            <div class="city-header">
                <h2 style="color: white; margin: 0;">{{ $data['cityName'] }}</h2>
            </div>

            @if($config->show_tso_attendance)
            <div class="mb-4">
                <h3>TSO Attendance</h3>
                <table class="table" style="width: 50%;">
                    <tr>
                        <th>Total TSOs</th>
                        <td>{{ $data['tsoData']['total'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>Present</th>
                        <td>{{ $data['tsoData']['present_count'] ?? 0 }}</td>
                    </tr>
                    <tr>
                        <th>Absent</th>
                        <td>{{ $data['tsoData']['absent_count'] ?? 0 }}</td>
                    </tr>
                </table>

                @if(!empty($data['tsoData']['absent_list']) && count($data['tsoData']['absent_list']) > 0)
                <h4>Absent TSOs</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ORDER BOOKER Name</th>
                            <th>Distributor Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['tsoData']['absent_list'] as $idx => $absent)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $absent->name }}</td>
                            <td>{{ $absent->distributor_name ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
            @endif

            @if($config->show_distributor_sales)
            <div class="mb-4">
                <h3>Distributor Sales</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Distributor Name</th>
                            <th class="text-right">Orders</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['distributorSales'] as $dist)
                        <tr>
                            <td>{{ $dist->distributor_name }}</td>
                            <td class="text-right">{{ number_format($dist->total_orders) }}</td>
                            <td class="text-right">{{ number_format($dist->total_qty) }}</td>
                            <td class="text-right">{{ number_format($dist->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">No sales data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif

            @if($config->show_product_sales)
            <div class="mb-4">
                <h3>Product Sales (Qty)</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th class="text-right">Quantity Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['productSales'] as $prod)
                        <tr>
                            <td>{{ $prod->product_name }}</td>
                            <td class="text-right">{{ number_format($prod->total_qty) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center">No product sales found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif

            @if($config->show_top_bottom_tso)
            <div class="mb-4">
                <h3>Top 10 TSOs (By Sales)</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>TSO Name</th>
                            <th class="text-right">Sales Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['topTsos'] as $tso)
                        <tr>
                            <td>{{ $tso->tso_name }}</td>
                            <td class="text-right">{{ number_format($tso->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if(count($data['bottomTsos']) > 0)
                <h3>Bottom 10 TSOs</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>TSO Name</th>
                            <th class="text-right">Sales Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['bottomTsos'] as $tso)
                        <tr>
                            <td>{{ $tso->tso_name }}</td>
                            <td class="text-right">{{ number_format($tso->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
            @endif

            @if($config->show_top_bottom_shop)
            <div class="mb-4">
                <h3>Top 10 Shops</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Shop Name</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['topShops'] as $shop)
                        <tr>
                            <td>{{ $shop->shop_name }} ({{ $shop->shop_code }})</td>
                            <td class="text-right">{{ number_format($shop->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @if(count($data['bottomShops']) > 0)
                <h3>Bottom 10 Shops</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Shop Name</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['bottomShops'] as $shop)
                        <tr>
                            <td>{{ $shop->shop_name }} ({{ $shop->shop_code }})</td>
                            <td class="text-right">{{ number_format($shop->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
            @endif
        </div>
    @endforeach

    @if($config->show_overall_sales)
    <div class="mb-4" style="border-top: 2px solid #333; padding-top: 10px;">
        <h2 class="text-center">GRAND TOTAL SUMMARY (ALL SELECTED CITIES)</h2>
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center">Total Orders</th>
                    <th class="text-center">Total Quantity</th>
                    <th class="text-center">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr style="font-size: 14px; font-weight: bold;">
                    <td class="text-center">{{ number_format($overallTotals['total_orders']) }}</td>
                    <td class="text-center">{{ number_format($overallTotals['total_qty']) }}</td>
                    <td class="text-center">{{ number_format($overallTotals['total_amount'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

</body>
</html>
