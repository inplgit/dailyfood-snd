<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
<form method="post" action="{{ route('route.transfer_store') }}">
    @method('PUT')
    @csrf
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sr No</th>
                    <th>Route Name</th>
                    <th>Distributor</th>
                    <th>Order Booker</th>
                </tr>
            </thead>
            <tbody id="data">
                @foreach ($tso as $key => $row)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $row->route_name }}</td>
                        <td>
                            <select  onchange="get_tso2(this)" class="form-control select2 distributor_ids" name="distributor_ids[{{ $key }}]">
                                <option value="">select</option>
                                @foreach ( $master->get_all_distributors() as $row1 )
                                   <option value="{{ $row1->id }}" {{$row->distributor_id == $row1->id  ? 'selected' : ''}}>{{ $row1->distributor_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control select2 tso_ids" id="tso_ids" name="tso_ids[{{ $key }}]"
                                required>
                                <option value="">select</option>
                                @foreach ( $master->get_all_tso_by_distributor_id($row->distributor_id) as $row2 )
                                    <option value="{{ $row2->id }}" {{$row->tso_id == $row2->id ? 'selected' : ''}}>{{ $row2->name }}</option>
                                @endforeach
                                {{-- @foreach ($master->get_all_tso() as $row1)
                                    <option value="{{ $row1->id }}">{{ $row1->name }}</option>
                                @endforeach --}}
                            </select>
                            <input type="hidden" name="ids[{{ $key }}]" value="{{ $row->id }}" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </br>
    <div class="col-12">
        <button type="submit" class="btn btn-primary mr-1">Transfer</button>

    </div>
</form>
