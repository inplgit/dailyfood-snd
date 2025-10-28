<?php
use App\Models\ShopVisit;
use App\Models\SaleOrder;
use App\Models\ReceiptVoucher;
use App\Models\Route;
use App\Models\Shop;
use App\Models\UsersLocation;
use App\Helpers\MasterFormsHelper;
use Carbon\Carbon;

$master = new MasterFormsHelper();
$user_allocate = $master->get_assign_user()->toArray();

?>

<style>
    .sno {
        width: 70px;
    }

    .shop {
        width: 250px;
    }

    .date {
        width: 130px;
    }

    .ref {
        width: 130px;
    }

    .type {
        width: 90px;
    }

    .memo {
        width: 200px;
    }

    .debit {
        width: 130px;
    }

    .credit {
        width: 130px;
    }

    .balance {
        width: 130px;
    }
</style>

<div class="container-fluid">
    <br />
    <div class="row">
        <div class="col-sm-12 text-center">
            <h3>Shop Ledger Report</h3>
            @if ($from_date != '' && $to_date != '')
                <h5>{{ date('d-M-Y', strtotime($from_date)) }} to {{ date('d-M-Y', strtotime($to_date)) }}</h5>
            @endif
        </div>
    </div>
    <div class="table-responsive">
        <table style="margin-top:20px;" id="" class="table table-bordered table-stripped">
            <thead>
                <tr>
                    <th class="sno">S.No</th>
                    <th class="shop">Shop</th>
                    <th class="date">Date</th>
                    <th class="ref">Ref</th>
                    <th class="type">Type</th>
                    <th class="memo">Memo</th>
                    <th class="debit">Debit</th>
                    <th class="credit">Credit</th>
                    <th class="return">Return</th>
                    <th class="balance">Balance</th>
                </tr>
            </thead>
        </table>

        <?php

$i = 1;
$total_debit = 0;
$total_credit = 0;
$total_balance = 0;


