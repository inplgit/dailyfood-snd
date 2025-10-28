<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', 'SND || Caregory')
@section('content')

    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{$shop->company_name}} (Report) ({{strtoupper(str_replace('_' ,' ' , $request->type))}})</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                                {{-- <th>Execution</th> --}}
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="data">
                            @php
                                $total = 0;
                            @endphp
                            @if ($sales)
                                @foreach ($sales as $key => $row)
                                    <tr class="text-center">
                                        {{-- <td><input type="checkbox" class="form-control" id="CheckUnCheck"></td> --}}
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $row->invoice_no }}</td>
                                        <td>{{ date('d-m-Y', strtotime($row->dc_date)) }}</td>
                                        <td>{{ $row->distributor->distributor_name }}</td>
                                        <td>{{ $row->tso->name ?? '' }}</td>
                                        <td>{{ $row->tso->cities->name ?? '' }}</td>
                                        <td>{{ $row->shop->company_name ?? '' }}</td>
                                        {{-- <td>{{ $row->excecution ? 'YES' : 'NO' }}</td> --}}
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
                                                        {{-- <!-- <a target="_blank" href="{{ route('sale.show', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">View</a> --> --}}
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
                                <td colspan="7">Total</td>
                                <td colspan="1">{{ number_format($total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
@endsection
