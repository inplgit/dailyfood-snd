@foreach ($so_data as $key => $row)
    <tr class="text-center">
        <td><input type="checkbox" name="ids[]" value="{{ $row->id }}" class="checkMeOut" id=""></td>
        <td>{{ ++$key }}</td>
        <td>{{ $row->invoice_no }}</td>
        <td>{{ date('d-m-Y', strtotime($row->dc_date)) }}</td>
        <td>{{ $row->distributor['distributor_name'] }}</td>
        <td>{{ $row->tso['name'] }}</td>
        <td>{{ $row->shop['company_name'] }}</td>

        <td>{{ $row->total_amount }}</td>


    </tr>
@endforeach
