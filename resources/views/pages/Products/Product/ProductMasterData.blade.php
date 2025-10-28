@extends('layouts.master')
@section('title', "Product")
@section('content')


    <form id="list_data"  method="get" action="{{ Request::url('') }}"></form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create Product Data</h4>
                </div>
                <div class="product_list_data">
             
                        <div class="accordion">
                            <div class="accordion-item">
                                <button id="accordion-button-1" aria-expanded="false">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span class="accordion-title">Product Category</span>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="add_span">
                                                    <span class="accordion-title">Add</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="icon" aria-hidden="true"></span>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                                <div class="accordion-content">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="main_head">
                                                    <h2>Product Category</h2>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Category Name</label>
                                                            <input name="sales_price" type="text" class="form-control" placeholder="Category Name" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4">
                                                        <div class="button_product text-right">
                                                            <a href="#" type="submit" class="btn btn-primary mr-1">Submit</a>
                                                            <a href="#" type="reset" class="btn btn-outline-secondary">Reset</a>
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="accordion">
                            <div class="accordion-item">
                                <button id="accordion-button-1" aria-expanded="false">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span class="accordion-title">Product Type</span>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="add_span">
                                                    <span class="accordion-title">Add</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="icon" aria-hidden="true"></span>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                                <div class="accordion-content">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="main_head">
                                                    <h2>Product Type</h2>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Product Type Name</label>
                                                            <input name="sales_price" type="text" class="form-control" placeholder="" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4">
                                                        <div class="button_product text-right">
                                                            <a href="#" type="submit" class="btn btn-primary mr-1">Submit</a>
                                                            <a href="#" type="reset" class="btn btn-outline-secondary">Clear Form</a>
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="accordion">
                            <div class="accordion-item">
                                <button id="accordion-button-1" aria-expanded="false">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span class="accordion-title">Brands</span>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="add_span">
                                                    <span class="accordion-title">Add</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="icon" aria-hidden="true"></span>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                                <div class="accordion-content">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="main_head">
                                                    <h2>Brands</h2>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">Brands Name</label>
                                                            <input name="sales_price" type="text" class="form-control" placeholder="" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4">
                                                        <div class="button_product text-right">
                                                            <a href="#" type="submit" class="btn btn-primary mr-1">Submit</a>
                                                            <a href="#" type="reset" class="btn btn-outline-secondary">Clear Form</a>
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <div class="accordion">
                            <div class="accordion-item">
                                <button id="accordion-button-1" aria-expanded="false">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <span class="accordion-title">Product Unit</span>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="add_span">
                                                    <span class="accordion-title">Add</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="icon" aria-hidden="true"></span>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                                <div class="accordion-content">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="main_head">
                                                    <h2>Product Unit</h2>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">UOM Name</label>
                                                            <input name="sales_price" type="text" class="form-control" placeholder="Category Name" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4">
                                                        <div class="button_product text-right">
                                                            <a href="#" type="submit" class="btn btn-primary mr-1">Submit</a>
                                                            <a href="#" type="reset" class="btn btn-outline-secondary">Clear Form</a>
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                           
                





              

                </div>
            </div>
        </div>
    </div>
 



@endsection
@section('script')
    <script>
        $(document).ready(function() {
            // get_ajax_data();
        });

    </script>
@endsection
