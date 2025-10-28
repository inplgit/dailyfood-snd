@foreach ($rack as $key => $row)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->rack_code }}</td>
        <td>{{ $row->bar_code }}</td>
        <td>
            @php
                $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
            @endphp

            {!! $generator->getBarcode($row->bar_code , $generator::TYPE_CODE_128) !!}
        </td>
        <td>
            <div>
                @can('Price_Type_Edit')
                    <a href="{{ route('rack.edit', $row) }}" class="btn btn-primary btn-sm">Edit</a>
                @endcan
                @can('Price_Type_Delete')
                    <button type="button" id="delete-user" data-url="{{ route('rack.destroy', $row->id) }}"
                        class="btn btn-danger btn-sm">Delete</button>
                @endcan
            </div>
        </td>

    </tr>
@endforeach
