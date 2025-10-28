<?php
 use App\Helpers\MasterFormsHelper;
$master_form = new MasterFormsHelper();
?>
<form method="post" action="{{ route('route.route_tso_wise') }}">
@method('PUT')
@csrf
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Sr No</th>
            <th>Route Name</th>
            <th>Days</th>
        </tr>
        </thead>
        <tbody id="data">
@foreach($tso as $key => $row)
    @php
        $days = $row->RouteDay->pluck('day')->toArray();
    @endphp
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->route_name }}</td>
        <td>
            <select class="form-control" name="day[{{$key}}][]" multiple>
                <option value=""> select</option>
                @foreach ($master_form->Days() as $day )
                    <option {{ in_array( $day, $days) ? 'selected' : ''}} value="{{ $day }}"> {{ $day }} </option>
                @endforeach
                <input type="hidden" name="ids[{{$key}}]" value="{{ $row->id }}"/>
            </select>
        </td>
    </tr>
@endforeach
        </tbody>
    </table>
</div>
</br>
<div class="col-12">
    <button type="submit" class="btn btn-primary mr-1">Create</button>

</div>
</form>

