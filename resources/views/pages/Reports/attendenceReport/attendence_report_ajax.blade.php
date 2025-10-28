@php
    use App\Models\Attendence;
    $monthDays = Carbon\Carbon::now()->month($monthYear[1])->daysInMonth;
@endphp
<div class="table-responsive">
    <table id="dataTable" class="table table-bordered">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Emp Code</th>
                <th>Emp Name </th>
                <th>Designation </th>
                <th>Distributor</th>
                <th>City</th>
                <th>Total Days</th>
                <th>Total Attendence</th>
                <th>Total Holidays</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendences as $key => $attendence)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $attendence['tso_code'] ?? '' }}</td>
                    <td>{{ $attendence['name'] ?? '' }}</td>
                    <td>{{ $attendence['designation']['name'] ?? '' }}</td>
                    <td>{{ $attendence['distributor']['distributor_name'] ?? '' }}</td>
                    <td>{{ $attendence['cities']['name'] ?? '' }}</td>
                    <td>
                        {{ $monthDays }}
                    </td>
                    <td>
                        @php
                            $presentDays = Attendence::whereMonth('in', '=', $monthYear[1])
                                ->whereYear('in', '=', $monthYear[0])
                                ->where('tso_id', $attendence['id'])
                                ->groupBy(DB::raw('DATE(`in`)'))
                                ->get();
                                $presentDays = count($presentDays);
                        @endphp
                        {{ $presentDays }}
                    </td>
                    <td>{{$monthDays - $presentDays}}</td>
                    <td><a href="{{route('attendence_report_detail', ['id'=>$attendence['id'], 'date'=> $monthYear])}}" class="btn btn-sm btn-info">Detail</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
