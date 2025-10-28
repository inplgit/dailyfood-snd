<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
use App\Models\Shop;


?>
 <div class="row" id="table-bordered">

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Shop List<span class="badge badge-success"></span></h4>
                <button type="button" id="exportBtn" onclick="exportBtn('ShopList')" class="btn btn-success">Export
                    Excel</button>
            </div>
            @if(isset($shop))
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Shop Code</th>
                        <th>Shop Name</th>
                        <th>City</th>
                        <th>Distributor</th>
                        <th>TSO</th>
                        <th>Route</th>
                        <th>Sub Routes</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="data">

                        @foreach ($shop as $key => $row)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $row->shop_code }}</td>
                            <td>{{ $row->company_name }}</td>
                            <td> {{ $row->Distributor->city ?? '' }}</td>
                            {{-- <td> {{ ($row->city) ? $row->cities->name ?? '' : '' }}</td> --}}
                            <td>{{ ($row->distributor_id) ? $row->distributor->distributor_name : '' }}</td>
                            <td>{{ $row->tso->name??'' }}</td>
                            <td>{{  $row->route->route_name ?? $row->route_id }}</td>
                            <td>{{  $row->subroutes->name ?? '' }}
                                {{-- @php
                                    dump($row->image);
                                @endphp --}}
                            </td>
                            @php
                                if ($row->active == 1) {
                                    $active_status = 'Activate';
                                }
                                elseif ($row->active == 2) {
                                    $active_status = 'Activate Request';
                                }
                                elseif ($row->active == 3) {
                                    $active_status = 'Deactivate Request';
                                }
                                elseif ($row->active == 0) {
                                    $active_status = 'Deactivate';
                                }
                                elseif ($row->active == 4) {
                                    $active_status = 'New Shop Create';
                                }
                            @endphp
                            <td>{{$active_status ?? ''}}</td>
                            <td>
                                <div class="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                    <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                                        {{-- <a target="_blank" data-url="{{ route('shop.image', $row->id) }}" data-title="View Shop" class="dropdown-item_sale_order_list dropdown-item launcher">View</a> --}}
                                        <a href="{{ route('shop.show', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">View</a>

                                        @can('Shop_Edit')
                                            <a href="{{ route('shop.edit', $row) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                                        @endcan
                                        @can('Shop_Delete')
                                            <a href="javascript:void(0);" data-url="{{ route('shop.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                                        @endcan
                                        @if ($row->active == 0)
                                            @can('Shop_Activate')
                                                <a href="javascript:void(0);" data-text="You want to activate this shop" data-url="{{ route('shop.active', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item" id="active-record">Activate</a>
                                            @endcan
                                        @elseif ($row->active == 1)
                                            @can('Shop_Deactivate')
                                                <a href="javascript:void(0);" data-text="You want to deactivate this shop" data-url="{{ route('shop.inactive', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item" id="inactive-record">Deactivate</a>
                                            @endcan
                                        @endif
                                    </div>
                                </div>




                            </td>
                        </tr>
                        @endforeach
                </table>

                {{-- <div>
                    Total Records: {{ $shop->total() }}
                </div>

                <div>
                    Total Pages: {{ $shop->lastPage() }}
                </div>
                <div style="float: right">
                {!! $shop->appends(request()->query())->links() !!} --}}

            </div>
            @endif
        </div>
    </div>
   </div>
  </div>


<script>

    // Bind event outside of AJAX call
    $(document).ready(function() {
        $('.launcher').on('click', function() {
            showModal($(this).data('url'), $(this).data('title'));
        });
        // Close modal when clicking on close button or outside the modal
        $('#showModal').on('click', closeModal);
        $('#showModal .modal-dialog').on('click', function(event) {
            event.stopPropagation(); // Prevent closing when clicking on modal content
        });
        $('#showModal .modal-close').on('click', closeModal);
    });

</script>


