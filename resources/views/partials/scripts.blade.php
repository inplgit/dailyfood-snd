{{-- <script src="{{ url('/public/assets/js/jquery.js') }}"></script>
<script src="{{ url('/public/assets/js/custom.js') }}"></script> --}}

<div class="modal" id="showModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="print_head">
                <div class="row">
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-4">
                        <div class="head_contect">
                            <h1 class="for-print modal-title"></h1>
                        </div>

                    </div>
                    <div class="col-md-4">

                    </div>
                </div>
            </div>
            <div style=""  id="content" class="container print">
                <div class="modal-body"></div>
                <div class="pritn_views">
                    <button  id="print" type="button"  class="btn btn-success btn-sm  right">Print</button>
                    <button type="button" class="modal-close btn btn-close btn-sm" tabindex="-1">Close</button>
                </div>



            </div>
            <!-- <div class="modal-footer hide">
                <button type="button" class="btn btn-primary mr-1" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Reset</button>
            </div> -->
        </div>
    </div>
</div>

<script>
    new WOW().init();
    // function showModal(url, title) {
    //     jQuery.ajax({
    //         url: url,
    //         method: 'GET',
    //         success:function(data) {
    //             $('#showModal').modal('show', {backdrop: 'false'});
    //             ('#showModal .modal-body').html(data);
    //             $j('#showModal .modal-title').html(title);
    //         },
    //         error: function (xhr, status, error) {
    //             // Handle errors if necessary
    //             console.error("Error loading content:", error);
    //         }
    //     });
    // }
</script>

<!-- BEGIN: Vendor JS-->
<script src="{{ url('/public/assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ url('/public/assets/vendors/js/charts/apexcharts.min.js') }}"></script>
<script src="{{ url('/public/assets/vendors/js/extensions/toastr.min.js') }}"></script>
<script src="{{ url('/public/assets/vendors/js/editors/quill/katex.min.js') }}"></script>
<script src="{{ url('/public/assets/vendors/js/editors/quill/highlight.min.js') }}"></script>
<script src="{{ url('/public/assets/vendors/js/editors/quill/quill.min.js') }}"></script>

