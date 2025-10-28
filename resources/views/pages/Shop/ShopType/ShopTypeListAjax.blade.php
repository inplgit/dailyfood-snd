@foreach ($shop_type as $key => $row)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->shop_type_name }}</td>
        <td>{{ $row->username }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                    @can('Shop_Type_Edit')
                        <a href="{{ route('shoptype.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                    @can('Shop_Type_Delete')
                        <a href="{{ route('shoptype.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                    @endcan
                </div>
            </div>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
            View
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
            </div>
            </div>
        </td>

    </tr>
@endforeach
