@foreach ($stock as $key => $row)
    <tr class="text-center">
        <td>{{ ++$key }}</td>
        <td>{{ $row->voucher_no }}</td>
        <td>{{ date('d-m-Y',strtotime($row->voucher_date)) }}</td>
        <td>{{ $row->distributor->distributor_name }}</td>
        <td>{{ $row->distributorsole->distributor_name ?? '' }}</td>
        <td>
            <div>
                <a target="_blank" href="{{ route('stock.show', $row->voucher_no) }}" class=""><i class="far fa-eye"></i></a>
                <i data-url="{{ route('stock.destroy', $row->voucher_no) }}" id="delete-user" class="far fa-trash-alt"></i>
            </div>
            <div>
            </div>
        </td>

    </tr>
@endforeach
