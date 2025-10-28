<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.compatibility')
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <title>{{ env('APP_NAME') }} | @yield('title')</title>
    @include('partials.style')
    @yield('css-end')

    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" /> --}}

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.9/css/fixedHeader.dataTables.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- datatable --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

</head>


<script>
        function updateTime() {
            var now = new Date();

            // Time
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // 12-hour clock
            minutes = minutes < 10 ? '0' + minutes : minutes;
            var timeString = "<h3>" + hours + ':' + minutes + ' <span>' + ampm + "</span></h3>";

            // Date
            var options = { year: 'numeric', month: 'long', day: 'numeric' };
            var dateString = now.toLocaleDateString('en-US', options);
            var dateElement = "<p class='date'>" + dateString + "</p>";

            // Update the HTML
            document.querySelector('.tim').innerHTML = timeString + dateElement;
        }

        // Update the time every minute
        document.addEventListener("DOMContentLoaded", function() {
            updateTime(); // Call the function initially to set the time and date

            setInterval(function() {
                updateTime();
            }, 60000);
        });
    </script>



<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click"
    data-menu="vertical-menu-modern" data-col="">
    @include('partials.header')
    @include('partials.sidebar')

    <!-- BEGIN: Content-->
    <div class="app-content content  ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="content-body">
                    <!-- Basic multiple Column Form section start -->
                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul></ul>
                    </div>

                    <div class="alert alert-success">
                        <ul>
                            @if (Session::has('success'))
                                {{ Session::get('success') }}
                            @endif
                        </ul>
                    </div>

                    <div clas="loader_bar">
                    <img id="loader" style="display:none;"  src="{{ url('public/assets/images/loader.gif') }}" alt="Italian Trulli">
                    </div>
                    @yield('content')
                    <!-- Basic Floating Label Form section end -->
                </div>
            </div>
        </div>
    </div>


    <!-- END: Content-->
    @include('partials.footer')
    @include('partials.scripts')
    @yield('script')

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;
        const userId = '{{ auth()->user()->id }}';

        var pusher = new Pusher('0afdf658eccf3c0e20bd', {
        cluster: 'ap2'
        });

        var channel = pusher.subscribe('notification');
        channel.bind('RequestNotificationEvent', function(event) {
            // alert(JSON.stringify(event));
            // event = JSON.stringify(event) // after stringify condition not working
            if (userId == event.id) {
                html = `
                <li>
                    <a href="${event.url}" class="font-weight-bold"> ${event.message} <span>Just Now</span>
                    </a>
                </li>
                `;
                $('.notification-data').prepend(html);
            }
        });
    </script>


    {{-- web sockets --}}
    {{-- <script src="{{url('public/js/app.js')}}"></script>
    <script>

        const userId = '{{ auth()->user()->id }}';



        window.Echo.channel('notification.'+userId) // Listen to the user's channel
            .listen('RequestNotificationEvent', (event) => {
                console.log(event);
                if (userId == event.id) {
                    html = `
                    <li>
                        <a href="${event.url}" class="font-weight-bold"> ${event.message} <span>Just Now</span>
                        </a>
                    </li>
                    `;
                    $('.notification-data').prepend(html);
                }
            });




        // window.Echo.private('notification.'+userId) // Listen to the user's channel
        //     .listen('RequestNotificationEvent', (event) => {
        //         console.log(event);
        //         // Handle the event, show a notification, etc.
        //         // You can update the DOM here with the new notification
        //         alert('New Notification notification: ' + event.message);
        //     });
    </script> --}}



    {{-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.9/js/dataTables.fixedHeader.min.js"></script>

<!-- Select2 CSS -->
{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}

<!-- jQuery load karo pehle -->





    <script src="{{ URL::asset('assets/js/custom.js') }}"></script>

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    {{-- <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> --}}
    <script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.all.min.js"></script>

    <script>
        // Aliiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
        // Add class in table tag filterTable and for filter dates-info-head class will be using :)
        function exportBtnWithFilters(fileName) {
            var clonedTable = $('.filterTable').clone();
            clonedTable.find('.export-hidden').remove();
            clonedTable.find('thead select').remove();
            clonedTable.find('thead span').remove();
            var headerHtml = $('.dates-info-head').html() || '';
            var colCount = clonedTable.find('thead tr:first th').length;
            var finalTable = $(`
                <table>
                    <thead>
                        <tr>
                            <th colspan="${colCount}" style="text-align: center; vertical-align: middle;">${headerHtml}</th>
                        </tr>
                        <tr><td colspan="${colCount}" style="text-align: center; vertical-align: middle;"></td></tr>
                    </thead>
                    <tbody>
                        ${clonedTable.html()}
                    </tbody>
                </table>
            `);
            $(finalTable).table2excel({
                filename: fileName + '_' + '{{ date("d-M-Y H:i:s") }}',
                sheetName: "Sheet1",
                preserveColors: true
            });
        }
        function printTableNew(tableId) {
            var printContents = document.querySelector(tableId).outerHTML;
            var win = window.open('', '', 'height=700,width=900');

            win.document.write('<html><head><title>Print</title>');

            win.document.write('<link rel="stylesheet" href="/css/app.css">');

            win.document.write(`
                <style>
                @media print {
                    @page { size: A4 portrait; margin: 10mm; }

                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                    }

                    .dates-info-head {
                        text-align: center;
                        margin-bottom: 15px;
                    }

                    .dates-info-head p {
                        text-align: center;
                        margin: 0;
                    }

                    /* Table styling */
                    table { width: 100%; border-collapse: collapse; page-break-inside: auto; }
                    table, th, td { border: 1px solid black; }
                    tr { page-break-inside: avoid; page-break-after: auto; }

                    /* Optional: repeat header on each page */
                    thead { display: table-header-group; }
                    tfoot { display: table-footer-group; }
                }
                </style>
                `);


            win.document.write('</head><body>');
            win.document.write(printContents);
            win.document.write('</body></html>');

            win.document.close();
            win.focus();
            win.print();
            win.close();
        }
        // Aliiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
        function exportBtn(fileName) {
            var clonedTable = $('#dataTable').clone();

            // Remove the hidden columns from the cloned table
            clonedTable.find('.export-hidden').remove();

            // Remove filter select elements from the header
            clonedTable.find('thead select').remove();
            clonedTable.find('thead span').remove();

            // Export the modified table
            clonedTable.table2excel({
                filename: fileName+'_'+'{{date("d-M-Y H:i:s")}}',
                sheetName: "Sheet1"
            });
        }

        function exportBtnn(fileName,table) {

            var clonedTable = $('#'+table).clone();

            // Remove the hidden columns from the cloned table
            clonedTable.find('.export-hidden').remove();



            // Export the modified table
            clonedTable.table2excel({
                filename: fileName+'_'+'{{date("d-M-Y H:i:s")}}',
                sheetName: "Sheet1"
            });
        }

        function exportExcelBtn(fileName , url)
        {
            var form = $('#list_data');
            query = form.serialize();

            window.location.href = url+'?' + query;
        }
    </script>


</body>

</html>
