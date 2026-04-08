<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Report</title>
    <style>
        @page{size:A4;margin:8mm 8mm 8mm 8mm !important;}
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.4;}
        h1, h2, h3 { color: #0056b3; margin-top: 20px;}
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px;}
        .table th, .table td { border: 1px solid #ddd; padding: 6px; text-align: left;}
        .table th { background-color: #f2f2f2; font-weight: bold;}
        .text-right { text-align: right;}
        .text-center { text-align: center;}
        .page-break1{page-break-before:always;}
        .page-break-after{page-break-after:always;}
        .table1{page-break-inside:avoid;}
        .tr1{page-break-inside:avoid;page-break-after:auto;}
        .header-box h1{text-align:center;}
        .header-box{display:flex;align-items:center;justify-content:space-between;}
        .header-left{display:flex;align-items:center;gap:10px;}
        .header-center{text-align:center;}
        .rela{position:relative;}
        .abus-footer{position:absolute;bottom:0;width:100%;}
        .footer{color:#000000;font-weight:bold;text-align:right;font-size: 9px}
        p{margin:0 !important;}
    </style>
</head>
<body>

    <div class="rela">
        <table width="100%">
             <tr>
                 <!-- Left (Logo) -->
                 <td style="width:32%; text-align:left;">
                     <img src="{{ public_path('assets/images/logo.png') }}" style="height:50px;">
                 </td>
     
                 <!-- Center (Heading) -->
                 <td style="width:36%; text-align:center;">
                     <h1 style="margin:0; white-space:nowrap;">Daily Summary Report</h1>
                 
                 </td>
     
                 <!-- Right (Empty space for balance) -->
                 <td style="width:32%;text-align:right;"> <p style="margin:0;">Date: {{ $dateStr }}</p></td>
             </tr>
         </table>
         {{-- <div class="header-box"> 
             <div class="header-left">
                 <img class="logo_m" src="{{ public_path('assets/images/logo.png') }}" style="height:50px;">
             </div>
     
             <div class="header-center">
                 <h1>Daily Summary Report</h1>
                 <p>Date: {{ $dateStr }}</p>
             </div> --}}
         
         
             {{-- <div class="header-left">
                 <img class="logo_m" src="{{ public_path('assets/images/logo.png') }}" style="height:50px;">
             </div>
     
             <div class="header-center">
                 <h1>Daily Summary Report</h1>
                 <p>Date: {{ $dateStr }}</p>
             </div> --}}
             {{-- 
         </div> --}}
         <br>
         @foreach($sections as $index => $data)
             <div class="city-block" @if(!$loop->first) style="page-break-before: always;" @endif>
                 
                 <div style="background-color: #f2f2f2; padding: 10px; border: 1px solid #ddd; margin-bottom: 20px;">
                     <h2 style="color: #0056b3; margin: 0; text-align: center;">{{ $data['cityName'] }}</h2>
                 </div>
     
                 @if($config->show_tso_attendance)
                 <div class="mb-4">
                     <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Daily Attendance (Designation-wise)</h3>
                     
                     @foreach($data['attendanceDetails'] as $designation => $staff)
                         <h4 style="color: #444; margin-top: 15px;">{{ $designation }}</h4>
                         <table class="table">
                             <thead>
                                 <tr>
                                     <th style="width: 40px;">#</th>
                                     <th>Name</th>
                                     <th class="text-center">Check-in</th>
                                     <th class="text-center">Check-out</th>
                                     <th class="text-center">Status</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @foreach($staff as $idx => $person)
                                 <tr>
                                     <td>{{ $idx + 1 }}</td>
                                     <td>{{ $person->name }}</td>
                                     <td class="text-center">{{ $person->check_in ? date('h:i A', strtotime($person->check_in)) : '-' }}</td>
                                     <td class="text-center">{{ $person->check_out ? date('h:i A', strtotime($person->check_out)) : '-' }}</td>
                                     <td class="text-center">
                                         @if($person->check_in)
                                             <span style="color: green; font-weight: bold;">P</span>
                                         @else
                                             <span style="color: red; font-weight: bold;">A</span>
                                         @endif
                                     </td>
                                 </tr>
                                 @endforeach
                             </tbody>
                         </table>
                     @endforeach
                 </div>
                 @endif
     
                 @if($config->show_distributor_sales)
                 <div class="mb-4">
                     @php
                         $totalAttendance = collect($data['attendanceDetails'])->flatten()->count();
                     @endphp
                     <div class="{{ $totalAttendance >= 15 ? 'page-break1' : '' }}">
                         <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Distributor Sales</h3>
                     </div>
                     <table class="table {{ $totalAttendance <= 15 ? 'table1' : '' }}">
                         <thead>
                            
                             <tr class="tr1">
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
                         @if(count($data['distributorSales']) > 0)
                         <tfoot>
                             <tr style="background-color: #eee; font-weight: bold;">
                                 <td>TOTAL</td>
                                 <td class="text-right">{{ number_format($data['distributorTotals']['orders']) }}</td>
                                 <td class="text-right">{{ number_format($data['distributorTotals']['qty']) }}</td>
                                 <td class="text-right">{{ number_format($data['distributorTotals']['amount'], 2) }}</td>
                             </tr>
                         </tfoot>
                         @endif
                     </table>
                 </div>
                 @endif
     
                 @if($config->show_product_sales)
                 <div class="mb-4">
                     {{-- <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Product Sales (Qty)</h3> --}}
                     <table class="table">
                         <thead>
                             <tr>
                                 <th colspan="2">
     
                                     <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Product Sales (Qty)</h3>
                                 </th>
                             </tr>
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
                         @if(count($data['productSales']) > 0)
                         <tfoot>
                             <tr style="background-color: #eee; font-weight: bold;">
                                 <td>TOTAL</td>
                                 <td class="text-right">{{ number_format($data['productTotals']['qty']) }}</td>
                             </tr>
                         </tfoot>
                         @endif
                     </table>
                 </div>
                 @endif
     
                 @if($config->show_top_bottom_tso)
                 <div class="mb-4">
                     {{-- <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Top 10 TSOs (By Sales)</h3> --}}
                     <table class="table page-break">
                         <thead>
                             <tr>
                                 <th colspan="2">
     
                                     <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Top 10 TSOs (By Sales)</h3>
                                 </th>
                             </tr>
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
                         @if(count($data['topTsos']) > 0)
                         <tfoot>
                             <tr style="background-color: #eee; font-weight: bold;">
                                 <td>TOTAL</td>
                                 <td class="text-right">{{ number_format($data['topTsos']->sum('total_amount'), 2) }}</td>
                             </tr>
                         </tfoot>
                         @endif
                     </table>
                     
                     @if(count($data['bottomTsos']) > 0)
                     {{-- <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Bottom 10 TSOs</h3> --}}
                     <table class="table">
                         <thead>
                             <tr>
                                 <th colspan="2">
                                     <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Bottom 10 TSOs</h3>
                                 </th>
                             </tr>
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
                         <tfoot>
                             <tr style="background-color: #eee; font-weight: bold;">
                                 <td>TOTAL</td>
                                 <td class="text-right">{{ number_format($data['bottomTsos']->sum('total_amount'), 2) }}</td>
                             </tr>
                         </tfoot>
                     </table>
                     @endif
                 </div>
                 @endif
     
                 @if($config->show_top_bottom_shop)
                 <div class="mb-4">
                     {{-- <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Top 10 Shops</h3> --}}
                     <table class="table">
                         <thead>
                             <tr>
                                 <th colspan="2">
     
                                     <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Top 10 Shops</h3>
                                 </th>
                             </tr>
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
                         @if(count($data['topShops']) > 0)
                         <tfoot>
                             <tr style="background-color: #eee; font-weight: bold;">
                                 <td>TOTAL</td>
                                 <td class="text-right">{{ number_format($data['topShops']->sum('total_amount'), 2) }}</td>
                             </tr>
                         </tfoot>
                         @endif
                     </table>
     
                     @if(count($data['bottomShops']) > 0)
                     {{-- <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Bottom 10 Shops</h3> --}}
                     <table class="table">
                         <thead>
                             <tr>
                                 <th colspan="2">
     
                                     <h3 style="border-bottom: 2px solid #0056b3; padding-bottom: 5px;">Bottom 10 Shops</h3>
                                 </th>
                             </tr>
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
                         <tfoot>
                             <tr style="background-color: #eee; font-weight: bold;">
                                 <td>TOTAL</td>
                                 <td class="text-right">{{ number_format($data['bottomShops']->sum('total_amount'), 2) }}</td>
                             </tr>
                         </tfoot>
                     </table>
                     @endif
                 </div>
                 @endif
             </div>
         @endforeach
     
         @if($config->show_overall_sales)
         <div class="mb-4" style="border-top: 2px solid #333; padding-top: 10px;">
             <table class="table">
                 <thead>
                     <tr><th colspan="3"><h2 class="text-center">GRAND TOTAL SUMMARY</h2></th></tr>
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
    </div>
    <div class="abus-footer " style="position: absolute; bottom: 0; width: 100%;">
        <div class="footer">
            <p>Powered By <strong><img style="height: 8px;margin-right: -6px;" src="{{ url('/public/assets/images/inon.png') }}" alt="Innovative Network (Pvt.) Ltd"> Innovative Network (Pvt.) Ltd.</strong></p>
        </div>
    </div>

</body>
</html>
