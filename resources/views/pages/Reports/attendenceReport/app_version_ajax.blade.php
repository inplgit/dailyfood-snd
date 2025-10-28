@php
    use App\Models\Attendence;
    use App\Helpers\MasterFormsHelper;
    use Carbon\Carbon; // ✅ Correct Carbon import

    $master = new MasterFormsHelper();

    $from = Carbon::parse($from_date);
    $to = Carbon::parse($to_date);
    $diff = $from->diffInDays($to);
@endphp

<div class="table-responsive">
    <table id="dataTable" class="table table-bordered">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Emp ID</th>
                <th>Emp Name</th>
                <th>CNIC</th>
                <th>Designation</th>
                <th>Distributor</th>
                <th>City</th>
                <th>Version</th>
                <th>Update Date</th>
            </tr>
        </thead>
        <tbody>
            @php $count = 0; @endphp
            @foreach ($attendences as $attendence)
                <tr>
                    <td>{{ ++$count }}</td>
                    <td>{{ $attendence['emp_id'] ?? '' }}</td>
                    <td>{{ $attendence['name'] ?? '' }}</td>
                    <td>{{ $attendence['cnic'] ?? '--' }}</td>
                    <td>{{ $attendence['designation']['name'] ?? '' }}</td>
                    <td>{{ $master->get_distributor_name($attendence['distributor_id']) ?? '' }}</td>
                    <td>{{ $attendence['cities']['name'] ?? '' }}</td>
                    <td>{{ $attendence['user_version'] ?? '' }}</td>
                     <td>{{ \Carbon\Carbon::parse($attendence['updated_at'])->setTimezone('Asia/Karachi')->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
