
<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>

    <?php echo MasterFormsHelper::PrintHead($from,$to,'Load Sheet',$tso_id);?>

@if(count($so_data)>0)
@php
$i =1;
$total = 0;
$total_qty = 0;
$grand_total_qty = [];
@endphp
<div class="row">
<div class="col-1 offset-11">
    <a target="_blank" style="
    background: #cde4f1;"   data-url="{{ route('report.loadSheet', [$from, $to, $tso_id ?? 0, $distributor_id ?? 0, $execution ?? 0]) }}"  data-title="View Load Sheet" class="dropdown-item_sale_order_list dropdown-item launcher">View</a>
</div>
</div>
<br/>
<table class="table table-bordered" >
    <thead>
    <tr>

        <th style="text-align: center;">Sr No</th>
        <th style="text-align: center;">Product Name</th>
        <th style="text-align: center;">Flavour Name</th>
        <th style="text-align: center;">Total Sale Unit</th>
        <th style="text-align: center;">T.P Rate</th>
        <th style="text-align: center;">Total Amount</th>
    </tr>
    </thead>
<tbody >
@foreach($so_data as $data)
@php
    // dump($execution , $data->excecution);
    $get_qty = '';
    $product_price = '';
    foreach (MasterFormsHelper::get_product_price($data->product_id) as $k => $productPrice) {
        // dump($data->product_id, $data->flavour_id , $productPrice->uom_id , $tso_id , $distributor_id , $execution);
        $qty = MasterFormsHelper::get_sale_qty($from , $to , $data->product_id, $data->flavour_id , $productPrice->uom_id , $tso_id , $distributor_id , $execution);

        $uom_name = $master->uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
        if ($qty > 0) {
            $get_qty .= ($get_qty ? ' , ' : '') . number_format($qty);
            $grand_total_qty[$productPrice->uom_id] = isset($grand_total_qty[$productPrice->uom_id]) ? $grand_total_qty[$productPrice->uom_id]+$qty : $qty;
        }
        // dump($qty ,$stock->product->id, $stock->flavour_id , $productPrice->uom_id ,Request::get('distributor_id'));
        $product_price .= ($product_price ? ' , ' : '') . number_format($productPrice->trade_price , 2);

    }
@endphp
<tr>

    <td style="text-align: center;">{{$i}}</td>
    <td style="text-align: center;">{{$data->product_name}}</td>
    <td style="text-align: center;">{{ MasterFormsHelper::get_flavour_name($data->flavour_id) ?? '--'}}</td>
    <td style="text-align: center;">{{$get_qty ?? '--'}}</td>
    <td style="text-align: center;">{{$product_price}}</td>
    {{-- <td style="text-align: center;">{{$data->qty_summary}}</td> --}}
    <td style="text-align: center;">{{number_format($data->amount , 2)}}</td>
</tr>
@php
$i++;
$total_qty += $data->qty;
$total += $data->amount;
@endphp
@endforeach

<tr>
    <td colspan="3">Total</td>
    @php
        $total_qty_sum = array_sum($grand_total_qty);
    @endphp
    <td style="text-align: center;">{{ number_format($total_qty_sum) }}</td>
    <td style="text-align: center;"></td>
    <td style="text-align: center;">{{ number_format($total, 2) }}</td>
</tr>

</tbody>
</table>
@else
<table class="table table-bordered" >
<tr>
    <td colspan="5" style="background:rgb(255, 170, 170) "> No Record Found</td>
</tr>
</table>

@endif
@if(count($summary_data) > 0)
    <hr>
    <h4>Order Summary</h4>
    <table class="table table-bordered">
     <thead>
    <tr>
        <th style="text-align: center;">S#</th>
        <th style="text-align: center;">Invoice No</th>
        <th style="text-align: center;">Date</th>
        <th style="text-align: center;">Shop Name</th>
       <th style="text-align: center;">Order Booker Name</th>
        <th style="text-align: center;">Total Order Qty</th>
        <th style="text-align: center;">Amount</th>
    </tr>
</thead>
<tbody>
    @php
        $s = 1;
        $summary_total_qty = 0;
        $summary_total_amount = 0;
    @endphp
    @foreach($summary_data as $row)
        <tr>
            <td style="text-align: center;">{{ $s++ }}</td>
            <td style="text-align: center;">{{ $row->invoice_no }}</td>
            <td style="text-align: center;">{{ \Carbon\Carbon::parse($row->dc_date)->format('d-M-Y') }}</td>
            <td style="text-align: center;">{{ $row->company_name }}</td>
            <td style="text-align: center;">{{ $row->tso_name }}</td>
            <td style="text-align: center;">{{ number_format($row->total_qty) }}</td>
            <td style="text-align: center;">{{ number_format($row->total_amount, 2) }}</td>
        </tr>
        @php
            $summary_total_qty += $row->total_qty;
            $summary_total_amount += $row->total_amount;
        @endphp
    @endforeach
    <tr style="background:#f2f2f2">
        <th colspan="5">Grand Total</th>
        <th style="text-align: center;">{{ number_format($summary_total_qty) }}</th>
        <th style="text-align: center;">{{ number_format($summary_total_amount, 2) }}</th>
    </tr>
</tbody>

    </table>
@endif

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