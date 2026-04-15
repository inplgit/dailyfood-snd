@php
use App\Helpers\MasterFormsHelper;
@endphp

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="trackingTable">
        <thead>
            <tr>
                <th>TSO</th>
                <th>Distributor</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Recorded At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $row->tso_name }}</td>
                <td>{{ $row->distributor_name }}</td>
                <td>{{ $row->latitude }}</td>
                <td>{{ $row->longitude }}</td>
                <td>{{ \Carbon\Carbon::parse($row->sync_date_time)->format('d-M-Y H:i:s') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No tracking data found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        // Convert PHP collection to JSON for JS usage
        var trackingPoints = @json($data);
        drawPath(trackingPoints);
    });
</script>
