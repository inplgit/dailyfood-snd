@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@foreach ($log_data as $key => $row)
    @php
        $data = json_decode($row->description);
        $days = $data->route_days ? implode(',',$data->route_days) : '';
        // dd($row , $data);
    @endphp
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->activity_type ?? '' }}</td>
        <td>{{ $master->changeDateFormat2($row->date , 'd M,Y') ?? '' }}</td>

        <td>{{ $data->route_name ?? '' }}</td>
        <td>{{ MasterFormsHelper::get_distributor_name($data->distributor_id) ?? '' }}</td>
        <td>{{ MasterFormsHelper::get_tso_name($data->tso_id) ?? '' }}</td>
        <td>{{ $days }}</td>

        <td>{{ $row->user_name ?? '' }}</td>


    </tr>
@endforeach
