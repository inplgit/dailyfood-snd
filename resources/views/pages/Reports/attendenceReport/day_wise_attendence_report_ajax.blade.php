@php
    use App\Models\Attendence;
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();

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
                <th>Emp ID</th>
                <th>Emp Name </th>
                <th>CNIC </th>
                <th>Designation </th>
                <th>Distributor</th>
                <th>City</th>

                <th>Location In</th>
                <th>Location Out</th>
                <th>Status </th>
                <th>Date </th>
                <th>ChecK in</th>
                <th>ChecK Out</th>
                <th>Duty hours</th>

            </tr>
        </thead>
        <tbody>
            @php
                $count = 0;
            @endphp
            @foreach ($attendences as $key => $attendence)
                @for ($date = $from->copy(); $date->lte($to) ; $date->addDay())

                    @php
                        $attendence_detail = Attendence::where('tso_id', $attendence['id'])
                        ->where('distributor_id', $attendence['distributor_id'])
                        ->whereDate('in' , $date->toDateString())->first();

                        $outAddress = $master->getAddress($attendence_detail?->latitude_out, $attendence_detail?->longitude_out);
                        // dd($attendence_detail);
                        // dump($date , $date->toDateString());
                    @endphp
                    {{-- <tr>
                        <td>{{$date}}
                            @php
                                dump($date , $attendence_detail);
                            @endphp
                        </td>
                    </tr> --}}
                    <tr>
                        <td>{{ ++$count }}</td>
                        <td>{{ $attendence['emp_id'] ?? '' }}</td>
                        <td>{{ $attendence['name'] ?? '' }}</td>
                        <td>{{ $attendence['cnic'] ?? '--' }}</td>
                        <td>{{ $attendence['designation']['name'] ?? '' }}</td>
                        <td>{{ $master->get_distributor_name($attendence['distributor_id']) ?? ''}}</td>
                        {{-- <td>{{ $attendence['distributor']['distributor_name'] ?? '' }}</td> --}}
                        <td>{{ $attendence['cities']['name'] ?? '' }}</td>

                        <td>{{$attendence_detail->usersLocation->location_title ?? '--'}}</td>
                        <td>{{$outAddress ?? '--'}}</td>
                        <td>{{ isset($attendence_detail->in) ? 'Present' : 'Absent'}}</td>
                        <td>{{ isset($date) ? date_format($date , 'd-M-Y') :  '--'}}</td>
                        <td>{{ isset($attendence_detail->in) ? MasterFormsHelper::changeDateFormat2($attendence_detail->in , 'h:i:s a') : '--'}}</td>
                        <td>{{ isset($attendence_detail->out) ? MasterFormsHelper::changeDateFormat2($attendence_detail->out , 'h:i:s a') : '--'}}</td>
                        @php
                            $result = '--';
                            if (isset($attendence_detail->out) && isset($attendence_detail->in)) {
                                $out = Carbon\Carbon::parse($attendence_detail->out);
                                $in = Carbon\Carbon::parse($attendence_detail->in);
                                // $diff = $attendence_detail->out - $attendence_detail->in;
                                $diff = $out->diffInMinutes($in);
                                $hours = floor($diff/60);
                                $minutes = $diff % 60;
                                $result = "{$hours}:{$minutes}";
                            }

                        @endphp
                        <td>{{$result?? '--'}}</td>
                    </tr>
                @endfor
            @endforeach
        </tbody>
    </table>
</div>