foreach ($shops as $shop) {

    $filteredData = $saleOrders->filter(function ($item) use ($shop) {
        return $item->shop_id == $shop->id;
    });
    $filteredData2 = $receiptVouchers->filter(function ($item) use ($shop) {
        return $item->shop_id == $shop->id;
    });

    $shop_td=0;
    $rowspan = max(1, count($filteredData) + count($filteredData2));

    // previous opening balance from date
    $oldSaleOrderAmount = 0;
    $oldReceiptVouchersAmount = 0;
    $oldOpeningBalance = 0;
    if ($from_date) {
        $oldSaleOrderAmount = DB::table('sale_orders')->where('dc_date', '<', $from_date)->where(['shop_id' => $shop->id , 'excecution' => 1])->sum('total_amount');
        $oldReceiptVouchersAmount = DB::table('receipt_vouchers')->where('issue_date', '<', $from_date)->where(['shop_id' => $shop->id , 'execution' => 1])->sum('amount');
        $oldOpeningBalance = $oldSaleOrderAmount - $oldReceiptVouchersAmount;
    }
    // dump($oldSaleOrderAmount , $oldReceiptVouchersAmount , $oldOpeningBalance);


    if( $shop->balance_amount > 0 || count($filteredData) > 0 || count($filteredData2) > 0){
        ?>
        <table style="margin-top:20px;" id="" class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <td rowspan="{{ $rowspan + 1 }}" class="sno"><?php echo $i++; ?></td>
                    <td rowspan="{{ $rowspan + 1 }}" class="shop"><?php echo $shop->company_name; ?></td>
                    <td colspan="4">Opening Balance </td>
                  
                    @if ($shop->debit_credit == 1)
                        <td>{{ number_format($shop->balance_amount, 2) }}</td>
                        <td></td>
                        @php
                            $total_debit += $shop->balance_amount;
                        @endphp
                    @else
                        <td></td>
                        <td>{{ number_format($shop->balance_amount, 2) }}</td>
                        @php
                            $total_credit += $shop->balance_amount;
                        @endphp
                    @endif
                    <td>-</td>
                    <td>{{ number_format($shop->balance_amount, 2) }} </td>
                </tr>

                <?php
    }

    foreach ($filteredData as $key => $item) {
        ?>
                @if ($shop_td == 0)
                    {{-- <tr>
                        <td rowspan="{{ $rowspan + 1 }}" class="sno"><?php echo $i++; ?></td>
                        <td rowspan="{{ $rowspan + 1 }}" class="shop"><?php echo $shop->company_name; ?></td>
                        <td colspan="4">Opening Balance </td>
                        @if ($shop->debit_credit == 1)
                            <td>{{ number_format($shop->balance_amount, 2) }}</td>
                            <td></td>
                            @php
                                $total_debit += $shop->balance_amount;
                            @endphp
                        @else
                            <td></td>
                            <td>{{ number_format($shop->balance_amount, 2) }}</td>
                            @php
                                $total_credit += $shop->balance_amount;
                            @endphp
                        @endif
                        <td>{{ number_format($shop->balance_amount, 2) }} </td>
                    </tr> --}}

                @endif
                <tr>
                    <td class="date"><?php echo $item->dc_date; ?></td>
                    <td class="ref"><?php echo $item->invoice_no; ?></td>
                    <td class="type">SO</td>
                    <td class="memo"><?php echo $item->notes; ?></td>
                    <td class="debit"><?php echo $item->total_amount ; ?></td>
                    <td class="credit"></td>
                    <td class="return"><?php echo  $master->get_sale_return_amount($item->invoice_no); ?></td>
                    <td class="balance"><?php echo $item->total_amount - $master->get_sale_return_amount($item->invoice_no); ?></td>
                </tr>
                <?php
        $shop_td++;
        $total_debit += $item->total_amount - $master->get_sale_return_amount($item->invoice_no);
    }


    foreach ($filteredData2 as $key => $item) {
        ?>
                @if ($shop_td == 0)
                    {{-- <tr>
                        <td rowspan="{{ $rowspan + 1 }}" class="sno"><?php echo $i++; ?></td>
                        <td rowspan="{{ $rowspan + 1 }}" class="shop"><?php echo $shop->company_name; ?></td>
                        <td colspan="4">Opening Balance </td>
                        @if ($shop->debit_credit == 1)
                            <td>{{ number_format($shop->balance_amount, 2) }}</td>
                            <td></td>
                            @php
                                $total_debit += $shop->balance_amount;
                            @endphp
                        @else
                            <td></td>
                            <td>{{ number_format($shop->balance_amount, 2) }}</td>
                            @php
                                $total_credit += $shop->balance_amount;
                            @endphp
                        @endif
                        <td>{{ number_format($shop->balance_amount, 2) }} </td>
                    </tr> --}}
                @endif
                <tr>
                    <td class="date"><?php echo $item->issue_date; ?></td>
                    <td class="ref"><?php echo $item->rec_id; ?></td>
                    <td class="type">RV</td>
                    <td class="memo"><?php echo $item->detail; ?></td>
                    <td class="debit"></td>
                    <td class="credit"><?php echo number_format($item->amount, 2); ?></td>
                    <td class="return"><?php echo  $master->get_sale_return_amount($item->invoice_no); ?></td>
                    <td class="balance"><?php echo $item->total_amount - $master->get_sale_return_amount($item->invoice_no); ?></td>
                </tr>
                <?php
        $shop_td++;
        $total_credit += $item->amount - $master->get_sale_return_amount($item->invoice_no);
    }

    if(count($filteredData) > 0 || count($filteredData2) > 0){
        ?>
            </tbody>
        </table> <?php
    }

    $total_balance += ($total_debit - $total_credit);
}
?>
        <table style="margin-top:20px;" id="" class="table table-bordered">
            <tbody>
                <tr style="background-color: darkgray;font-weight: bold" class="bold">
                    <td colspan="7">Total</td>

                    <td class="debit"><?php echo number_format($total_debit, 2); ?></td>
                    <td class="credit"><?php echo number_format($total_credit, 2); ?></td>
                    <td class="balance"><?php echo number_format($total_debit - $total_credit, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
