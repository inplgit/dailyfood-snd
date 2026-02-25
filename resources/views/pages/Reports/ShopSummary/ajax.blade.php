<div class="table-responsive printBody">
    @php
        $distributorName = $distributor_id ? \App\Models\Distributor::find($distributor_id)->distributor_name : 'All';
        $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        $routeName = $route_id ? \App\Models\Route::find($route_id)->route_name : 'All';
        
        $colTotals = ['Total' => 0];
        foreach ($groupNames as $name) {
            $colTotals[$name] = 0;
        }

        foreach ($days as $day) {
            $colTotals['Total'] += $matrix[$day]['Total'] ?? 0;
            foreach ($groupNames as $name) {
                $colTotals[$name] += $matrix[$day][$name] ?? 0;
            }
        }
    @endphp

    <div class="row mb-2">
        <div class="col-12 text-center">
            <h3 class="mb-0">DAILY FOOD</h3>
            <h4 class="mb-0">SHOP {{ strtoupper($type) }} SUMMARY ({{ $distributorName }})</h4>
            <p class="mt-1">
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }} |
                <b>Route:</b> {{ $routeName }}
            </p>
        </div>
    </div>

    <table class="table table-bordered filterTable">
        <thead class="bg-light">
            <tr>
                <th class="align-middle">Days</th>
                <th class="align-middle">Total</th>
                @foreach ($groupNames as $name)
                    <th class="align-middle" style="font-size: 0.8rem;">{{ strtoupper($name ?? 'OTHERS') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($days as $day)
                <tr>
                    <td class="bg-light"><strong>{{ $day }}</strong></td>
                    <td class="font-weight-bold">{{ $matrix[$day]['Total'] }}</td>
                    @foreach ($groupNames as $name)
                        <td>{{ $matrix[$day][$name] ?? 0 }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-light font-weight-bold">
            <tr>
                <td>Total</td>
                <td>{{ $colTotals['Total'] }}</td>
                @foreach ($groupNames as $name)
                    <td>{{ $colTotals[$name] }}</td>
                @endforeach
            </tr>
        </tfoot>
    </table>
</div>

<style>
    .printBody {
        color: #333;
    }
    .printBody table {
        border: 2px solid #000 !important;
    }
    .printBody th, .printBody td {
        border: 1px solid #000 !important;
    }
    .printBody thead th {
        background-color: #f2f2f2 !important;
        font-weight: bold;
    }
    @media print {
        .printBody {
            width: 100%;
        }
        .col-md-9 { width: 75%; float: left; }
        .col-md-3 { width: 25%; float: left; }
    }
</style>
