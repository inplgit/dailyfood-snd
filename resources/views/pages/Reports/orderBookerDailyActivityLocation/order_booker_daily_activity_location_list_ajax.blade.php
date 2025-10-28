<?php
    use App\Helpers\MasterFormsHelper;
    use App\Models\Distributor;
    use App\Models\TSO;
    use Illuminate\Support\Facades\DB;
    $master = new MasterFormsHelper();
?>

{{-- <div class="table-responsive printBody">
    @if (isset($to))
        @php
            $distributorName = $distributor_id
                ? Distributor::find($distributor_id)->distributor_name
                : 'All';
            $tsoName = $tso_id ? TSO::find($tso_id)->name : 'All';
        @endphp

        <div class="dates-info-head text-center mb-3">
            <p class="mb-0">
                <strong>Laziza International</strong><br>
                <strong>Order Booker Daily Activity Timestamp Report</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>
        </div>
    @endif

    <table class="table table-bordered filterTable">
        <thead>
            <tr class="text-center">
                 <th>S.No.</th>
                <th>Code</th>
                <th>Date</th>
                <th>Time</th>
                <th>Order Booker</th>
                <th>Manager</th>
                <th>Distributor</th>
                <th>Shop</th>
                <th>Type</th>
                <th>Units</th>
                <th>Remarks</th>
                <th>Map Name</th>
                <th>View Map</th>
            </tr>
        </thead>
        <tbody>
            @php $total_global_pcs = 0;
            $serial = 1; // Serial Number Counter
            
            @endphp

            @foreach ($data as $row)
                @php
                    // Fetch sale orders
                    $saleOrders = DB::table('sale_orders')
                        ->where('tso_id', $row->tso_id)
                        ->where('distributor_id', $row->distributor_id)
                        ->where('shop_id', $row->id)
                        ->whereBetween('dc_date', [$from, $to]);

                    $shopVisits = DB::table('shop_visits')
                        ->where('user_id', $row->user_id)
                        ->where('shop_id', $row->id)
                        ->whereBetween('visit_date', [$from, $to])
                        ->get();

                    $statuses = [
                        0 => '',
                        1 => 'Stock Available',
                        2 => 'No Sale',
                        3 => 'Owner Not Available',
                        4 => 'Shop Closed',
                    ];

                    $totalPcs = $saleOrders->sum('total_pcs') ?? 0;
                    $total_global_pcs += $totalPcs;

                    $unitRecord = $saleOrders->first();

                    $dateTime = $row->visit_created_at ?? ($unitRecord->created_at ?? null);
                    $date = $dateTime ? \Carbon\Carbon::parse($dateTime)->format('d-M-Y') : '-';
                    $time = $dateTime ? \Carbon\Carbon::parse($dateTime)->format('h:i:s A') : '-';
                @endphp

                @if ($unitRecord)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>{{ $row->shop_code }}</td>
                        <td>{{ $date }}</td>
                        <td>{{ $time }}</td>
                        <td>{{ $row->tso }}</td>
                        <td>{{ $row->manager_name }}</td>
                        <td>{{ $row->distributor_name }}</td>
                        <td>{{ $row->shop_name }}</td>
                        <td>Productive Shop</td>
                        <td>{{ $totalPcs }}</td>
                        <td></td>
                        <td>{{ $row->shop_map_name ?? '-' }}</td>
                        <td>
                            <a href="https://www.google.com/maps?q={{ $row->latitude }},{{ $row->longitude }}" target="_blank">
                                View Map
                            </a>
                        </td>
                    </tr>
                @endif

                @foreach ($shopVisits as $visit)
                    <tr>
                        <td>{{ $serial++ }}</td>
                        <td>{{ $row->shop_code }}</td>
                        <td>{{ $date }}</td>
                        <td>{{ $time }}</td>
                        <td>{{ $row->tso }}</td>
                        <td>{{ $row->manager_name }}</td>
                        <td>{{ $row->distributor_name }}</td>
                        <td>{{ $row->shop_name }}</td>
                        <td>Unproductive Shop</td>
                        <td>0</td>
                        <td>{{ $statuses[$visit->visit_reason_id ?? 0] ?? '' }}</td>
                        <td>{{ $row->shop_map_name ?? '-' }}</td>
                        <td>
                            <a href="https://www.google.com/maps?q={{ $row->latitude }},{{ $row->longitude }}" target="_blank">
                                View Map
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endforeach

            <tfoot class="fw-bold bg-light">
                <tr>
                    <td><strong>Total</strong></td>
                    <td colspan="8"></td>
                    <td><strong>{{ $total_global_pcs }}</strong></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            paging: false,
            order: [
                [1, 'asc'], // Date column
                [2, 'asc']  // Time column
            ],
            columnDefs: [
                { targets: [1], type: 'date' },
                { targets: [2], type: 'time' }
            ]
        });
    });
