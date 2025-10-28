@php
    use App\Models\Attendence;
    // $monthDays = Carbon\Carbon::now()->month($monthYear[1])->daysInMonth;
    // dd($monthYear);
    $from = Carbon\Carbon::parse($from_date);
    $to = Carbon\Carbon::parse($to_date);
    // dd($from , $to);
    $diff = $from->diffInDays($to);
    // dd($diff);
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
                {{-- <th>ChecK in</th>
                <th>ChecK Out</th> --}}
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
                        {{ $diff }}
                    </td>
                    <td>
                        @php
                         $presentDays = Attendence::whereBetween('in', [$from, $to])
                                ->where('tso_id', $attendence['id'])
                                ->groupBy(DB::raw('DATE(`in`)'))
                                ->get();
                            // $presentDays = Attendence::whereMonth('in', '=', $monthYear[1])
                            //     ->whereDay('in', '=', $monthYear[2])
                            //     ->whereYear('in', '=', $monthYear[0])
                            //     ->where('tso_id', $attendence['id'])
                            //     ->groupBy(DB::raw('DATE(`in`)'))
                            //     ->first();
                                // dd($presentDays);
                                $presentDays = count($presentDays);
                                // dd($from_date , $to_date);
                        @endphp
                        {{$presentDays}}
                        {{-- {{ $presentDays->in??'---' }} --}}
                    </td>
                    <td> {{$diff - $presentDays}}</td>
                    {{-- <td>{{ $presentDays->out??'---'}}</td> --}}
                    <td><a href="{{route('attendence_report_detail', ['id'=>$attendence['id'], 'from_date'=> $from_date , 'to_date' => $to_date])}}" class="btn btn-sm btn-info">Detail</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
