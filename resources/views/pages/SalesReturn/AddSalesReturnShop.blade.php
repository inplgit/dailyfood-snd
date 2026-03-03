<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>

@extends('layouts.master')
@section('title', 'SND || Add Sales Return')

@section('content')


    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title">ADD Sales Return</h4>
                    </div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('sales_return.submit') }}" enctype="multipart/form-data"
                            id="returnForm">
                            @csrf

                            <div class="row">

                                {{-- Distributor --}}
                                <div class="col-md-3">
                                    <label class="control-label">Distributor</label>
                                    <select name="distributor_id" class="form-control select2" onchange="get_tso()"
                                        id="distribuotr_id" tabindex="-1" aria-hidden="true">
                                        <option value="" selected="">select</option>
                                        @foreach ($master->get_all_distributor_user_wise() as $distributor)
                                            <option value="{{ $distributor->id }}">{{ $distributor->distributor_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- TSO --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select onchange="get_route_by_tso()" class="form-control select2" id="tso_id" name="tso_id" required>
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                                    <label class="control-label">TSO</label>
                                    <select onchange="get_shop_by_tso()" class="form-control select2" name="tso_id" id="tso_id">
                                        <option value="">Select a TSO</option>
                                    </select>
                                </div> --}}

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Route</label>
                                        <select onchange="get_shop_by_route()" class="form-control" id="route_id" name="route_id" required>
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Shop --}}
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Shop</label>
                                        <select class="form-control select2" id="shop_id" name="shop_id" required>
                                            <option value="">select</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- PRODUCT TABLE --}}
                            <div class="table-responsive mt-3">
                                <div class="add-button text-right mb-2">

                                    <button type="button" class="btn btn-success btn-sm " id="add_row_btn"> + Add More </button>
                                </div>
                                <table class="table table-bordered"S>
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Status</th>
                                            <th>Image</th>
                                            <th>Preview</th>
                                            <th>Remark</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sales_return_body">

                                        <tr class="item-row">

                                            <td>
                                                <select name="details[0][product_id]" class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->product_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <input type="number" name="details[0][qty]" value="0"
                                                    class="form-control">
                                            </td>

                                            <td>
                                                <select name="details[0][reason]" class="form-control">
                                                    <option value="Fresh">Fresh</option>
                                                    <option value="Expired">Expired</option>
                                                    <option value="Damaged">Damaged</option>
                                                </select>
                                            </td>

                                            <td>
                                                <input type="file" name="details[0][damage_photo]"
                                                    class="form-control img-upload" accept="image/*">
                                            </td>

                                            <td>
                                                <img src="" class="img-preview" width="50"
                                                    style="display: none;">
                                            </td>

                                            <td>
                                                <input type="text" name="details[0][remarks]" class="form-control"
                                                    placeholder="Remark">
                                            </td>

                                            <td>
                                                <button type="button"
                                                    class="btn btn-danger btn-sm delete_row_btn">X</button>
                                            </td>

                                        </tr>

                                    </tbody>

                                </table>
                            </div>

                            <button type="submit" class="btn btn-primary mt-2">
                                Submit
                            </button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script>
        $(document).ready(function() {$('select').select2();});
     document.getElementById('add_row_btn').addEventListener('click', function() {

    let tbody = document.getElementById('sales_return_body');
    let first = document.querySelector('.item-row');

    // Destroy select2 before cloning
    $(first).find('select').select2('destroy');

    let clone = first.cloneNode(true);
    let index = document.querySelectorAll('.item-row').length;

    clone.querySelectorAll('select, input').forEach(function(el) {

        let name = el.getAttribute('name');

        if (name) {
            name = name.replace(/\[\d+\]/, "[" + index + "]");
            el.setAttribute("name", name);
        }

        // Reset values
        if (el.type !== "file") {
            el.value = "";
        }
    });

    clone.querySelector('.img-preview').style.display = "none";
    tbody.appendChild(clone);

    // Re-init select2 on all selects
    $('select').select2();
});


        // Delete Row
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('delete_row_btn')) {

                if (document.querySelectorAll('.item-row').length === 1) {
                    alert("At least one item is required.");
                    return;
                }
                e.target.closest('.item-row').remove();
            }
        });

        // Image Preview
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('img-upload')) {
                let preview = e.target.closest('tr').querySelector('.img-preview');
                let file = e.target.files[0];

                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(ev) {
                        preview.src = ev.target.result;
                        preview.style.display = "block";
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    </script>
@endsection
