<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>


@extends('layouts.master')
@section('title', 'Sale Order')
@section('content')



    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">SALES {{strtoupper(str_replace('_' ,' ' , $request->type))}}</h4>
                </div>
                <div class="table-responsive">
                    <input type="hidden" value="{{$request->type}}" id="type">
                    <table class="table table-bordered yajra-table">
                        <thead>
                            <tr>
                                {{-- <th><input type="checkbox" class="form-control" id="CheckUnCheck"></th> --}}
                                <th>Sr No</th>
                                <th>Inoivce No.</th>
                                <th>Inoivce Date.</th>
                                <th>Distributor</th>
                                <th>TSO</th>
                                <th>City</th>
                                <th>Shop</th>
                                <th>Execution</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="data">
                            @php
                                $total = 0;
                            @endphp
                            {{-- @if ($sales)
                                @foreach ($sales as $key => $row)
                                    <tr class="text-center">
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $row->invoice_no }}</td>
                                        <td>{{ date('d-m-Y', strtotime($row->dc_date)) }}</td>
                                        <td>{{ $row->distributor->distributor_name }}</td>
                                        <td>{{ $row->tso->name ?? '' }}</td>
                                        <td>{{ $row->tso->cities->name ?? '' }}</td>
                                        <td>{{ $row->shop->company_name ?? '' }}</td>
                                        <td>{{ $row->excecution ? 'YES' : 'NO' }}</td>
                                        <td>{{ $row->total_amount }}</td>
                                        @php
                                            $total += $row->total_amount;
                                        @endphp
                                        <td>
                                            <div class="dropdown">
                                                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false"></i>
                                                <div class="dropdown-menu dropdown-menu_sale_order_list"
                                                    aria-labelledby="dropdownMenuButton">
                                                    @can('Sale_Order_VIew')
                                                        <a target="_blank" data-url="{{ route('sale.show', $row->id) }}"
                                                            data-title="View Sale Order"
                                                            class="dropdown-item_sale_order_list dropdown-item launcher">View</a>
                                                    @endcan


                                                    @if (!$row->excecution)
                                                        @can('Sale_Order_Edit')
                                                            <a href="{{ route('sale.edit', $row->id) }}"
                                                                class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                                                        @endcan
                                                        @can('Sale_Order_Delete')
                                                            <a href="{{ route('sale.destroy', $row->id) }}" id="delete-user"
                                                                class="dropdown-item_sale_order_list dropdown-item"
                                                                href="#">Delete</a>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <td colspan="8">Total</td>
                                <td colspan="1">{{ number_format($total, 2) }}</td>
                            </tr> --}}

                        </tbody>
                    </table>
                </div>
                {{-- <div class="create">
                @can('Create_Sales_Order')
                        <a href="{{ route('sale.create') }}" class="btn btn-success mr-1">Create Sale Order</a>
                    @endcan
                </div> --}}
            </div>
        </div>
    </div>
    <!-- Basic Floating Label Form section end -->

@endsection
@section('script')

    <script>
        function showModal(url, title) {
            var $modal = $('#showModal'); // Define $modal within the function
            $.ajax({
                url: url,
                method: 'GET',
                success: function(res) {
                    // Update modal content
                    $modal.find('.modal-body').html(res);
                    $modal.find('.modal-title').text(title);
                    // Open modal after updating content
                    openModal();
                },
                error: function(xhr, status, error) {
                    // Handle errors if necessary
                    console.error("Error loading content:", error);
                }
            });
        }

        // Define openModal and closeModal functions outside of showModal
        function openModal() {
            $('#showModal').fadeIn();
        }

        function closeModal() {
            $('#showModal').fadeOut();
        }

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
    {{-- <script>
        $(document).ready(function() {
            get_ajax_data();
        });
    </script> --}}

    <script>
        $(document).ready(function() {

        var columns = [
                // { data: 'id', name: 'id' },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // Auto-increment column
                { data: 'invoice_no', name: 'invoice_no'  },
                { data: 'dc_date', name: 'dc_date' },
                { data: 'distributor', name: 'distributor' , searchable: false },
                { data: 'tso', name: 'tso' , searchable: false },
                { data: 'city', name: 'city' , searchable: false },
                { data: 'shop', name: 'shop' , searchable: false},
                { data: 'excecution', name: 'excecution' },
                { data: 'total_amount', name: 'total_amount' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ];

        // Define custom filter inputs that you want to send with the AJAX request
        var filterInputs = [
            {name: 'type', selector: "#type"},
        ];
        // Initialize DataTable with custom search params
        data_table().initialize('{{ Request::url('') }}', columns, filterInputs);


        });
    </script>
@endsection
