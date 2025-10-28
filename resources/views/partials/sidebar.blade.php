<!-- BEGIN: Main Menu-->
@php
    $user_data = Auth::user();



@endphp
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow"   >
    <div class="navbar-header">
        <!-- <ul class="nav navbar-nav flex-row">
            <li class="nav-itemm mr-auto">

            </li>
            <li class="nav-items nav-toggle">

            </li>

        </ul> -->
        <div class="logo_flex">
            <div class="logo_wrp">
                <a class="navbar-brand" href="{{ url('dashboard') }}">
                    <span class="brand-logo">
                        <!-- <img style="width: 175px;" src="{{ url('/public/assets/images/logo.png') }}"> -->
                        <img class="logo_m" src="{{ url('/public/assets/images/logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('logoo.png') }}'" />
                        <img class="logo_m hide" src="{{ asset('logo.png') }}">
                    </span>
                </a>
            </div>
            <div class="o_f">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="fa fa-list-ul" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="main-menu-content ps">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            {{-- @can('Dashboard') --}}
            <li class="nav-items">
                <a class="d-flex align-items-center" href="{{ url('dashboard') }}">
                    <i class="fa-solid fa-house"></i>
                    <span class="menu-title text-truncate"> Home</span>
                </a>
            </li>
            {{-- @endcan --}}
            {{-- @can('Manage_Permissions', 'Create_User', 'User_List') --}}
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                    <i class="fa-solid fa-user-gear"></i>
                    <span class="menu-title text-truncate">User Management</span></a>
                    <ul class="menu-content">
                        @can('Manage_Permissions')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('permission.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Manage Permissions
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Create_User')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('users.create') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Add User
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('User_List')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('users.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        User list
                                    </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            {{-- @endcan --}}
            {{-- @can('Execution_Sales_Order', 'Execution_Sales_Return', 'Payment_Recovery', 'Bill_Printing') --}}
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i class="fas fa-gears"></i>
                        <span class="menu-title text-truncate">
                            Execution
                        </span>
                    </a>
                    <ul class="menu-content">
                        @can('Execution_Sales_Order')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('sale-order.execution.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Sales Order
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Execution_Sales_Return')
                         <!-- <li>
                                <a class="d-flex align-items-center" href="{{ route('sales_return.sales_return_list', 1) }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Sales Return
                                    </span>
                                </a>
                            </li> -->
  				<li>
                                <a class="d-flex align-items-center" href="{{ route('sales_return.sales_return_list_shop_wise', 1) }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Sales Return V2
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Payment_Recovery')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('payment-recovery.execution.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Payment Recovery
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Bill_Printing')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('execution.bill_printing') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Bill Printing
                                    </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            {{-- @endcan --}}
            {{-- @can('Create_Sales_Order', 'Sales_Order_List', 'Create_Sales_Return', 'Sales_Return_List',
                'New_Receipt_Voucher', 'Manage_Receipts') --}}
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i class="fas fa-users-gear"></i>
                        <span class="menu-title text-truncate">
                            KPO
                        </span>
                    </a>
                    <ul class="menu-content">
                        @can('Create_Sales_Order')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('sale.create') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Create Sales Order
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Sales_Order_List')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('sale.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Sales Order List
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Create_Sales_Return')
                            <!-- <li>
                                <a class="d-flex align-items-center" href="{{ route('sales_return.create') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Create Sales Return
                                    </span>
                                </a>
                            </li> -->
                        @endcan
                        @can('Sales_Return_List')
                        
                            <!-- <li>
                                <a class="d-flex align-items-center" href="{{ route('sales_return.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Sales Return List
                                    </span>
                                </a>
                            </li> -->
				 <li>
                                <a class="d-flex align-items-center" href="{{ route('sales_return.index_return') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Sales Return List V2
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('New_Receipt_Voucher')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('receipt-voucher.create') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        New Receipt Voucher
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Manage_Receipts')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('receipt-voucher.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Manage Receipts
                                    </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            {{-- @endcan --}}
            {{-- @can('Create_Product', 'Product_List', 'Create_Category', 'Category_List', 'Create_Product_Type',
                'Product_Type_List', 'Create_Brand', 'Brand_List', 'Create_UOM', 'UOM_List') --}}
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i class="fas fa-boxes"></i>
                        <span class="menu-title text-truncate">
                            Product
                        </span>
                    </a>
                    <ul class="menu-content">
                        @can('Create_Product')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('product.create') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Add Product
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Create_Product')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('product.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Product List
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('import_product')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('product.import_product') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Import Product
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Create_Product')
                            {{-- <li>
                                <a class="d-flex align-items-center" href="{{ route('product.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Product Master Data
                                    </span>
                                </a>
                            </li> --}}
                        @endcan
                        @can('Create_Product')
                            {{-- <li>
                                <a class="d-flex align-items-center" href="{{ route('product.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Product Master Data List
                                    </span>
                                </a>
                            </li> --}}
                        @endcan

                        @can('Create_Scheme_Product', 'Scheme_Product_List')
                            <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Scheme Product
                                    </span>
                                </a>
                                <ul class="menu-content">
                                    @can('Create_Scheme_Product')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ url('product/scheme_product/create') }}">
                                                <span class="menu-item text-truncate">
                                                    Add Scheme Product
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('Scheme_Product_List')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ url('product/scheme_product') }}">
                                                <span class="menu-item text-truncate">
                                                    Scheme Product List
                                                </span>
                                            </a>
                                        </li>
                                    @endcan



                                </ul>
                            </li>
                        @endcan

   <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Scheme Product Pcs
                                    </span>
                                </a>
                                <ul class="menu-content">
                                  
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ url('product/scheme_product_create_pcs') }}">
                                                <span class="menu-item text-truncate">
                                                    Add Scheme Product Pcs
                                                </span>
                                            </a>
                                        </li>
                                  
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ url('product/scheme_product_pcs') }}">
                                                <span class="menu-item text-truncate">
                                                    Scheme Product List Pcs
                                                </span>
                                            </a>
                                        </li>
                                  
                                </ul>
                            </li>
                     	
                        @can('Create_Category', 'Category_List')
                            <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Product Category
                                    </span>
                                </a>
                                <ul class="menu-content">
                                    @can('Create_Category')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ url('product/category/create') }}">
                                                <span class="menu-item text-truncate">
                                                    Add Category
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('Category_List')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ url('product/category') }}">
                                                <span class="menu-item text-truncate">
                                                    Category List
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        @can('Create_Product_Type', 'Product_Type_List')
                            <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Product Type
                                    </span>
                                </a>
                                <ul class="menu-content">
                                    @can('Create_Product_Type')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('type.create') }}">
                                                <span class="menu-item text-truncate">
                                                    Add Type
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('Product_Type_List')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('type.index') }}">
                                                <span class="menu-item text-truncate">
                                                    Type List
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        @can('Create_Brand', 'Brand_List')
                            <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Brands
                                    </span>
                                </a>
                                <ul class="menu-content">
                                    @can('Create_Brand')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('brand.create') }}">
                                                <span class="menu-item text-truncate">
                                                    Add Brand
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('Brand_List')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('brand.index') }}">
                                                <span class="menu-item text-truncate">
                                                    Brand List
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        @can('Create_UOM', 'UOM_List')
                            <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Product Unit
                                    </span>
                                </a>
                                <ul class="menu-content">
                                    @can('Create_UOM')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('uom.create') }}">
                                                <span class="menu-item text-truncate">
                                                    Add UOM
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('UOM_List')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('uom.index') }}">
                                                <span class="menu-item text-truncate">
                                                    UOM List
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                    </ul>
                </li>
            {{-- @endcan --}}
            {{-- @can('Create_Shop', 'Shop_List', 'Create_Shop_Type', 'Shop_Type_List', 'Create_Price_Type',
                'Price_Type_List') --}}
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i class="fa-solid fa-shop"></i>
                        <span class="menu-title text-truncate">
                            Shop
                        </span>
                    </a>
                    <ul class="menu-content">
                        @canany(['Create_Shop', 'Shop_List'])
                            <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Shop
                                    </span>
                                </a>
                                <ul class="menu-content">
                                    @can('Create_Shop')
                                        <!-- <li>
                                            <a class="d-flex align-items-center" href="{{ route('shop.create') }}">
                                                <span class="menu-item text-truncate">
                                                    Add Shop
                                                </span>
                                            </a>
                                        </li> -->
                                    @endcan
                                    @can('Shop_List')
                                        <!-- <li>
                                            <a class="d-flex align-items-center" href="{{ route('shop.index') }}">
                                                <span class="menu-item text-truncate">
                                                    Shop List
                                                </span>
                                            </a>
                                        </li> -->
                                    @endcan
  					@can('Shop_list_v2')
                                       <li>
                                            <a class="d-flex align-items-center" href="{{ route('index_Add_multi') }}">
                                                <span class="menu-item text-truncate">
                                                    Shop List v2
                                                </span>
                                            </a>
                                        </li>
                                          @endcan
                                          @can('Shop_add_v2')
                                         <li>
                                            <a class="d-flex align-items-center" href="{{ route('create_create_multi') }}">
                                                <span class="menu-item text-truncate">
                                                    Add Shop v2
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('Shop_Status_Request')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('shop.status_request') }}">
                                                <span class="menu-item text-truncate">
                                                    Shop Status Request
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('import_shop')
                                    <li>
                                        <a class="d-flex align-items-center" href="{{ route('shop.ImportShop') }}">
                                            <span class="menu-item text-truncate">
                                                Import Shop
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        @can('Create_Shop_Type', 'Shop_Type_List')
                            <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Shop Type
                                    </span>
                                </a>
                                <ul class="menu-content">
                                    @can('Create_Shop_Type')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('shoptype.create') }}">
                                                <span class="menu-item text-truncate">
                                                    Add Shop Type
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('Shop_Type_List')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('shoptype.index') }}">
                                                <span class="menu-item text-truncate">
                                                    Shop Type List
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                        @can('rack', 'rack_List')
                        <li>
                            <a class="d-flex align-items-center" href="#">
                                <i data-feather="circle"></i>
                                <span class="menu-item text-truncate">
                                    Rack
                                </span>
                            </a>
                            <ul class="menu-content">
                                @can('rack')
                                    <li>
                                        <a class="d-flex align-items-center" href="{{ route('rack.create') }}">
                                            <span class="menu-item text-truncate">
                                                Add Rack
                                            </span>
                                        </a>
                                    </li>
                                @endcan
                                @can('rack_list')
                                    <li>
                                        <a class="d-flex align-items-center" href="{{ route('rack.index') }}">
                                            <span class="menu-item text-truncate">
                                                 Rack List
                                            </span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                        @endcan

                        @can('issue_rack' , 'issue_rack_list')
                        <li>
                            <a class="d-flex align-items-center" href="#">
                                <i data-feather="circle"></i>
                                <span class="menu-item text-truncate">
                                    Issue Rack
                                </span>
                            </a>
                            <ul class="menu-content">
                                @can('issue_rack')
                                    <li>
                                        <a class="d-flex align-items-center" href="{{ route('issue_rack') }}">
                                            <span class="menu-item text-truncate">
                                                Issue Rack
                                            </span>
                                        </a>
                                    </li>
                                @endcan
                                @can('issue_rack_list')
                                    <li>
                                        <a class="d-flex align-items-center" href="{{ route('issue_rack_list') }}">
                                            <span class="menu-item text-truncate">
                                                Issue Rack List
                                            </span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                        @endcan

                        @can('Create_Price_Type', 'Price_Type_List')
                            <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Price Type
                                    </span>
                                </a>
                                <ul class="menu-content">
                                    @can('Create_Price_Type')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('priceType.create') }}">
                                                <span class="menu-item text-truncate">
                                                    Add Price Type
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('Price_Type_List')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('priceType.index') }}">
                                                <span class="menu-item text-truncate">
                                                    Price Type List
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                    </ul>
                </li>
            {{-- @endcan --}}
            {{-- @can('Create_TSO', 'TSO_List', 'TSO_Target', 'TSO_Activity') --}}
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i class="fa-solid fa-hand-holding"></i>
                        <span class="menu-title text-truncate">
                            ORDER BOOKER
                        </span>
                    </a>
                    <ul class="menu-content">
                        @can('Create_TSO')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('tso.create') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Add ORDER BOOKER
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('TSO_List')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('tso.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        ORDER BOOKER List
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('TSO_Status_Request')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('tso.status_request') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        ORDER BOOKER Status Request
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('TSO_Target')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('tso-target.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        ORDER BOOKER Target
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('TSO_Import')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('tso.ImportTSO') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Order Booker Import
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('TSO_Log')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('tso_log') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        ORDER BOOKER Log
                                    </span>
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
            {{-- @endcan --}}
            {{-- @can('Create_Distributor', 'Distributor_List', 'Import_Stock_Form', 'Import_Stock_CSV', 'Stock_List',
                'Create_Zone', 'Zone_List') --}}
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i class="fa-solid fa-arrows-split-up-and-left"></i>
                        <span class="menu-title text-truncate">
                            Distributor
                        </span>
                    </a>
                    <ul class="menu-content">
                        @can('Create_Distributor')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('distributor.create') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Add Distributor
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Distributor_List')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('distributor.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Distributor List
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('import_distributor')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('distributor.ImportDistributor') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Import Distributors
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Import_Stock_Form', 'Import_Stock_CSV', 'Stock_List')
                            <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Stock Management
                                    </span>
                                </a>
                                <ul class="menu-content">


                                    @can('Import_Stock_Form')
                                    <li>
                                        <a class="d-flex align-items-center" href="{{ route('add_opening_form') }}">
                                            <span class="menu-item text-truncate">
                                                Opening
                                            </span>
                                        </a>
                                    </li>
                                @endcan

                                    @can('Import_Stock_Form')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('stockManagement.create') }}">
                                                <span class="menu-item text-truncate">
                                                    Import Stock (Form)
                                                </span>
                                            </a>
                                        </li>
                                    @endcan

                                    @can('Import_Stock_Form')
                                    <li>
                                        <a class="d-flex align-items-center" href="{{ route('stockManagement.import_stock_list') }}">
                                            <span class="menu-item text-truncate">
                                                Import Stock List
                                            </span>
                                        </a>
                                    </li>
                                @endcan

                                    @can('Import_Stock_CSV')
                                        <li>
                                            <a class="d-flex align-items-center"
                                                href="{{ route('stockManagement.importStock') }}">
                                                <span class="menu-item text-truncate">
                                                    Import Stock (CSV)
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('Stock_List')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('stockManagement.index') }}">
                                                <span class="menu-item text-truncate">
                                                    Stock List
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        {{-- @endcan --}}

                            {{-- @can('create_stock_transfer', 'stock_transfer_list', 'stock_transfer_view') --}}
                                <li>
                                    <a class="d-flex align-items-center" href="#">
                                        <i data-feather="circle"></i>
                                        <span class="menu-item text-truncate">
                                        Stock Transfer
                                    </span>
                                    </a>
                                    <ul class="menu-content">
                                        @can('create_stock_transfer')
                                            <li>
                                                <a class="d-flex align-items-center" href="{{ route('stock.create') }}">
                                                <span class="menu-item text-truncate">
                                                  Add  Stock Transfer
                                                </span>
                                                </a>
                                            </li>
                                        @endcan
                                        @can('stock_transfer_list')
                                            <li>
                                                <a class="d-flex align-items-center"
                                                   href="{{ route('stock.index') }}">
                                                <span class="menu-item text-truncate">
                                                    Stock Transfer List
                                                </span>
                                                </a>
                                            </li>
                                        @endcan

                                    </ul>
                                </li>
                            {{-- @endcan --}}


                        {{-- @can('Create_Zone', 'Zone_List') --}}
                            <li>
                                <a class="d-flex align-items-center" href="#">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Zone
                                    </span>
                                </a>
                                <ul class="menu-content">
                                    @can('Create_Zone')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('zone.create') }}">
                                                <span class="menu-item text-truncate">
                                                    Add Zone
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('Zone_List')
                                        <li>
                                            <a class="d-flex align-items-center" href="{{ route('zone.index') }}">
                                                <span class="menu-item text-truncate">
                                                    Zone List
                                                </span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan
                    </ul>
                </li>
            {{-- @endcan --}}
            {{-- @can('Create_Route', 'Route_List', 'TSO_Day_Wise_Plan') --}}
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i class="fa-solid fa-route"></i>
                        <span class="menu-title text-truncate">
                            Route
                        </span>
                    </a>
                    <ul class="menu-content">
                        @can('Create_Route')
                            <!-- <li class="">
                                <a class="d-flex align-items-center" href="{{ route('route.create') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Add Route
                                    </span>
                                </a>
                            </li> -->
                        @endcan
                        @can('Route_List')
                            <!-- <li>
                                <a class="d-flex align-items-center" href="{{ route('route.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Route List
                                    </span>
                                </a>
                            </li> -->
                        @endcan

 			@can('Create_viewe_Route')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('route_create_tso_multi') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Add Route V2
                                    </span>
                                </a>
                            </li>
                             @endcan
                            @can('Create_List_Route')
                              <li>
                                <a class="d-flex align-items-center" href="{{ route('AddRouteMultiTso_index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Route List V2
                                    </span>
                                </a>
                            </li>
                        @endcan
                       
                        @can('TSO_Day_Wise_Plan')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('route.TSODayWisePlanner') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        ORDER BOOKER Day Wise Planner
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Transfer_Route')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('route.transfer') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Transfer Route
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Route_Log')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('route_log') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Route Log
                                    </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            {{-- @endcan --}}


            {{-- @can('Create_Sub_Route', 'Sub_Route_List') --}}
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i class="fa-solid fa-route"></i>
                        <span class="menu-title text-truncate">
                          Sub  Route
                        </span>
                    </a>
                    <ul class="menu-content">
                        @can('Create_Sub_Route')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('subroutes.create') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Add Sub Route
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Sub_Route_List')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('subroutes.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                       Sub Route List
                                    </span>
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
            {{-- @endcan --}}

		   @can('Report_Center')
   		<li class="">
                     <a class="d-flex align-items-center" href="{{ route('report_center') }}">
                    <i class="fa-solid fa-file-invoice"></i>
                        <span class="menu-title text-truncate">
                            Report Center
                        </span>
                    </a>
                   
                </li>

		 @endcan





            {{-- @can('Order_Summary_Report', 'Order_List_Report', 'Scheme_Product_Report', 'Product_Availability',
                'Load_Sheet_Report', 'Order_VS_Execution', 'TSO_Target_Sheet') --}}
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                    <i class="fa-solid fa-file-invoice"></i>
                        <span class="menu-title text-truncate">
                            Report
                        </span>
                    </a>
                    <ul class="menu-content">
                        @can('Order_Summary_Report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('order_summary') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Order Summary Report
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Receipt_Voucher_Summary_Report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('receipt_voucher_summary') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Receipt Voucher Report
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('shop_ledger_Report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('shop_ledger_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Shop Ledger Report
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('shop_visit')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('shop.shopVisitList') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Shop Visit / Merchandising
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Order_List_Report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('order_list') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Order List Report
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('TSO_Activity')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('activity') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        ORDER BOOKER Activity
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Scheme_Product_Report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('scheme_product') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Scheme product Report
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Product_Availability')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('product_avail') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Product Availability
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Product_Productivity')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('product_productivity') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Product Productivity
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Load_Sheet_Report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('load_Sheet') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Load Sheet Report
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Order_VS_Execution')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('order_vs_execution') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Order vs Execution
                                    </span>
                                </a>
                            </li>
                        @endcan




 			<li class="">
                                <a class="d-flex align-items-center" href="{{ route('order_vs_execution_return') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Order vs Execution Return
                                    </span>
                                </a>
                            </li>
                        @can('TSO_Target_Sheet')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('tso_target') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        ORDER BOOKER Target Sheet
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('racks_report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('racks.report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Racks Report
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('Attendence_Report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('attendence_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Attendence Report
                                    </span>
                                </a>
                            </li>
                        @endcan

                        @can('Attendence_Report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('day_wise_attendence_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Day Wise Attendence Report
                                    </span>
                                </a>
                            </li>
                        @endcan

                        @can('item_wise_sales')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('item_wise_sale') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Item Wise Sale
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('stock_report')
                        <li class="" >
                            <a class="d-flex align-items-center" href="{{ route('stock_report') }}">
                                <i data-feather="circle"></i>
                                <span class="menu-item text-truncate">
                                    Stock Details Report
                                </span>
                            </a>
                        </li>
  @endcan
                         @can('stock_report_new')
  <li class="" >
                            <a class="d-flex align-items-center" href="{{ route('stock_report_new') }}">
                                <i data-feather="circle"></i>
                                <span class="menu-item text-truncate">
                                    Stock Details Report (New)
                                </span>
                            </a>
                        </li>
                        @endcan
 @can('Closing_Stock_Adjustment')
                        <li class="" >
                            <a class="d-flex align-items-center" href="{{ route('closing_stock_minus_zero') }}">
                                <i data-feather="circle"></i>
                                <span class="menu-item text-truncate">
                                  Closing Stock Adjustment 
                                </span>
                            </a>
                        </li>
                        @endcan



   @can('brand_wise_daily_sale')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('brand_wise_daily_sale') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                    Brand wise Daily Sale
                                    </span>
                                </a>
                            </li>
                        @endcan
 @can('brand_distributer_report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('brand_distributer_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Brand Distributer Report
                                    </span>
                                </a>
                            </li>
                        @endcan



                        @can('booking_vs_execution')
                            <li class="" >
                                <a class="d-flex align-items-center" href="{{ route('booking_vs_execution') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                    Booking vs Execution
                                    </span>
                                </a>
                            </li>
                        @endcan

                        
                         @can('distributer_product_sales_value_report')
                            <li class="" >
                                <a class="d-flex align-items-center" href="{{ route('distributer_product_sales_value_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                    Distributer Product Sales Value Report
                                    </span>
                                </a>
                            </li>
                        @endcan

                        
                         @can('daily_booking_unit_summary')
                            <li class="" >
                                <a class="d-flex align-items-center" href="{{ route('daily_booking_unit_summary') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                    Daily Booking Unit Summary
                                    </span>
                                </a>
                            </li>
                        @endcan

   			@can('non_dispatch_order_report')
                            <li class="" >
                                <a class="d-flex align-items-center" href="{{ route('non_dispatch_order_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                   Non Dispatch Order Report
                                    </span>
                                </a>
                            </li>
                        @endcan

			@can('product_wise_sale')
                            <li class="" >
                                <a class="d-flex align-items-center" href="{{ route('product_wise_sale') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                  	Product Wise Sale
                                    </span>
                                </a>
                            </li>
                        @endcan
			@can('cancelled_orders_report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('cancelled_orders_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Cancelled Orders Report
                                    </span>
                                </a>
                            </li>
 			@endcan

			@can('unproductive_shop_report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('unproductive_shop_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                      Unproductive Shop Report
                                    </span>
                                </a>
                            </li>
 			@endcan

  			@can('app_version_report')
                          <li class="">
                                <a class="d-flex align-items-center" href="{{ route('app_version_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                       App version  Report
                                    </span>
                                </a>
                            </li>
                        @endcan

		@can('order_booker_target_report')
                            <li class="">
                                <a class="d-flex align-items-center" href="{{ route('order_booker_target_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Order Booker Target Report
                                    </span>
                                </a>
                            </li>
 				 @endcan
@can('tso_sales_return_report')

   <li class="">
                                <a class="d-flex align-items-center" href="{{ route('tso_sales_return_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        TSO Sales Return Report
                                    </span>
                                </a>
                            </li>
 @endcan
 

				@can('shop_wise_sales_report')
    				<li class="">
                                <a class="d-flex align-items-center" href="{{ route('shop_wise_sales_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Shop Wise Sales Report
                                    </span>
                                </a>
                            </li>
	 			@endcan


@can('order_booker_daily_activity_report')
    				<li class="">
                                <a class="d-flex align-items-center" href="{{ route('order_booker_daily_activity_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                      Order Booker Daily Activity Report
                                    </span>
                                </a>
                            </li>
	 			@endcan

				@can('order_booker_daily_activity_location_report')
    				<li class="">
                                <a class="d-flex align-items-center" href="{{ route('order_booker_daily_activity_location_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                     Order Booker Daily Activity Location Report
                                    </span>
                                </a>
                            </li>
	 			@endcan


			@can('sales_return_report')
      				<li class="">
                                <a class="d-flex align-items-center" href="{{ route('sales_return_report') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Sales Return Report
                                    </span>
                                </a>
                            </li>
			@endcan

                    </ul>
                </li>
            {{-- @endcan --}}
            @can('attendenceList')
            {{-- <li class=" nav-item">
                <a class="d-flex align-items-center" href="#">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span class="menu-title text-truncate">
                        Attendence
                    </span>
                </a>
                <ul class="menu-content">
                    <li class=" nav-item" >
                        <a class="d-flex align-items-center" href="{{ route('attendenceList') }}">
                            <i data-feather="circle"></i>
                            <span class="menu-item text-truncate">
                                List
                            </span>
                        </a>
                    </li>
                </ul>
            </li> --}}
            @endcan
            {{-- @can('Department', 'Designation', 'User_Roles','City') --}}
                <li class=" nav-item">
                    <a class="d-flex align-items-center" href="#">
                        <i class="fa-solid fa-sliders"></i>
                        <span class="menu-title text-truncate">
                            Settings
                        </span>
                    </a>
                    <ul class="menu-content">
                        @can('Department')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('department.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Departments
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('City')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('city.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        City
                                    </span>
                                </a>
                            </li>
                            @endcan
                        @can('Designation')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('designation.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Designations
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('User_Roles')
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('role.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        User Roles
                                    </span>
                                </a>
                            </li>
                        @endcan


                        {{-- @can('Config') --}}

				
                        @if (Auth::user()->user_type == 9)

			 <li>
                                <a class="d-flex align-items-center" href="{{ route('config.index') }}">
                                    <i data-feather="circle"></i>
                                    <span class="menu-item text-truncate">
                                        Config
                                    </span>
                                </a>
                            </li>
                           
                        @endif
                        {{-- @endcan --}}
                    </ul>
                </li>
            {{-- @endcan --}}
        </ul>
    </div>
</div>
{{-- @dump(auth()->user()->can('User_Roles')) --}}
{{-- @dump(Auth::user()->hasAnyPermission(['User_Roles']) , Auth::user()->getRoleNames() ,Auth::user()->permissions->toArray()) --}}
<!-- END: Main Menu-->
