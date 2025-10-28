@extends('layouts.master')
@section('title', 'Dashboard')
@section('content')
<section id="apexchart">
    <?php
        use App\Models\UserAttendence;
        $attendence = UserAttendence::where(['user_id'=>Auth::user()->id,'date'=>date('Y-m-d')])->first();
    ?>
    <style>
        .overview-wrap{display:-webkit-box;display:-webkit-flex;display:-moz-box;display:-ms-flexbox;display:flex;-webkit-box-pack:justify;-webkit-justify-content:space-between;-moz-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;-webkit-box-align:center;-webkit-align-items:center;-moz-box-align:center;-ms-flex-align:center;align-items:center}
        @media(max-width:767px){.overview-wrap{-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;-moz-box-orient:vertical;-moz-box-direction:normal;-ms-flex-direction:column;flex-direction:column}
        .overview-wrap .button{-webkit-box-ordinal-group:2;-webkit-order:1;-moz-box-ordinal-group:2;-ms-flex-order:1;order:1}
        .overview-wrap h2{-webkit-box-ordinal-group:3;-webkit-order:2;-moz-box-ordinal-group:3;-ms-flex-order:2;order:2}
        }
        .overview-item{-webkit-border-radius:10px;-moz-border-radius:10px;border-radius:10px;padding:30px;/* padding-bottom:0;*/
        -webkit-box-shadow:0 2px 5px 0 rgba(0,0,0,.1);-moz-box-shadow:0 2px 5px 0 rgba(0,0,0,.1);box-shadow:0 2px 5px 0 rgba(0,0,0,.1);margin-bottom:40px}
        @media(min-width:992px) and (max-width:1519px){.overview-item{padding-left:15px;padding-right:15px}
        }
        .overview-item--c1{background-image:-moz-linear-gradient(90deg,#3f5efb 0%,#fc466b 100%);background-image:-webkit-linear-gradient(90deg,#3f5efb 0%,#fc466b 100%);background-image:-ms-linear-gradient(90deg,#3f5efb 0%,#fc466b 100%)}
        .overview-item--c2{background-image:-moz-linear-gradient(90deg,#11998e 0%,#38ef7d 100%);background-image:-webkit-linear-gradient(90deg,#11998e 0%,#38ef7d 100%);background-image:-ms-linear-gradient(90deg,#11998e 0%,#38ef7d 100%)}
        .overview-item--c3{background-image:-moz-linear-gradient(90deg,#ee0979 0%,#ff6a00 100%);background-image:-webkit-linear-gradient(90deg,#ee0979 0%,#ff6a00 100%);background-image:-ms-linear-gradient(90deg,#ee0979 0%,#ff6a00 100%)}
        .overview-item--c4{background-image:-moz-linear-gradient(90deg,#45b649 0%,#dce35b 100%);background-image:-webkit-linear-gradient(90deg,#45b649 0%,#dce35b 100%);background-image:-ms-linear-gradient(90deg,#45b649 0%,#dce35b 100%)}
        .overview-box .icon{display:inline-block;vertical-align:top;margin-right:15px}
        .overview-box .icon i{font-size:37px;color:#fff}
        @media(min-width:992px) and (max-width:1199px){.overview-box .icon{margin-right:3px}
        .overview-box .icon i{font-size:30px}
        }
        @media(max-width:991px){.overview-box .icon{font-size:46px}
        }
        .overview-box .text{font-weight:300;display:inline-block}
        .overview-box .text h2{font-weight:300;color:#fff;font-size:36px;line-height:1;margin-bottom:5px}
        .overview-box .text span{font-size:18px;color:rgba(255,255,255,.6)}
        @media(min-width:992px) and (max-width:1199px){.overview-box .text{display:inline-block}
        .overview-box .text h2{font-size:20px;margin-bottom:0}
        .overview-box .text span{font-size:14px}
        }
        @media(max-width:991px){.overview-box .text h2{font-size:26px}
        .overview-box .text span{font-size:15px}
        }
        .overview-chart{height:115px;position:relative}
        .overview-chart canvas{width:100%}
        .feather{font-family:feather!important;speak:none;font-style:normal;font-weight:400;font-variant:normal;text-transform:none;line-height:1;-webkit-font-smoothing:antialiased;}
        .m-b-0{margin-bottom:0;}
        .col-8{-ms-flex:0 0 66.666667%;flex:0 0 66.666667%;max-width:66.666667%;}
        .align-items-center{/* -ms-flex-align:center!important;*/
        align-items:center!important;}
        .f-w-600{font-weight:600;}
        .col-4{-ms-flex:0 0 33.333333%;flex:0 0 33.333333%;max-width:33.333333%;}
        .bg-c-yellow{/* background:-webkit-gradient(linear,left top,right top,from(#fe9365),to(#feb798));*/
        background:linear-gradient(to right,#fe9365,#feb798);}
        .card .card-footer{background-color:#fff;border-top:none;}
        .card-footer:last-child{border-radius:0 0 calc(0.25rem - 1px) calc(0.25rem - 1px);}
        .card-block{padding:1.25rem;}
        .card-footer{padding:0.75rem 1.25rem;}
        .styleoficon{font-size:32px;}
        .bg-c-green{background:linear-gradient(to right,#0ac282,#0df3a3);}
        .bg-c-pink{background:linear-gradient(to right,#fe5d70,#fe909d);}
        .bg-c-blue{background:linear-gradient(to right,#01a9ac,#01dbdf);}
        .widget-heading{color:#000 !important;font-weight:500;}
        .main-card.card.card_product_sale {height: 470px;}
        .select2-container--default .select2-selection--single .select2-selection__arrow b {border-width: 5px 4px 0 4px;}
        h6.fw-bolder.m-b-0.text-dark {font-weight: bold;color: #000 !important;font-size: 13px;}
        /* .text-c-green{color:#0ac282;}
        .text-c-pink{color:#fe5d70;}
        .text-c-blue{color:#01a9ac;}*/
        /* .text-c-yellow{color:#fe9365;}*/
    </style>
    {{-- @if(!isset($attendence) && empty($attendence))
        <div class="row attendence">
            <div class="col-xl-2 col-12">
                <div class="card ">
                <button type="text"  onclick="attendence({{Auth::user()->id}})" class="btn btn-primary">
                    Present
                </button>
                </div>
            </div>
        </div>
    @endif --}}

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">

                <!-- Score -->
                <div class="Score">
                    <div class="row">

                        <div class="col-sm-6 col-md-3">
                            {{-- <a href="{{url('tso/tso')}}"> --}}
                                <div class="overview-item_score overview-item_orange">
                                    <div class="overview__inner">
                                        <div class="overview-box">
                                            <div class="score_head_content">
                                                <h2>Total Order Booker</h2>
                                            </div>
                                            <div class="icon">
                                                <img src="{{ url('/public/assets/images/7659005.png') }}" alt="">
                                            </div>
                                        </div>
                                        <div class="overview_imgae">
                                            <img src="{{ url('/public/assets/images/miniBar.svg') }}" alt="">
                                        </div>
                                        {{-- <div style="display: flex; align-items: center;width: max-content" class="overview-box_num">
                                            <h2 style="width: max-content" class="total_tso">0</h2>
                                            <h6 style="color: white"> &nbsp; (Activate) &nbsp;</h6>
                                            <h2 style="width: max-content" class="total_tso">0</h2>
                                            <h6 style="color: white"> &nbsp; (Deactivate)</h6>
                                        </div> --}}
                                        <div style="display: flex; align-items: center;" class="overview-box_num">
                                            <!-- Active TSO -->
                                            <a href="{{url('tso/tso')}}">
                                                <div style="display: flex; align-items: center;">
                                                    <h2 style="margin-right: 5px;" class="total_active_tso">0</h2> <!-- Example count -->
                                                    <i class="fas fa-check-circle" style="color: #4caf50; font-size: 20px;"></i>
                                                </div>
                                            </a>

                                            <!-- Deactive TSO -->
                                            <a href="{{url('tso/tso')}}" style="display: {{Auth::user()->hasAnyRole(['CEO','Super Admin']) ? 'block' : 'none'}};">
                                                <div style="display: flex; align-items: center; margin-left: 20px">
                                                    <h2 style="margin-right: 5px;" class="total_inactive_tso">0</h2> <!-- Example count -->
                                                    <i class="fas fa-times-circle" style="color: #f44336; font-size: 20px;"></i>
                                                </div>
                                            </a>

                                            <a href="{{url('tso/tso_status_request')}}">
                                                <div style="display: flex; align-items: center; margin-left: 20px;">
                                                    <h2 style="margin-right: 5px;" class="total_pending_tso">0</h2> <!-- Example count -->
                                                    <i class="fas fa-clock" style="color: #ebdf39; font-size: 20px;"></i> <!-- Pending icon -->
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            {{-- </a> --}}
                        </div>

                        <div class="col-sm-6 col-md-3">
                            {{-- <a href="{{url('shop/shop')}}"> --}}
                                <div class="overview-item_score overview-item_purple">
                                    <div class="overview__inner">
                                        <div class="overview-box">
                                            <div class="score_head_content">
                                                <h2>Total Shop</h2>
                                            </div>
                                            <div class="icon">
                                                <img src="{{ url('/public/assets/images/shop.png') }}" alt="">
                                            </div>
                                        </div>
                                        <div class="overview_imgae">
                                            <img src="{{ url('/public/assets/images/miniBar.svg') }}" alt="">
                                        </div>
                                        {{-- <div class="overview-box_num">
                                            <h2 class="shop_count">0</h2>
                                        </div> --}}
                                        <div style="display: flex; align-items: center;" class="overview-box_num">
                                            <!-- Active TSO -->
                                            <a href="{{url('shop/shop')}}">
                                                <div style="display: flex; align-items: center; ">
                                                    <h2 style="margin-right: 5px;" class="active_shop_count">0</h2> <!-- Example count -->
                                                    <i class="fas fa-check-circle" style="color: #4caf50; font-size: 20px;"></i>
                                                </div>
                                            </a>

                                            <!-- Deactive TSO -->
                                            <a href="{{url('shop/shop')}}">
                                                <div style="display: flex; align-items: center; margin-left: 20px;">
                                                    <h2 style="margin-right: 5px;" class="inactive_shop_count">0</h2> <!-- Example count -->
                                                    <i class="fas fa-times-circle" style="color: #f44336; font-size: 20px;"></i>
                                                </div>
                                            </a>
                                            <!-- Pending Approval TSO -->
                                            <a href="{{url('shop/shop_status_request')}}">
                                                <div style="display: flex; align-items: center; margin-left: 20px;">
                                                    <h2 style="margin-right: 5px;" class="pending_shop_count">0</h2>
                                                    <i class="fas fa-clock" style="color: #ff9800; font-size: 20px;"></i> <!-- Pending icon -->
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            {{-- </a> --}}
                        </div>


                        <div class="col-sm-6 col-md-3">
                            <a href="{{url('product/product')}}">
                                <div class="overview-item_score overview-item_pink">
                                    <div class="overview__inner">
                                        <div class="overview-box">
                                            <div class="score_head_content">
                                                <h2>Total Product</h2>
                                            </div>
                                            <div class="icon">
                                                <img src="{{ url('/public/assets/images/sets_task.png') }}" alt="">
                                            </div>
                                        </div>
                                        <div class="overview_imgae">
                                            <img src="{{ url('/public/assets/images/miniBar.svg') }}" alt="">
                                        </div>
                                        <div class="overview-box_num">
                                            <h2 class="total_product">0</h2>
                                        </div>
                                        {{-- <div class="overview-chart"><div class="chartjs-size-monitor" style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                                        <canvas id="widgetChart3" height="115" style="display: block; width: 187px; height: 115px;" width="187" class="chartjs-render-monitor"></canvas>
                                        </div> --}}
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="col-sm-6 col-md-3">
                            <a href="{{url('report/sales_report?type=current_Month')}}">
                                <div class="overview-item_score overview-item_blue">
                                    <div class="overview__inner">
                                    <div class="overview-box">
                                            <div class="score_head_content">
                                                <h2>Current Month Sale</h2>
                                            </div>
                                            <div class="icon">
                                                <img src="{{ url('/public/assets/images/task_manager.png') }}" alt="">
                                            </div>
                                        </div>
                                        <div class="overview_imgae">
                                            <img src="{{ url('/public/assets/images/miniBar.svg') }}" alt="">
                                        </div>
                                        <div class="overview-box_num">
                                            <h2 class="current_month_sale_amount">0</h2>
                                        </div>
                                        {{-- <div class="overview-chart"><div class="chartjs-size-monitor" style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
                                        <canvas id="widgetChart4" height="115" style="display: block;" width="187" class="chartjs-render-monitor"></canvas>
                                        </div> --}}
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Top Charts -->
                <div class="tops">
                    <div class="row mb-2">
                        <!-- Top 4 TSO-->
                        <div class="col-md-4">
                            <div class="main-card card  card_product_sale">
                                <div class="card-header card_bor_re">
                                    <div class="btn-actions-pane-right">
                                        <h4 class="card-title mb-sm-0 mb-1">Top 10 Order Bookers</h4>
                                    </div>
                                    <div role="group" class="btn-group-sm btn-group">
                                        <a href="#" class="btn btn-info2">Over All</a>
                                        {{-- <button class="active btn btn-info">Last Week</button> --}}
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="align-middle mb-0 table table-borderless ">
                                        <!-- <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>Name</th>
                                                <th class="text-center">Sale</th>
                                            </tr>
                                        </thead> -->
                                        <tbody id="top_tso">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- TOP 3 DISTRIBUTOR-->
                            <div class="main-card card card_product_sale">
                                <div class="card-header card_bor_re">
                                    <div class="btn-actions-pane-right">
                                        <h4 class="card-title mb-sm-0 mb-1">TOP 10 DISTRIBUTOR</h4>
                                    </div>
                                    <div role="group" class="btn-group-sm btn-group">
                                        <button class="btn btn-info2">Over All</button>
                                        {{-- <button class="active btn btn-info">Last Week</button> --}}
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="align-middle mb-0 table table-borderless">
                                        <thead>
                                            <!-- <tr>
                                                <th class="text-center">#</th>
                                                <th>Name</th>
                                                <th class="text-center">Sale</th>
                                            </tr>
                                            </thead> -->
                                            <tbody id="topDistributer">
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- RadialBar Chart Starts -->
                            <!-- <div class="card card-he card_product_sale">
                                <div class="card-header card_bor_re">
                                    <div class="btn-actions-pane-right">
                                        <h4 class="card-title mb-sm-0 mb-1">TOP 3 DISTRIBUTOR</h4>
                                    </div>
                                    <div role="group" class="btn-group-sm btn-group">
                                        <button class="btn btn-info2">Over All</button>
                                        {{-- <button class="active btn btn-info">Last Week</button> --}}
                                    </div>
                                </div>
                                <div id="radialbar-chart"></div>
                            </div> -->
                        </div>
                        <div class="col-md-4">
                            <!-- Column Chart Starts -->
                            <div class="main-card mb-3 card card_product_sale">
                                <div class="card-header card_bor_re">
                                    <div class="btn-actions-pane-right">
                                        <h4 class="card-title mb-sm-0 mb-1"> Top 10 Product Sale</h4>
                                    </div>
                                    <div role="group" class="btn-group-sm btn-group">
                                        {{-- <button class="active btn btn-info">Last Week</button> --}}
                                        <button class="btn btn-info2">Over All</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="align-middle mb-0 table table-borderless">
                                        <!-- <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>Product</th>
                                                <th class="text-center">Sale Qty</th>
                                            </tr>
                                        </thead> -->
                                        <tbody id="topProduct">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <!-- Column Chart Starts -->
                            <div class="main-card mb-3 card card_product_sale">
                                <div class="card-header card_bor_re">
                                    <div class="btn-actions-pane-right">
                                        <h4 class="card-title mb-sm-0 mb-1"> Top 10 Shop Sale</h4>
                                    </div>
                                    <div role="group" class="btn-group-sm btn-group">
                                        {{-- <button class="active btn btn-info">Last Week</button> --}}
                                        <button class="btn btn-info2">Over All</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="align-middle mb-0 table table-borderless">
                                        <!-- <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>Product</th>
                                                <th class="text-center">Sale Qty</th>
                                            </tr>
                                        </thead> -->
                                        <tbody id="topShop">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            {{-- <div class="main-card mb-3 card card_product_sale">
                                <div class="card-header card_bor_re">
                                    <div class="btn-actions-pane-right">
                                        <h4 class="card-title mb-sm-0 mb-1"> Top 10 Balance of Shop </h4>
                                    </div>
                                    <div role="group" class="btn-group-sm btn-group">
                                        <button class="btn btn-info2">Over All</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="align-middle mb-0 table table-borderless">
                                        <tbody id="topBalanceShop">
                                        </tbody>
                                    </table>
                                </div>
                            </div> --}}
                        </div>
                        <div class="col-md-4">
                            {{-- <div class="main-card mb-3 card card_product_sale">
                                <div class="card-header card_bor_re">
                                    <div class="btn-actions-pane-right">
                                        <h4 class="card-title mb-sm-0 mb-1"> Non Productive Shop </h4>
                                    </div>
                                    <div role="group" class="btn-group-sm btn-group">
                                        <button class="btn btn-info2">Over All</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="align-middle mb-0 table table-borderless">
                                        <tbody id="nonProductiveshop">
                                        </tbody>
                                    </table>
                                </div>
                            </div> --}}
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </div>


    <!-- Changes charts -->
    <div class="sales_point_charts">
        <div class="col-md-12">
            <div class="row">

                <div class="col-md-3">
                    <a href="{{url('report/sales_report?type=today')}}">
                        <div class="card card_product_sale2">
                            <div class="card-block">
                                <div class="card_flex">
                                    <div class="content_head">
                                        <h4 class="text-c-yellow f-w-600 today_sale_amount"></h4>
                                    </div>
                                    <div class="changes_valu_icon">
                                        <i class="fa-solid fa-truck-fast styleoficon"></i>
                                    </div>
                                </div>
                                <div class="">
                                    <h6 class="fw-bolder m-b-0 text-dark">Booking Today {{date('F d,Y')}}</h6>
                                </div>
                            </div>
                            <div class="card-footer orange_footer">
                                <div class="row align-items-center">
                                    <div class="col-md-9">
                                        <p class="text-white m-b-0">% change</p>
                                    </div>
                                    <div class="col--md3 text-right">
                                            <i class="feather icon-trending-up text-white f-16"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-md-3">
                    <a href="{{url('report/sales_report?type=yesterday')}}">
                        <div class="card card_product_sale2">
                            <div class="card-block">
                                <div class="card_flex">
                                    <div class="content_head">
                                        <h4 class="text-c-green f-w-600 yesterday_sale_amount">0</h4>
                                    </div>
                                    <div class="changes_valu_icon">
                                        <i class="fa-regular fa-calendar-check styleoficon"></i>
                                    </div>
                                </div>
                                <div class="">
                                    <h6 class="fw-bolder m-b-0 text-dark">Sales Yesterday {{date('F d, Y', strtotime('-1 day'));}} </h6>
                                </div>
                            </div>
                            <div class="card-footer purple_footer">
                                <div class="row ">
                                    <div class="col-md-9">
                                        <p class="text-white m-b-0">% change</p>
                                    </div>
                                    <div class="col-3 text-right">
                                            <i class="feather icon-trending-up text-white f-16"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-md-3">
                    <a href="{{url('report/unit_sold_report?type=currentMonth')}}">
                        <div class="card card_product_sale2">
                            <div class="card-block">
                                <div class="card_flex">
                                    <div class="content_head">
                                        <h4 class="text-c-pink f-w-600 product_count">145</h4>
                                    </div>
                                    <div class="changes_valu_icon">
                                        <i class="fa-solid fa-cart-arrow-down styleoficon"></i>
                                    </div>
                                </div>
                                <div class="">
                                    <h6 class="fw-bolder m-b-0 text-dark">Total Units Sold ({{date('F Y')}})</h6>
                                </div>
                            </div>
                            <div class="card-footer pink_footer">
                                <div class="row ">
                                    <div class="col-md-9">
                                        <p class="text-white m-b-0">% change</p>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <i class="feather icon-trending-up text-white f-16"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-md-3">
                    <a href="{{url('report/sales_report?type=last_Month')}}">
                        <div class="card card_product_sale2">
                            <div class="card-block">
                                <div class="card_flex">
                                    <div class="content_head">
                                        <h4 class="text-c-blue f-w-600 previous_month_sale_amount"></h4>
                                    </div>
                                    <div class="changes_valu_icon">
                                            <i class="fa-solid fa-clock-rotate-left styleoficon"></i>
                                    </div>
                                </div>
                                <div class="">
                                    <h6 class="fw-bolder m-b-0 text-dark">Sales Previous month  {{date('F d, Y', strtotime('-1 month'));}}</h6>
                                </div>
                            </div>
                            <div class="card-footer blue_footer">
                                <div class="row align-items-center">
                                    <div class="col-md-9">
                                        <p class="text-white m-b-0">% change</p>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <i class="feather icon-trending-up text-white f-16"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

        <!-- Candlestick Chart Starts -->
        <!-- <div class="col-xl-6 col-12" style="display: none">
            <div class="card">
                <div class="card-header d-flex flex-sm-row flex-column justify-content-md-between align-items-start justify-content-start">
                    <div>
                        <h4 class="card-title mb-50">FISCAL YEAR SALES GRAPH</h4>
                    </div>
                    <div class="d-flex align-items-center mt-md-0 mt-1">
                        <i class="font-medium-2" data-feather="calendar"></i>
                        <input type="text" class="form-control flat-picker bg-transparent border-0 shadow-none" placeholder="YYYY-MM-DD" />
                    </div>
                </div>
                <div class="card-body">
                    <div id="candlestick-chart"></div>
                </div>
            </div>
        </div> -->
        <!-- Bar Chart Starts -->
        <!-- <div class="col-xl-6 col-12"  style="display: none">
            <div class="card">
                <div class="card-header d-flex flex-sm-row flex-column justify-content-md-between align-items-start justify-content-start">
                    <div>
                        <p class="card-subtitle text-muted mb-25">SYSTEM ACTIVITY</p>
                    </div>
                    <div class="d-flex align-items-center mt-md-0 mt-1">
                        <i class="font-medium-2" data-feather="calendar"></i>
                        <input type="text" class="form-control flat-picker bg-transparent border-0 shadow-none" placeholder="YYYY-MM-DD" />
                    </div>
                </div>
                <div class="card-body">
                    <div id="bar-chart"></div>
                </div>
            </div>
        </div> -->
        <!-- {{-- <div class="col-4">
            <div class="card card-he">
                <div class="card-header d-flex flex-md-row flex-column justify-content-md-between justify-content-start align-items-md-center align-items-start">
                    <h4 class="card-title">TOP 3 PRODUCTS</h4>
                </div>
                <div class="card-body">
                    <div id="column-chart">
                        <table class="table table-bordered">
                            <thead id="topProduct">

                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div> --}}  -->
        <!-- Column Chart Ends -->
        <!-- RadialBar Chart Ends -->
        <!-- Radial Chart Starts-->
        <!-- <div class="col-xl-4 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">TOP 3 PRODUCTS</h4>
                </div>
                <div class="card-body">
                    <div id="radar-chart"></div>
                </div>
            </div>
        </div> -->
        <!-- Radial Chart Ends-->
        <!-- Boxes -->
        <!-- {{-- <div class="col-xl-3 col-md-6 col-12">
            <div class="card card-statistics">
                <div class="card-header">
                    <div class="media">
                        <div class="avatar bg-light-primary mr-2">
                            <div class="avatar-content">
                                <i class="fa fa-user-secret" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="media-body my-auto">
                            <h4 class="font-weight-bolder mb-0 total_tso">0</h4>
                            <p class="card-text font-small-3 mb-0">Total TSO</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-xl-3 col-md-6 col-12">
            <div class="card card-statistics">
                <div class="card-header">
                    <div class="media">
                        <div class="avatar bg-light-primary mr-2">
                            <div class="avatar-content">
                                <i class="fas fa-tasks-alt"></i>
                            </div>
                        </div>
                        <div class="media-body my-auto">
                            <h4 class="font-weight-bolder mb-0 today_sale_amount">Rs. 0</h4>
                            <p class="card-text font-small-3 mb-0">Booking Today {{date('F Y')}} </p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-xl-3 col-md-6 col-12">
            <div class="card card-statistics">
                <div class="card-header">
                    <div class="media">
                        <div class="avatar bg-light-primary mr-2">
                            <div class="avatar-content">
                                <i class="fas fa-tasks-alt"></i>
                            </div>
                        </div>
                        <div class="media-body my-auto">
                            <h4 class="font-weight-bolder mb-0 yesterday_sale_amount">Rs. 0</h4>
                            <p class="card-text font-small-3 mb-0">Sales Yesterday </p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-xl-3 col-md-6 col-12">
            <div class="card card-statistics">
                <div class="card-header">
                    <div class="media">
                        <div class="avatar bg-light-primary mr-2">
                            <div class="avatar-content">
                                <i class="fas fa-shopping-basket"></i>
                            </div>
                        </div>
                        <div class="media-body my-auto">
                            <h4 class="font-weight-bolder mb-0 shop_count">0</h4>
                            <p class="card-text font-small-3 mb-0">Total Shop</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-xl-3 col-md-6 col-12">
            <div class="card card-statistics">
                <div class="card-header">
                    <div class="media">
                        <div class="avatar bg-light-primary mr-2">
                            <div class="avatar-content">
                            <i class="fas fa-tasks-alt"></i>
                            </div>
                        </div>
                        <div class="media-body my-auto">
                            <h4 class="font-weight-bolder mb-0 current_month_sale_amount">Rs. 0</h4>
                            <p class="card-text font-small-3 mb-0">Sales Current Month {{date('F Y')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-xl-3 col-md-6 col-12">
            <div class="card card-statistics">
                <div class="card-header">
                    <div class="media">
                        <div class="avatar bg-light-primary mr-2">
                            <div class="avatar-content">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                        <div class="media-body my-auto">
                            <h4 class="font-weight-bolder mb-0 product_count">0</h4>
                            <p class="card-text font-small-3 mb-0">Units Sold ({{date('F Y')}})</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-xl-3 col-md-6 col-12">
            <div class="card card-statistics">
                <div class="card-header">
                    <div class="media">
                        <div class="avatar bg-light-primary mr-2">
                            <div class="avatar-content">
                                <i data-feather="trending-up" class="avatar-icon"></i>
                            </div>
                        </div>
                        <div class="media-body my-auto">
                            <h4 class="font-weight-bolder mb-0 previous_month_sale_amount">Rs. 0</h4>
                            <p class="card-text font-small-3 mb-0">Sales Previous month </p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-xl-3 col-md-6 col-12">
            <div class="card card-statistics">
                <div class="card-header">
                    <div class="media">
                        <div class="avatar bg-light-primary mr-2">
                            <div class="avatar-content">
                                <i data-feather="trending-up" class="avatar-icon"></i>
                            </div>
                        </div>
                        <div class="media-body my-auto">
                            <h4 class="font-weight-bolder mb-0 total_product">0</h4>
                            <p class="card-text font-small-3 mb-0">Total Product</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}} -->
        <!-- Boxes Ends -->

</section>
@section('script')
@endsection
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.all.min.js"></script>

    <script>
        jQuery(document).ready(function($) {
            topTso();
            dashboarData();
            setInterval(() => {
                console.log('setinterval');

                topTso();
                dashboarData();
            }, 120000);

        });

    function topTso()
    {

            $('#topProduct').html('');
                $('#topDistributer').html('');
                $('#top_tso').html('');
                $('#topShop').html('');
                $('#topBalanceShop').html('');
                $('#nonProductiveshop').html('');



            $('#loader').show();
            var id  =0 ;
            $.ajax({
            url:"{{route('topRank')}}",
            method: 'GET',
            data: {id,id},
            error: function()
            {},
            success: function(response)
            {
                // console.log(response.distributer);
                var tso = '';
                var distributer = '';
                var product = '';
                var shop = '';
                var topBalanceShop = '';
                var nonProductiveshop = '';
                var i =1;
                var j =1;
                var k =1;
                var l =1;
                var m =1;
                var n =1;

                $('#loader').hide();
                $('.dashboard').css('display','');

                    for (var key in response.tso_check) {
                        if (response.tso_check.hasOwnProperty(key)) {
                            var value = response.tso_check[key];
                            var amounts = parseFloat(value.net_amount);
                            tso += ` <tr>
                            <!-- <td class="text-center text-muted">#${i}</td>-->
                                        <td>
                                            <div class="widget-content p-0">
                                                <a href="{{url('report/top_tso_report')}}/${value.id}">
                                                    <div class="widget-content-wrapper">
                                                        <div class="widget-content-left mr-3">
                                                            <img  src="{{asset('public/assets/images/avatars/avator.png')}}" alt="Avatar">
                                                        </div>
                                                        <div class="widget-content-left flex2">
                                                            <div class="widget-heading">${value.tso_name}</div>
                                                            <div class="widget-heading_sub"></div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-center"><div class="product_counts"> ${Math.round(amounts).toLocaleString()}  </div></td>

                                    </tr>`;
                                    i++;
                        }
                    }

                    for (var key in response.distributer_check) {
                        if (response.distributer_check.hasOwnProperty(key)) {
                            var value = response.distributer_check[key];
                            var amounts = parseFloat(value.net_amount);
                            distributer += ` <tr>
                            <!-- <td class="text-center text-muted">#${j}</td>-->
                                <td>
                                    <div class="widget-content p-0">
                                        <a href="{{url('report/top_distributor_report')}}/${value.id}">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-content-left mr-3">
                                                    <img  src="{{asset('public/assets/images/avatars/shop.png')}}" alt="Avatar">
                                                </div>
                                                <div class="widget-content-left flex2">
                                                    <div class="widget-heading">${value.distributor_name}</div>
                                                    <div class="widget-heading_sub"></div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center"><div class="product_counts">${amounts.toLocaleString()}</div></td>

                            </tr>`;
                                    j++;
                        }
                    }

                    for (var key in response.shop_check) {
                        if (response.shop_check.hasOwnProperty(key)) {
                            var value = response.shop_check[key];
                            var amounts = parseFloat(value.net_amount);
                            shop += ` <tr>
                            <!-- <td class="text-center text-muted">#${l}</td>-->
                                <td>
                                    <div class="widget-content p-0">
                                        <a href="{{url('report/top_shop_report')}}/${value.id}?type=top_sale">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-content-left mr-3">
                                                    <img  src="{{asset('public/assets/images/avatars/shop.png')}}" alt="Avatar">
                                                </div>
                                                <div class="widget-content-left flex2">
                                                    <div class="widget-heading">${value.shop_name}</div>
                                                    <div class="widget-heading_sub"></div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center"><div class="product_counts">${amounts.toLocaleString()}</div></td>

                            </tr>`;
                                    l++;
                        }
                    }

                    for (var key in response.product_check) {
                        if (response.product_check.hasOwnProperty(key)) {
                            var value = response.product_check[key];

                            product += `<tr>
                            <!-- <td class="text-center text-muted">#${k}</td>-->
                            <td><div class="widget-content p-0">
                                <a href="{{url('report/top_product_report')}}/${value.id}">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left mr-3">
                                            <img  class="" src="{{asset('public/assets/images/avatars/product_top3.png')}}" alt="Avatar">
                                        </div>
                                        <div class="widget-content-left flex2">
                                            <div class="widget-heading">${value.product_name}</div>
                                            <div class="widget-heading_sub"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </td>
                        <td class="text-center"> <div class="product_counts">${Math.round(value.net_qty)}</div></td>

                        </tr>`;
                                    k++;
                        }
                    }

                    for (var key in response.topBalanceShop_check) {
                        if (response.topBalanceShop_check.hasOwnProperty(key)) {
                            var value = response.topBalanceShop_check[key];
                            var amounts = parseFloat(value.net_amount);
                            topBalanceShop += `<tr>
                            <!-- <td class="text-center text-muted">#${m}</td>-->
                            <td><div class="widget-content p-0">
                                <a href="{{url('report/top_shop_balance_report?shop_id=')}}${value.id}">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left mr-3">
                                            <img  class="" src="{{asset('public/assets/images/avatars/shop.png')}}" alt="Avatar">
                                        </div>
                                        <div class="widget-content-left flex2">
                                            <div class="widget-heading">${value.shop_name}</div>
                                            <div class="widget-heading_sub"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </td>
                        <td class="text-center"> <div class="product_counts">${amounts.toLocaleString()}</div></td>

                        </tr>`;
                                    m++;
                        }
                    }

                    for (var key in response.nonProductiveshop) {
                        if (response.nonProductiveshop.hasOwnProperty(key)) {
                            var value = response.nonProductiveshop[key];
                            var mydate = new Date(value.dc_date);
                            var options = { year: 'numeric', month: 'long' , day : 'numeric'};
                            var str = mydate.toLocaleDateString('en-US', options);

                            nonProductiveshop += `<tr>
                            <!-- <td class="text-center text-muted">#${n}</td>-->
                            <td><div class="widget-content p-0">
                                <a href="{{url('report/top_shop_report')}}/${value.id}?type=non_productive">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left mr-3">
                                            <img  class="" src="{{asset('public/assets/images/avatars/shop.png')}}" alt="Avatar">
                                        </div>
                                        <div class="widget-content-left flex2">
                                            <div class="widget-heading">${value.shop_name}</div>
                                            <div class="widget-heading_sub"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </td>
                        <td class="text-center"> <div class="product_counts">${str}</div></td>

                        </tr>`;
                                    n++;
                        }
                    }

                    $('#topProduct').append(product);
                    $('#topDistributer').append(distributer);
                    $('#top_tso').append(tso);
                    $('#topShop').append(shop);
                    $('#topBalanceShop').append(topBalanceShop);
                    $('#nonProductiveshop').append(nonProductiveshop);

            }
        });
    }

        function dashboarData()
        {
            var id  =0 ;
            $.ajax({
            url:"{{route('dashboarData')}}",
            method: 'GET',
            data: {id,id},
            error: function()
            {},
            success: function(response)
            {
                console.log(response);
                var c_amount = Math.round(response.current_month_sale_amount).toLocaleString("en")??0;
                var p_amount = Math.round(response.previous_month_sale_amount.amount).toLocaleString("en")??0;
                var today_sale_amount = Math.round(response.today_sale_amount.amount).toLocaleString("en")??0;
                var yesterday_sale_amount = Math.round(response.yesterday_sale_amount.amount).toLocaleString("en")??0;
                var product_count = Math.round(response.product_count_new_return.net_qty)??0;
                // var product_count = Math.round(response.product_count.product_count)??0;
                $('.total_active_tso').html(response.tso_active_count);
                $('.total_inactive_tso').html(response.tso_inactive_count);
                $('.total_pending_tso').html(response.tso_pending_count);
                $('.active_shop_count').html(response.active_shop_count);
                $('.inactive_shop_count').html(response.inactive_shop_count);
                $('.pending_shop_count').html(response.pending_shop_count);

                $('.current_month_sale_amount').html(c_amount.toLocaleString());
                $('.previous_month_sale_amount').html('Rs. '+p_amount);
                $('.today_sale_amount').html('Qty '+today_sale_amount);
                $('.yesterday_sale_amount').html('Rs. '+yesterday_sale_amount);
                $('.product_count').html(product_count);
                $('.total_product').html(response.total_product);
            }



            })
        }
        function attendence(id)
        {

        // Correct usage with the 'new' keyword
        // swal();
        const instance = new   swal({
            title: "Are you sure?",
            text: "",
            icon: "warning",
            buttons: ["No", "Yes|Save"],
            closeModal:true,
            dangerMode: false,
            })
                .then((willDelete) => {
            if (willDelete) {
            $.ajax({
                url:"{{route('addAttendence')}}",
                method: 'GET',
                data: {id,id},
                error: function()
                {},
                success: function(response)
                {
                    console.log(response);
                    if(response == 1)
                    {
                        const instance = new  swal(" Greate Successfully !");
                    $('.attendence').hide();

                    }
                }
            });
        } else {
            const instance = new swal("Your imaginary file is safe!");
        }
        });
            }
    </script>
@endsection
