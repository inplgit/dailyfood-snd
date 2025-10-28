<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>

@foreach ($log_data as $key => $row)
    @php
        $data = json_decode($row->description);
        $distributors = DB::table('distributors')->whereIn('id' , $data->distributors_id)->pluck('distributor_name')->implode(', ');
        // dd($row , $row->Log , $data->distributors_id , $distributors , $data);
    @endphp
    <tr class="text-center">
        <td>{{ ++$key }}</td>
        <td>{{ $row->activity_type ?? '' }}</td>
        <td>{{ $master->changeDateFormat2($row->date , 'd M,Y') ?? '' }}</td>

        <td>{{ $row->title ?? '' }}</td>
        <td>{{ $data->name ?? '' }}</td>
        <td>{{ $distributors ?? '' }}</td>
        <td>{{ $row->user_name ?? '' }}</td>

    </tr>
@endforeach
