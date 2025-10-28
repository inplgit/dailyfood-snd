@foreach ($city as $key => $row)
    <tr class="text-center">
        <td>{{ ++$key }}</td>
        <td>{{ $row->name }}</td>
        <td>{{ $row->status == 1 ? 'Active' : 'Not Active' }}</td>
        <td>
            <div>

                
                @can('City_Edit') 
                <a href="{{ route('city.edit', $row->id) }}" class="btn btn-primary btn-sm">Edit</a>

                @endcan
                @can('City_Delete') 
                   
                <button type="button" id="delete-user" data-url="{{ route('city.destroy', $row->id) }}"
                    class="btn btn-danger btn-sm">Delete</button>
              @endcan
            </div>
        </td>

    </tr>
@endforeach