{{-- <script src="{{ url('/public/assets/vendors/js/forms/select/select2.full.min.js') }}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ url('/public/assets/js/core/app-menu.js') }}"></script>
<script src="{{ url('/public/assets/js/core/app.js') }}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{ url('/public/assets/js/scripts/pages/dashboard-ecommerce.js') }}"></script>
<script src="{{ url('/public/assets/js/scripts/charts/chart-apex.js') }}"></script>
<script src="{{ url('/public/assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ url('/public/assets/js/scripts/pages/app-email.js') }}"></script>

<script>
    var latitude = parseFloat({{ isset($shop->latitude)?$shop->latitude:24.8607343}}); // Example latitude
    var longitude = parseFloat({{ isset($shop->longitude)?$shop->longitude:67.0011364}}); // Example longitude
</script>
<script defer type="text/javascript"
    src="https://maps.googleapis.com/maps/api/js?key={{ env('MAP_KEY') }}&libraries=places&callback=initAutocomplete">
</script>
<script src="{{ url('/public/assets/js/map.js') }}"></script>
<!-- END: Page JS-->
<script>
    $(document).ready(function() {

        // Attach a click event handler to a button or link that triggers the printing
        $('#print').click(function() {
            $('#content').addClass('print');
            var printContents = document.getElementById('content').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        });
    });






    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    // generalize submit
    $("#subm").submit(function(e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.
        $(".print-error-msg").find("ul").html('');
        $(".alert-success").find("ul").html('');

        var form = $(this);
        var actionUrl = form.attr('action');
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        });
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: new FormData(this), // serializes the form's elements.
            // data: form.serialize(), // serializes the form's elements.
            contentType: false, // Set contentType to false
            processData: false, // Set processData to false

            success: function(data) {

                // console.log(data);
                if (data.catchError) {
                    $(".print-error-msg").find("ul").html('');
                    $(".print-error-msg").css('display', 'block');
                    $(".print-error-msg").find("ul").append('<li>' + data.catchError + '</li>');
                    window.scrollTo(0, 0);
                    return;
                }
                if ($.isEmptyObject(data.error)) {

                    $(".alert-success").find("ul").html('<li>' + data.success + '</li>');
                    $("#subm").trigger("reset");
                    // get_ajax_data();
                    handleSuccess();
                    $('#unique_code').val(data.code);

                } else {
                    printErrorMsg(data.error);
                }
            }
        });

    });

    // reset form
    function reset_form(id)
    {
        // $('.select2').select2();
        // $('select').select2();
        $('#'+id).trigger("reset");

        $('#' + id).find('.select2').each(function() {
            // Reset select2
            $(this).val(null).trigger('change');
        });
        // $("#subm").trigger("reset");
        // document.getElementById(id).reset();
        // document.querySelectorAll('#' + id + ' select').forEach(select => select.value  = '');
    }


    // generalize errors
    function printErrorMsg(msg) {

        $(".print-error-msg").find("ul").html('');
        $(".print-error-msg").css('display', 'block');
        $.each(msg, function(key, value) {

            $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            window.scrollTo(0, 0);
        });
    }

    // generalize get list
    function get_ajax_data() {
        var form = $('#list_data');
        var actionUrl = form.attr('action');
        $('#data').html('');
        var pages =  $('#pages').val();
        $('#loader').show();
        $.ajax({
            type: "get",
            url: actionUrl,
            data: form.serialize(), // serializes the form's elements.
            async: true,
            cache: false,
            success: function(data) {

                if ($.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable().destroy();
                    // Clear any previous filters in the table header
                }
                $('#dataTable thead tr').each(function() {
                    $(this).find('select').remove(); // Remove any existing filter select elements
                    $(this).find('span').remove(); // Remove any existing filter select elements
                });


                $('#loader').hide();
                $('#data').html(data);


                if(pages!='all')
                {
                    var table = $('#dataTable').DataTable({
                        aLengthMenu: [
                            [25, 50, 100, 200, -1],
                            [25, 50, 100, 200, "All"]
                        ],
                        iDisplayLength: -1
                    });
                }
                else
                {
                    var table = $('#dataTable').DataTable({
                        aLengthMenu: [
                            [25, 50, 100, 200, -1],
                            [25, 50, 100, 200, "All"]
                        ],
                        iDisplayLength: 25
                    });
                }


                table.columns().flatten().each(function(colIdx) {
                    // Create the select list and search operation
                    var select = $('<select />')
                        .appendTo(
                            table.column(colIdx).header()
                        )
                        .on('change', function() {
                            table
                                .column(colIdx)
                                .search($(this).val())
                                .draw();

                                var currentSerial = 1;
                                $('#dataTable tbody tr:visible').each(function () {
                                    var index = $(this).index();

                                    $(this).find('td:first').text(currentSerial++);
                                });
                        });
                    select.append($('<option value=""> All </option>'));
                    // Get the search data for the first column and add to the select list
                    table
                        .column(colIdx)
                        .cache('search')
                        .sort()
                        .unique()
                        // .addClass('select2 form-control')
                        .each(function(d) {
                            select.append($('<option value="' + d + '">' + d + '</option>'));
                        });

                });
                $('select').select2();
            }
        });
    }


    // generalize delete




    function handleSuccess(){
        if ($('.yajra-table').length > 0) {
            // If .yajra-table exists, redraw the DataTable
            get_ajax_dataTable();
        } else {
            // Otherwise, fallback to get_ajax_data()
            get_ajax_data();
        }
    }


    var data_table = function() {
        return {
            initialize: function(url, data_columns, filterInputs) {
                $('.yajra-table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    // "responsive": true,
                    "ajax": {
                        url: url,
                        data: function(d) {
                            // Loop through the filterInputs and add their values to the request data
                            filterInputs.forEach(function(input) {
                                d[input.name] = $(input.selector).val();
                            });
                        }
                    },
                    columns: data_columns,
                });
            }
        };
    };

    function get_ajax_dataTable()
    {
        $('.yajra-table').DataTable().draw();
    }



    $('body').on('click', '#delete-user', function() {

        var userURL = $(this).data('url');
        var trObj = $(this);

        if (confirm("Are you sure you want to remove this?") == true) {
            $.ajax({
                url: userURL,
                type: 'DELETE',
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    if (typeof(data.success)=='undefined')
                    {
                        alert(data.catchError);
                        return;
                    }
                    alert(data.success);
                    trObj.parents("tr").remove();
                }
            });
        }

    });


     // Activate Product
     $(document).on('click', '#active-record', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var text = $(this).data('text');
        Swal.fire({
            title: 'Are you sure?',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, activate it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST', // or 'GET', depending on your backend setup
                    data: {
                        _token: '{{ csrf_token() }}' // Include CSRF token for security
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                response.heading,
                                response.success,
                                'success'
                            ).then(() => {
                                // get_ajax_data(); // Reload the page to see the changes
                                handleSuccess();
                            });
                        }
                        else
                        {
                            Swal.fire(
                                'Error!',
                                response.catchError,
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'There was a problem activating.',
                            'error'
                        );
                    }
                });
            }
        });
    });



    // Deactivate Product
    $(document).on('click', '#inactive-record', function(e) {
        e.preventDefault();
        var text = $(this).data('text');
        var url = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, deactivate it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST', // or 'GET', depending on your backend setup
                    data: {
                        _token: '{{ csrf_token() }}' // Include CSRF token for security
                    },
                    success: function(response) {
                        console.log(response);

                        if (response.success) {
                            Swal.fire(
                                response.heading,
                                 response.success,
                                'success'
                            ).then(() => {
                                // get_ajax_data(); // Reload the page to see the changes
                                handleSuccess();
                            });
                        }
                        else{
                            Swal.fire(
                                'Error!',
                                 response.catchError,
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'There was a problem deactivating.',
                            'error'
                        );
                    }
                });
            }
        });
    });


    function get_shop_by_tso() {
        var tso_id = $('#tso_id').val();
        var distributor_id = $('#distribuotr_id').val();

        var o = new Option("select", "");
        $("#shop_id").html(o);

        $.ajax({
            type: "get",
            url: '{{ route('get_shop_by_tso') }}',
            data: {
                tso_id: tso_id,
                distributor_id:distributor_id,
            },
            dataType: 'json',
            success: function(data) {
                $.each(data.shop, function(key, value) {

                    $('#shop_id').append($('<option>', {
                        value: value.id,
                        text: value.company_name
                    }));
                });
                $('#shop_id').select2();
            }
        });
    }

    function get_tso() {
        var distribuotr_id = $('#distribuotr_id').val();
        var o = new Option("select", "");
        $("#tso_id").html(o);

        $.ajax({
            type: "get",
            url: '{{ route('route.GetTsoByDistributor') }}',
            data: {
                distribuotr_id: distribuotr_id
            },
            dataType: 'json',
            success: function(data) {
                $.each(data.tso, function(key, value) {

                    $('#tso_id').append($('<option>', {
                        value: value.id,
                        text: value.name
                    }));
                });
            }
        });
    }
    function get_tso_multi() {
        var distributor_ids = $('#distributor_id').val(); // array of selected IDs
        // console.log(distributor_ids);
        var o = new Option("Select", "");
        $("#tso_id").html(o);

        if (distributor_ids && distributor_ids.length > 0) {
            $.ajax({
                type: "get",
                url: '{{ route('route.GetTsoByDistributorMultiNew') }}',
                data: {
                    distributor_id: distributor_ids // match backend parameter name
                },
                dataType: 'json',
                success: function(data) {
                    $.each(data.tso, function(key, value) {
                        $('#tso_id').append($('<option>', {
                            value: value.id,
                            text: value.name
                        }));
                    });
                }
            });
        }
    }


    function get_tso2(instance) {
        var distribuotr_id = $(instance).closest('tr').find('.distributor_ids').val();
        var o = new Option("select", "");
        // $("#tso_id").html(o);
        $(instance).closest('tr').find('.tso_ids').html(0)

        $.ajax({
            type: "get",
            url: '{{ route('route.GetTsoByDistributor') }}',
            data: {
                distribuotr_id: distribuotr_id
            },
            dataType: 'json',
            success: function(data) {
                $.each(data.tso, function(key, value) {

                    $(instance).closest('tr').find('.tso_ids').append($('<option>', {
                        value: value.id,
                        text: value.name
                    }));
                });
            }
        });
    }

    function get_tso_all(instance) {
        var distribuotr_id = $('#distribuotr_id').val();
        var o = new Option("select", "");
        $("#tso_id").html(o);

        $.ajax({
            type: "get",
            url: '{{ route('route.GetAllTsoByDistributor') }}',
            data: {
                distribuotr_id: distribuotr_id
            },
            dataType: 'json',
            success: function(data) {
                $.each(data.tso, function(key, value) {

                    $('#tso_id').append($('<option>', {
                        value: value.id,
                        text: value.name
                    }));
                });
            }
        });
    }
    function getDistributorByCity()
        {
            city_id = $('#city').val();
            var o = new Option("select", "");
            $("#distribuotr_id").html(o);

            console.log(city_id);

            $.ajax({
                type: "get",
                url: "{{route('distributor.getDistributorByCity')}}",
                data:{city_id:city_id},
                async: true,
                cache: false,
                success: function(data) {
                    $.each(data.distributor, function(key, value) {
                        $('#distribuotr_id').append($('<option>', {
                            value: value.id,
                            text: value.distributor_name
                        }));
                    });
                }
            });
        }


    function get_tso_by_multiple_dis() {
        var distribuotr_id = $('#distribuotr_id').val();
        var o = new Option("select", "");
        $("#tso_id").html(o);

        $.ajax({
            type: "get",
            url: '{{ route('route.GetTsoByMultipleDistributor') }}',
            data: {
                distribuotr_id: distribuotr_id
            },
            dataType: 'json',
            success: function(data) {
                $.each(data.tso, function(key, value) {

                    $('#tso_id').append($('<option>', {
                        value: value.id,
                        text: value.name
                    }));
                });
            }
        });
    }



    // function get_route_by_tso() {
    //     var tso_id = $('#tso_id').val();
    //     var o = new Option("select", "");
    //     $("#route_id").html(o);

    //     $.ajax({
    //         type: "get",
    //         url: '{{ route('route.GetRouteBYTSO') }}',
    //         data: {
    //             tso_id: tso_id
    //         },
    //         dataType: 'json',
    //         success: function(data) {
    //             console.log(data.route);
    //             $.each(data.route, function(key, value) {

    //                 $('#route_id').append($('<option>', {
    //                     value: value.id,
    //                     text: value.route_name
    //                 }));
    //             });
    //         }
    //     });
    // }

    function get_route_by_tso() {
        var tso_id = $('#tso_id').val();
        var o = new Option("select", "");
        $("#route_id").html(o);

        $.ajax({
            type: "get",
            url: '{{ route('route.GetRouteBYTSO') }}',
            data: {
                tso_id: tso_id
            },
            dataType: 'json',
            success: function(data) {
                console.log(data.route);
                $.each(data.route, function(key, value) {

                    $('#route_id').append($('<option>', {
                        value: value.id,
                        text: value.route_name
                    }));
                });
            }
        });
    }

    function get_sub_routes() {
        var route_id = $('#route_id').val();
        var o = new Option("select", "");
        $("#sub_route_id").html(o);

        $.ajax({
            type: "get",
            url: '{{ route('route.get_sub_route') }}',
            data: {
                route_id: route_id
            },
            dataType: 'json',
            success: function(data) {

                console.log(data.route);
                $.each(data.route, function(key, value) {

                    $('#sub_route_id').append($('<option>', {
                        value: value.id,
                        text: value.name
                    }));
                });
            }
        });
    }

    function get_shop_by_route() {
        var route_id = $('#route_id').val();
        var o = new Option("select", "");
        $("#shop_id").html(o);

        $.ajax({
            type: "get",
            url: '{{ route('get_shop_by_route') }}',
            data: {
                route_id: route_id
            },
            dataType: 'json',
            success: function(data) {


                $.each(data.shop, function(key, value) {

                    $('#shop_id').append($('<option>', {
                        value: value.id,
                        text: value.company_name
                    }));
                });
            }
        });
    }

    jQuery(document).ready(function($) {

        //Use this inside your document ready jQuery
        $(window).on('popstate', function() {
            location.reload(true);
        });


        $(".checkbox").change(function() {
            if (this.checked) {

                $('.checkMeOut').prop('checked', true);
            } else {
                $('.checkMeOut').prop('checked', false);
            }
        });

    });

    $(document).ready(function () {
    $('.nav-item').each(function () {
        var $subMenu = $(this).find('.menu-content');
        if ($subMenu.children().length === 0) {
            $(this).hide();
        }
    });
});
</script>


