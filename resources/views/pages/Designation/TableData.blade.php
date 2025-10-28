@foreach ($designation as $key => $row)
    <tr class="text-center">
        <td>{{ ++$key }}</td>
        <td>{{ $row->name }}</td>
        <td>{{ $row->status == 1 ? 'Active' : 'Not Active' }}</td>
        <td>
            <div>
                @can('Designation_Edit')
                    <a href="{{ route('designation.edit', $row->id) }}" class="btn btn-primary btn-sm">Edit</a>
                @endcan
                @can('Designation_Delete')
                    <button type="button" id="delete-user" data-url="{{ route('designation.destroy', $row->id) }}"
                        class="btn btn-danger btn-sm">Delete</button>
                @endcan
            </div>
        </td>

    </tr>
@endforeach
