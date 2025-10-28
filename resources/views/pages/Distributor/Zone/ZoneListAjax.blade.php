@foreach ($zone as $key => $row)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->zone_name }}</td>
        <td>{{ $row->username }}</td>
        <td>
            <div>
                @can('Zone_Edit')
                    <a href="{{ route('zone.edit', $row->id) }}" class="btn btn-primary btn-sm">Edit</a>
                @endcan
                @can('Zone_Delete')
                    <button type="button" id="delete-user" data-url="{{ route('zone.destroy', $row->id) }}"
                        class="btn btn-danger btn-sm">Delete</button>
                @endcan
            </div>
        </td>

    </tr>
@endforeach