</script> --}}
<div class="table-responsive printBody">
    @if (isset($to))
        @php
            $distributorName = $distributor_id
                ? \App\Models\Distributor::find($distributor_id)->distributor_name
                : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp

        <div class="dates-info-head text-center mb-3">
            <p class="mb-0">
                <strong>Laziza International</strong><br>
                <strong>Order Booker Daily Activity Timestamp Report</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>
        </div>
    @endif

    <table class="table table-bordered filterTable">
        <thead>
            <tr class="text-center">
                <th>S.No.</th>
                <th>Code</th>
                <th>Date</th>
                <th>Time</th>
                <th>Order Booker</th>
                <th>Manager</th>
                <th>Distributor</th>
                <th>Shop</th>
                <th>Type</th>
                <th>Units</th>
                <th>Remarks</th>
                <th>Map Name</th>
                <th>View Map</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $serial = 1; 
                $total_global_pcs = 0;
                $statuses = [
                    0 => '',
                    1 => 'Stock Available',
                    2 => 'No Sale',
                    3 => 'Owner Not Available',
                    4 => 'Shop Closed',
                ];
            @endphp

        {{-- @dd($prepared) --}}
            @foreach ($prepared as $row)
                @php
                    $total_global_pcs += $row->total_pcs;
                    $dateTime = $row->visit_created_at ?? ($row->unit_record->created_at ?? null);
                    $fullDateTime = $dateTime ? \Carbon\Carbon::parse($dateTime)->format('Y-m-d\TH:i:s') : null;
                    $date = $dateTime ? \Carbon\Carbon::parse($dateTime)->format('d-M-Y') : '-';
                    $time = $dateTime ? \Carbon\Carbon::parse($dateTime)->format('h:i:s A') : '-';
                    $lat = $row->visit_latitude ?? $row->latitude ?? null;
                    $long = $row->visit_longitude ?? $row->longitude ?? null;
                    
                @endphp

                {{-- Productive --}}
                @if ($row->unit_record)
                    <tr data-datetime="{{ $fullDateTime }}">
                        <td></td>
                        <td>{{ $row->shop_code }}</td>
                        <td>{{ $date }}</td>
                        <td>{{ $time }}</td>
                        <td>{{ $row->tso }}</td>
                        <td>{{ $row->manager_name }}</td>
                        <td>{{ $row->distributor_name }}</td>
                        <td>{{ $row->shop_name }}</td>
                        <td>Productive Shop</td>
                        <td>{{ $row->total_pcs }}</td>
                        <td></td>
                        <td>{{ $row->shop_map_name ?? '-' }}</td>
                        <td>
                            @if ($row->unit_record?->usersLocation?->latitude)
                                <a href="https://www.google.com/maps?q={{ $row->unit_record->usersLocation->latitude }},{{ $row->unit_record->usersLocation->longitude }}" target="_blank">View Map</a>
                            @endif
                        </td>
                    </tr>
                @endif

                {{-- Unproductive --}}
                @foreach ($row->shop_visits as $visit)
                    <tr data-datetime="{{ $fullDateTime }}">
                        <td></td>
                        <td>{{ $row->shop_code }}</td>
                        <td>{{ $date }}</td>
                        <td>{{ $time }}</td>
                        <td>{{ $row->tso }}</td>
                        <td>{{ $row->manager_name }}</td>
                        <td>{{ $row->distributor_name }}</td>
                        <td>{{ $row->shop_name }}</td>
                        <td>Unproductive Shop</td>
                        <td>0</td>
                        <td>{{ $statuses[$visit->visit_reason_id ?? 0] ?? '' }}</td>
                        <td>{{ $row->shop_map_name ?? '-' }}</td>
                        <td>
                            @if ($lat && $long)
                                <a href="https://www.google.com/maps?q={{ $lat }},{{ $long }}" target="_blank">View Map</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot class="fw-bold bg-light">
            <tr>
                <td><strong>Total</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><strong>{{ $total_global_pcs }}</strong></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $(document).ready(function () {
        let $tbody = $('.filterTable tbody');
        let $rows = $tbody.find('tr').get();

        // Sort by data-datetime attribute
        $rows.sort(function (a, b) {
            let aTime = new Date($(a).data('datetime'));
            let bTime = new Date($(b).data('datetime'));
            return aTime - bTime;  // ascending: oldest first
        });

        // Re-append sorted rows
        $.each($rows, function (i, row) {
            $tbody.append(row);
        });

        // Add serial numbers
        $tbody.find('tr').each(function (i) {
            $(this).find('td:first').text(i + 1);
        });
    });
</script>

