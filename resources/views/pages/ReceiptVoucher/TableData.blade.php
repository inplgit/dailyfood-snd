@foreach ($receiptVoucher as $key => $row)
    <tr class="text-center">
        <td>{{ ++$key }}</td>
        <td>{{ $row->issue_date}}</td>
        <td>{{ $row->distributor->distributor_name }}</td>
        <td>{{ $row->tso->name }}</td>
        <td>{{ $row->shop->company_name ?? '' }}</td>
        <td>{{ $row->deliveryMan->name ?? '' }}</td>
        <td>{{ $row->route->route_name ?? '' }}</td>
        <td>{{ $row->execution == 1 ? 'Yes' : 'No' }}</td>
        <td>{{ $row->amount ?? '' }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                    @can('Receipt_Voucher_View')
                    {{-- <a target="_blank" href="{{ route('receipt-voucher.show', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">View</a> --}}
                    <a target="_blank" data-url="{{ route('receipt-voucher.show', $row->id) }}" data-title="View Receipt Voucher" class="dropdown-item_sale_order_list dropdown-item launcher">View</a>

                    @endcan
                    @if (!$row->execution)
                    @can('Receipt_Voucher_Edit')
                        <a href="{{ route('receipt-voucher.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                    @can('Sale_Return_Execute_Delete', 'Sales_Return_Delete')
                        <a href="{{ route('receipt-voucher.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                    @endcan
                    @endif
                </div>
            </div>

           <!-- Button trigger modal -->
            {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
            View
            </button> --}}

            <!-- Modal -->
            {{-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">



                <div style="" id="content" class="container print">


                    <div class="card ptb">

                        <h1 class="for-print">Sales Order</h1>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 well">
                                <table class="table table table-bordered">
                                    <tr>
                                        <th>Order Number:</th>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>Order Date:</th>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 well">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Distributor:</th>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>TSO:</th>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>Route:</th>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>Sub Route:</th>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <th>Shop:</th>
                                        <td></td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Order Details</h4>
                                <table class="table table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Sr No</th>
                                        <th>Product</th>
                                        <th>QTY</th>
                                        <th>Rate</th>
                                        <th>Discount</th>
                                        <th>Tax</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    <tr class="bold" style="">
                                        <td class="text-center" colspan="6">Total</td>
                                        <td colspan="1"></td>
                                    </tr>

                                        <tr class="bold" style="">
                                            <td class="text-center" colspan="6">Bulk Discount</td>
                                            <td colspan="1"></td>
                                        </tr>

                                        <tr class="bold" style="">
                                            <td class="text-center" colspan="6">Net Total</td>
                                            <td colspan="1"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4>Free Units Detail</h4>
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Pieces</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
                </div>
            </div> --}}
            </div>
        </td>

    </tr>
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
@endforeach