<script>



//  Proflie Upload

function previewFile() {
    var preview = document.querySelector('img');
    var preview2 = document.getElementById('profile-image2');
    var file    = document.querySelector('input[type=file]').files[0];
    var reader  = new FileReader();


    reader.addEventListener("load", function () {
      preview.src = reader.result;
      preview2.src = reader.result;
    }, false);

    if (file) {
      reader.readAsDataURL(file);
    }
    console.log(file);
    var form_file = new FormData();
    form_file.append('image', file);

    $.ajax({
        url: "{{url('users/upload_profile')}}",
        type: 'post',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data  : form_file,
        contentType: 'multipart/form-data',
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            alert(data.message);

        }
    });
  }
                        $(function() {
              $('#profile-image1').on('click', function() {
                  $('#profile-image-upload').click();
              });
          });


        //   Time
        var today = new Date();
        var day = today.getDate();
        var month = today.getMonth() + 1;

        function appendZero(value) {
            return "0" + value;
        }

        function theTime() {
            var d = new Date();
            document.getElementById("time").innerHTML = d.toLocaleTimeString("en-US");
        }

        if (day < 10) {
            day = appendZero(day);
        }

        if (month < 10) {
            month = appendZero(month);
        }

        today = day + "/" + month + "/" + today.getFullYear();

        document.getElementById("date").innerHTML = today;

        var myVar = setInterval(function () {
            theTime();
        }, 1000);



</script>
