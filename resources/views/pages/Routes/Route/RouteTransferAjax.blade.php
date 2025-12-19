<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>

<form method="post" action="{{ route('route.transfer_store') }}">
    @csrf
    @method('PUT')

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sr No</th>
                    <th>Route Name</th>
                    <th>Distributor</th>
                    <th>Order Booker (TSO)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tso as $key => $row)
                    @php
                        $selectedTsos = explode(',', $row->tso_ids);
                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $row->route_name }}</td>

                       <td>
    <select class="form-control select2 distributor_ids"
            onchange="get_tso2_tasfer(this)"
            name="distributor_ids[{{ $key }}]">
        <option value="">Select</option>
        @foreach ($master->get_all_distributors() as $d)
            <option value="{{ $d->id }}"
                {{ $row->distributor_id == $d->id ? 'selected' : '' }}>
                {{ $d->distributor_name }}
            </option>
        @endforeach
    </select>
</td>

<td>
    <select class="form-control select2 tso_ids"
            name="tso_ids[{{ $key }}][]"
            multiple required>
        @foreach ($master->get_all_tso_by_distributor_id($row->distributor_id) as $t)
            <option value="{{ $t->id }}"
                {{ in_array($t->id, $selectedTsos) ? 'selected' : '' }}>
                {{ $t->name }}
            </option>
        @endforeach
    </select>

    <input type="hidden" name="ids[{{ $key }}]" value="{{ $row->id }}">
</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">Transfer</button>
    </div>
</form>
