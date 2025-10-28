@php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'Report Center')
@section('content')
<style> /* Main Container Styles */
 .report-container{max-width:1200px;margin:0 auto;padding:20px;}
.report-container h1{color:#2c3e50;font-size:28px;margin-bottom:30px;padding-bottom:10px;border-bottom:2px solid #f05a2b;}
/* Card Styles */
 .report-card{background-color:white;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-bottom:30px;overflow:hidden;}
/* Accordion Styles */
 .accordion-item active{border-bottom:1px solid #e0e0e0;}
.accordion-item active:last-child{border-bottom:none;}
.accordion-header{padding:15px 20px;background-color:#f8f9fa;cursor:pointer;display:flex;align-items:center;justify-content:space-between;transition:all 0.3s ease;}
.accordion-header:hover{background-color:#fbece8;}
.accordion-header.active{background-color:#f05a2b;color:white;}
.accordion-header.active .accordion-icon{transform:rotate(180deg);color:white;}
.accordion-title{display:flex;align-items:center;font-weight:500;}
.accordion-icon{transition:transform 0.3s ease;font-size:0.9rem;}
.accordion-content{max-height:0;overflow:hidden;transition:max-height 0.3s ease;}
.accordion-content-inner{padding:15px 20px;}
.accordion-content{max-height:none !important;}
.accordion-item .accordion-content{max-height:none;}
/* Report List Styles */
 .report-list{list-style:none;display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:10px;}
.report-list li a{display:flex;align-items:center;padding:10px 15px;color:#4a4a4a;text-decoration:none;border-radius:4px;transition:all 0.2s ease;}
.report-list li a:hover{background-color:#fdf2ef;color:#f05a2b;}
.report-list li i{margin-right:10px;font-size:0.7rem;color:#f05a2b;}
/* Responsive Adjustments */
 @media (max-width:768px){.report-list{grid-template-columns:1fr;}
.accordion-header{padding:12px 15px;}
}
.accordion-title {font-weight: bold !important; color: #4a4a4a !important; font-size: 16px !important;}
.report-list li a {color: #4a4a4a !important; font-size: 16px !important; font-weight: bold !important;}
</style>

<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="report-container">
                        <h1>REPORTS</h1>
                        
                        <div class="report-card">
                            <!-- Sales Reports -->
                            @canany(['Order_Summary_Report', 'Order_List_Report', 'item_wise_sales', 'book_wise_sale', 'category_wise_sale'])
                            <div class="accordion-item active">
                                <div class="accordion-header">
                                    <div class="accordion-title">
                                        <i class="fas fa-chart-line mr-2"></i>
                                        <span>Sales Reports</span>
                                    </div>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </div>
                                <div class="accordion-content">
                                    <div class="accordion-content-inner">
                                        <ul class="report-list">
                                            @can('Order_Summary_Report')
                                            <li>
                                                <a href="{{ route('order_summary') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Order Summary</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('Order_List_Report')
                                            <li>
                                                <a href="{{ route('order_list') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Order List</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('item_wise_sales')
                                            <li>
                                                <a href="{{ route('item_wise_sale') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Item Wise Sales</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('book_wise_sale')
                                            <li>
                                                <a href="{{ route('book_wise_sale') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Booker Wise Sales</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('category_wise_sale')
                                            <li>
                                                <a href="{{ route('category_wise_sale') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Category Wise Sales</span>
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endcanany
                            
                            <!-- Financial Reports -->
                            @canany(['Receipt_Voucher_Summary_Report', 'shop_ledger_Report', 'shop_cash_memo_report'])
                            <div class="accordion-item active">
                                <div class="accordion-header">
                                    <div class="accordion-title">
                                        <i class="fas fa-money-bill-wave mr-2"></i>
                                        <span>Financial Reports</span>
                                    </div>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </div>
                                <div class="accordion-content">
                                    <div class="accordion-content-inner">
                                        <ul class="report-list">
                                            @can('Receipt_Voucher_Summary_Report')
                                            <li>
                                                <a href="{{ route('receipt_voucher_summary') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Receipt Voucher</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('shop_ledger_Report')
                                            <li>
                                                <a href="{{ route('shop_ledger_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Shop Ledger</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('shop_cash_memo_report')
                                            <li>
                                                <a href="{{ route('shop_cash_memo_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Shop Cash Memo</span>
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endcanany
                            
                            <!-- Inventory Reports -->
                            @canany(['stock_report', 'racks_report', 'Product_Availability', 'Load_Sheet_Report'])
                            <div class="accordion-item active">
                                <div class="accordion-header">
                                    <div class="accordion-title">
                                        <i class="fas fa-boxes mr-2"></i>
                                        <span>Inventory Reports</span>
                                    </div>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </div>
                                <div class="accordion-content">
                                    <div class="accordion-content-inner">
                                        <ul class="report-list">
                                            @can('stock_report')
                                            <li>
                                                <a href="{{ route('stock_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Stock Details</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('stock_report_new')
                                            <li>
                                                <a href="{{ route('stock_report_new') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Stock Details Report (New)</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('racks_report')
                                            <li>
                                                <a href="{{ route('racks.report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Racks Report</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('Product_Availability')
                                            <li>
                                                <a href="{{ route('product_avail') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Product Availability</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('Load_Sheet_Report')
                                            <li>
                                                <a href="{{ route('load_Sheet') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Load Sheet</span>
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endcanany
                            
                            <!-- Activity Reports -->
                            @canany(['TSO_Activity', 'shop_visit', 'Order_VS_Execution', 'TSO_Target_Sheet'])
                            <div class="accordion-item active">
                                <div class="accordion-header">
                                    <div class="accordion-title">
                                        <i class="fas fa-user-check mr-2"></i>
                                        <span>Activity Reports</span>
                                    </div>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </div>
                                <div class="accordion-content">
                                    <div class="accordion-content-inner">
                                        <ul class="report-list">
                                            @can('TSO_Activity')
                                            <li>
                                                <a href="{{ route('activity') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Order Booker Activity</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('shop_visit')
                                            <li>
                                                <a href="{{ route('shop.shopVisitList') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Shop Visit/Merchandising</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('Order_VS_Execution')
                                            <li>
                                                <a href="{{ route('order_vs_execution') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Order vs Execution</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('TSO_Target_Sheet')
                                            <li>
                                                <a href="{{ route('tso_target') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Order Booker Target</span>
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endcanany
                            
                            <!-- Product Reports -->
                            @canany(['Scheme_Product_Report', 'Product_Productivity'])
                            <div class="accordion-item active">
                                <div class="accordion-header">
                                    <div class="accordion-title">
                                        <i class="fas fa-tags mr-2"></i>
                                        <span>Product Reports</span>
                                    </div>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </div>
                                <div class="accordion-content">
                                    <div class="accordion-content-inner">
                                        <ul class="report-list">
                                            @can('Scheme_Product_Report')
                                            <li>
                                                <a href="{{ route('scheme_product') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Scheme Products</span>
                                                </a>
                                            </li>
                                            @endcan
                                            @can('Product_Productivity')
                                            <li>
                                                <a href="{{ route('product_productivity') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Product Productivity</span>
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endcanany
                            
                            <!-- Attendance Reports -->
                            @canany(['Attendence_Report'])
                            <div class="accordion-item active">
                                <div class="accordion-header">
                                    <div class="accordion-title">
                                        <i class="fas fa-calendar-check mr-2"></i>
                                        <span>Attendance Reports</span>
                                    </div>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </div>
                                <div class="accordion-content">
                                    <div class="accordion-content-inner">
                                        <ul class="report-list">
                                            @can('Attendence_Report')
                                            <li>
                                                <a href="{{ route('attendence_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Attendance Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('day_wise_attendence_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Day Wise Attendance</span>
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endcanany

                             <!-- Report Center -->
                            @canany(['Attendence_Report'])
                            <div class="accordion-item active">
                                <div class="accordion-header">
                                    <div class="accordion-title">
                                        <i class="fas fa-calendar-check mr-2"></i>
                                        <span>New Reports</span>
                                    </div>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </div>
                                <div class="accordion-content">
                                    <div class="accordion-content-inner">
                                        <ul class="report-list">
                                            @can('Attendence_Report')
                                            <li>
                                                <a href="{{ route('brand_wise_daily_sale') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Brand wise Daily Sale</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('brand_distributer_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Brand Distributer Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('booking_vs_execution') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Order vs Execution</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('order_vs_execution_product_wise') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Order vs Execution Product wise</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('distributer_product_sales_value_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Distributer Product Sales Value Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('daily_booking_unit_summary') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Daily Booking Unit Summary</span>
                                                </a>
                                            </li>
                                             <li>
                                                <a href="{{ route('non_dispatch_order_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Non Dispatch Order Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('product_wise_sale') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Product Wise Sale</span>
                                                </a>
                                            </li>
                                             <li>
                                                <a href="{{ route('cancelled_orders_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Tso Cancelled Orders Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('unproductive_shop_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Unproductive Shop Report</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('app_version_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>App version  Report</span>
                                                </a>
                                            </li>
                                             <li>
                                                <a href="{{ route('order_booker_target_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Order Booker Target Report</span>
                                                </a>
                                            </li>
                                             <li>
                                                <a href="{{ route('tso_sales_return_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>TSO Sales Return Report</span>
                                                </a>
                                            </li>
                                             <li>
                                                <a href="{{ route('shop_wise_sales_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Shop Wise Sales Report</span>
                                                </a>
                                            </li>
                                             <li>
                                                <a href="{{ route('order_booker_daily_activity_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Order Booker Daily Activity Report</span>
                                                </a>
                                            </li>
                                             <li>
                                                <a href="{{ route('order_booker_daily_activity_location_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Order Booker Daily Activity Timestamp Report</span>
                                                </a>
                                            </li>
                                             <li>
                                                <a href="{{ route('sales_return_report') }}">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Sales Return Report</span>
                                                </a>
                                            </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endcanany
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accordionItems = document.querySelectorAll('.accordion-item');
    
    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        
        header.addEventListener('click', function() {
            accordionItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                }
            });
            item.classList.toggle('active');
        });
    });

    if (document.querySelectorAll('.accordion-item.active').length === 0 && accordionItems.length > 0) {
        accordionItems[0].classList.add('active');
    }
});
</script>

@endsection