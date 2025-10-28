@php
    use App\Helpers\MasterFormsHelper;
    use App\Models\Attendence;
    use App\Models\ProductPrice;
    use App\Models\ProductFlavour;
    use App\Models\Distributor;
 
    $master = new MasterFormsHelper();

    $flavours = ProductFlavour::pluck('flavour_name', 'id')->toArray();
    $distributors = Distributor::pluck('distributor_name', 'id')->where('status',1)->toArray();


@endphp

@php
    
@endphp

<style>
    .table-container {
        height: 400px;
        /* Adjust the height as needed */
        overflow: auto;
    }

    /* Fix the table heading */
    #table_data thead {
        position: sticky;
        top: 0;
        background-color: white;
        /* Set to match your table's styling */
        z-index: 1;
    }
    @media print {
    /* Scroll/overflow hatane ke liye */
    .no-scroll {
        overflow: visible !important;
        height: auto !important;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #000;
        padding: 5px;
    }
}
</style>
<div class="table-responsive table-container no-scroll">
    <table id="table_data" class="table table-bordered">
        <thead>
            <thead>
                <tr class="text-center">
                    <th colspan="4"></th>
                    <th style="background-color: rgb(147 190 147)" colspan="@if($type=='') 3 @else 2 @endif">Opening</th>
                    <th style="background-color: #c0c0d7" colspan="@if($type=='') 3 @else 2 @endif">Purchase</th>
                    <th style="background-color: #edc7a3" colspan="@if($type=='') 3 @else 2 @endif">Market Return</th>
                    <th style="background-color: #9999dd" colspan="@if($type=='') 3 @else 2 @endif">Transfer Received</th>
                    <th style="background-color: #8080803b" colspan="@if($type=='') 3 @else 2 @endif">Available Sales For Sales</th>
                    <th style="background-color: #edc7a3" colspan="@if($type=='') 3 @else 2 @endif">Stock Transfer</th>
                    <th style="background-color: #9999dd" colspan="@if($type=='') 3 @else 2 @endif">Sales (Execution Only) </th>
                    
                    <th style="background-color: #12c52a" colspan="@if($type=='') 3 @else 2 @endif">Closing </th>
                </tr>
            </thead>
            <tr>
                <th>S.No</th>
                <th>Distributor Name</th>
                <th>Prouct Name</th>
                <th>Flavour Name</th>
                <!-- <th>Distributor Name</th> -->
                {{-- opening --}}
                @if($type!='carton')<th>QTY </th>@endif
                @if($type!='qty')<th>QTY Carton</th>@endif
                <th>Value </th>

                {{-- Purchase --}}
               @if($type!='carton')<th>QTY </th>@endif
                @if($type!='qty')<th>QTY Carton</th>@endif
                <th>Value </th>

                {{-- Transfer Recived --}}
                @if($type!='carton')<th>QTY </th>@endif
                @if($type!='qty')<th>QTY Carton</th>@endif
                <th>Value </th>

                {{-- Return --}}
                @if($type!='carton')<th>QTY </th>@endif
                @if($type!='qty')<th>QTY Carton</th>@endif
                <th>Value </th>

                


                {{-- Avaiable --}}
                @if($type!='carton')<th>QTY </th>@endif
                @if($type!='qty')<th>QTY Carton</th>@endif
                <th>Value </th>

                {{-- stock transfer --}}
               @if($type!='carton')<th>QTY </th>@endif
                @if($type!='qty')<th>QTY Carton</th>@endif
                <th>Value </th>

                {{-- Sales --}}
                @if($type!='carton')<th>QTY </th>@endif
                @if($type!='qty')<th>QTY Carton</th>@endif
                <th>Value </th>

                


                {{-- Closing --}}


                @if($type!='carton')<th>QTY </th>@endif
                @if($type!='qty')<th>QTY Carton</th>@endif
                <th>Value </th>


            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
                $total_check=0;
                $total_opening_qty = 0;
                $total_opening_qty_previous=0;
                $total_opening_carton_qty_previous=0;
                $total_opening_carton_qty = 0;
                $total_opening_value = 0;
                $total_opening_value_previous=0;

                $total_purchase_qty = 0;
                $total_purchase_qty_previous = 0;
                $total_purchase_carton_qty = 0;
                $total_purchase_carton_qty_previous = 0;
                $total_purchase_value = 0;
                $total_purchase_value_previous = 0;

                $total_sales_qty = 0;
                $total_sales_carton_qty=0;
                $total_sales_qty_previous = 0;
                $total_sales_carton_qty_previous=0;
                $total_sales_value = 0;
                $total_sales_value_previous=0;

                $total_return_qty = 0;
                $total_return_carton_qty = 0;
                $total_return_qty_previous = 0;
                $total_return_carton_qty_previous = 0;
                $total_return_value = 0;
                $total_return_value_previous=0;

                
                
                $total_available_qty = 0;
                $total_available_carton_qty = 0;
                $total_available_qty_previous = 0;
                $total_available_carton_qty_previous = 0;
                $total_available_amount = 0;
                $total_available_amount_previous = 0;
                
                $total_unpacking_in_qty = 0;
                $total_unpacking_in_amount = 0;
                
                $total_unpacking_out_qty = 0;
                $total_unpacking_out_amount = 0;
                
                
                $total_transfer_recieved_qty=0;
                $total_transfer_recieved_qty_previous=0;
                $total_transfer_recieved_carton_qty=0;
                $total_transfer_recieved_carton_qty_previous=0;
                $total_transfer_recieved_amount=0;
                $total_transfer_recieved_amount_previous=0;
                
                $total_transfer_qty = 0;
                $total_transfer_carton_qty = 0;
                $total_transfer_amount = 0;
                
                $total_closing_carton = 0;
                $total_closing_qty = 0;
                $total_closing_carton_qty=0;
                $total_closing_amount = 0;
                $total_closing_qty_new= 0;
                $total_closing_carton_qty_new=0;
                $total_closing_amount_new = 0;
                $total_closing_qty_new_previous= 0;
                $total_closing_carton_qty_new_previous=0;
                $total_closing_amount_new_previous = 0;


     
            @endphp
            @foreach ($result as $item)
                @php
                    // get packing from products
                    $packing = $item->packing_size > 0 ? $item->packing_size : 1;

                    // get discount percentage from distributor

                    $discount_amount = ($item->sales_price / 100) * $item->max_discount;
                    $price = $item->sales_price - $discount_amount;


                    if(!empty($from)){

                        $get_purchase_previous = $preparedPurchaseDataPrevious[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                        $purchase_qty_previous = $get_purchase_previous['main_qty'];
                        $purchase_amount_previous = $get_purchase_previous['main_amount'];

                        $purchase_qty_display_previous =0;
                        $purchase_carton_qty_display_previous =0;  
                        $purchase_unit_display_previous = '';
                        
                        $get_opening_previous = $preparedData[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                        $opening_qty_previous = $get_opening_previous['main_qty'];
                        $open_amount_previous = $get_opening_previous['main_amount'];
                        

                        $open_qty_display_previous =0; 
                        $open_carton_qty_display_previous =0;   
                        $open_unit_display_previous = ''; 

                        foreach (explode(',', $opening_qty_previous) as $val) {
                        
                                $qty_explode = explode('=>', $val);
                                $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                                $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
                
                                    $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
                                if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                                if(str_replace(' ', '', $unit)=='Bo'){
                                    $unit='Box';
                                }
                    
                                if(str_replace(' ', '', $unit)=='Carton'){
                                    $open_carton_qty_display_previous+= $qty;
                                    $total_opening_carton_qty_previous += $qty;
                                   
                                    $open_qty_display_previous += ($qty * $pcsPerCarton);
                                    $total_opening_qty_previous += ($qty * $pcsPerCarton);
                                    
                                                }else{
                                                    $open_qty_display_previous += $qty;
                                                    $total_opening_qty_previous += $qty;
                                   
                                    $open_carton_qty_display_previous+= ($qty / $pcsPerCarton);
                                    $total_opening_carton_qty_previous += ($qty / $pcsPerCarton);
                                   
                                }

                                
                                
                                
                            
                        }
                        
                        
                        $total_opening_value_previous += (float)$open_amount_previous;

                        

                 
                        foreach (explode(',', $purchase_qty_previous) as $val) {
                            
                                $qty_explode = explode('=>', $val);
                                $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                                $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
                                $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
                                            if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                                            if(str_replace(' ', '', $unit)=='Bo'){
                                                $unit='Box';
                                            }
                                            
                                            if(str_replace(' ', '', $unit)=='Carton'){
                                                $purchase_carton_qty_display_previous+=$qty;
                                                $total_purchase_carton_qty_previous +=$qty;
                                
                                $purchase_qty_display_previous += ($qty * $pcsPerCarton);
                                $total_purchase_qty_previous += ($qty * $pcsPerCarton);
                                
                                            }else{
                                                $purchase_qty_display_previous += $qty;
                                                $total_purchase_qty_previous += $qty;
                                
                                $purchase_carton_qty_display_previous+= ($qty / $pcsPerCarton);
                                $total_purchase_carton_qty_previous += ($qty / $pcsPerCarton);
                    
                                }
                                
                            
                        }

                        $total_purchase_value_previous += (float)$purchase_amount_previous;

                        $get_sales_return_previous = $preparedSalesReturnDataPrevious[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                        $sales_return_qty_previous = $get_sales_return_previous['main_qty'];
                        $return_amount_previous = $get_sales_return_previous['main_amount'];

                        
                        $return_qty_display_previous = 0;
                        $return_qty_carton_display_previous = 0;  
                        $return_unit_display_previous = ''; 
                    
                    foreach (explode(',', $sales_return_qty_previous) as $val) {
                       
                            $qty_explode = explode('=>', $val);
                            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
                            $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
                            if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                            if(str_replace(' ', '', $unit)=='Bo'){
                                    $unit='Box';
                                }            
                                        if(str_replace(' ', '', $unit)=='Carton'){
                                            $return_qty_carton_display_previous+=$qty;
                                            $total_return_carton_qty_previous += $qty;
                            
                            $return_qty_display_previous += ($qty * $pcsPerCarton);
                            $total_return_qty_previous += ($qty * $pcsPerCarton);
                            
                                        }else{
                                            $return_qty_display_previous += $qty;
                                            $total_return_qty_previous += $qty;
                            
                            
                            $return_qty_carton_display_previous+= ($qty / $pcsPerCarton);
                            $total_return_carton_qty_previous += ($qty / $pcsPerCarton);
				
                            }
                            
                            
                           

                    }
                    
                    $total_return_value_previous += (float)$return_amount_previous;
                        
                        


                        $get_transfer_received_previous = $preparedTransferRecievedDataPrevious[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                        $transfer_received_qty_previous = $get_transfer_received_previous['main_qty'];
                        $transfer_received_amount_previous = $get_transfer_received_previous['main_amount'];


                        $transfer_recieved_qty_display_previous = 0; 
                    $transfer_recieved_qty_carton_display_previous = 0;  
                    $transfer_recieved_unit_display_previous = ''; 
                    
                    foreach (explode(',', $transfer_received_qty_previous) as $val) {
                        
                            $qty_explode = explode('=>', $val);
                            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
			                $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
                            if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                            if(str_replace(' ', '', $unit)=='Bo'){
                                    $unit='Box';
                                }
                            if(str_replace(' ', '', $unit)=='Carton'){
                                $total_transfer_recieved_carton_qty_previous += $qty;
                                $transfer_recieved_qty_carton_display_previous += $qty;
				
				  $total_transfer_recieved_qty_previous += ($qty * $pcsPerCarton);
				  $transfer_recieved_qty_display_previous += ($qty * $pcsPerCarton);
				
                            }else{
                                $total_transfer_recieved_qty_previous += $qty;
                                $transfer_recieved_qty_display_previous += $qty;
				
				  $total_transfer_recieved_carton_qty_previous += ($qty / $pcsPerCarton);
				  $transfer_recieved_qty_carton_display_previous += ($qty / $pcsPerCarton);
				
                            }
                            
                            
                           
                        
                    }
                    
                    $total_transfer_recieved_amount_previous+=(float)$transfer_received_amount_previous;



                        $get_sales_previous = $preparedSalesDataPrevious[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                        $sales_qty_previous = $get_sales_previous['main_qty'];
                        $sales_amount_previous = $get_sales_previous['main_amount'];


                        $sales_qty_display_previous = 0;  
                        $sales_qty_carton_display_previous=0;
                        $sales_unit_display_previous = ''; 
                        
                        foreach (explode(',', $sales_qty_previous) as $val) {
                            
                                $qty_explode = explode('=>', $val);
                                $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                                $total_check+=$qty;
                                $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
                    
                                $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
                                if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                                    if(str_replace(' ', '', $unit)=='Bo'){
                                        $unit='Box';
                                    }
                                    if(str_replace(' ', '', $unit)=='Carton'){
                                            $sales_qty_carton_display_previous += $qty;
                                            $total_sales_carton_qty_previous += $qty;
                        
                                            $sales_qty_display_previous += ($qty * $pcsPerCarton);
                                            $total_sales_qty_previous += ($qty * $pcsPerCarton);
                            
                                        }else{
                                            $sales_qty_display_previous += $qty;
                                            $total_sales_qty_previous += $qty;
                    
                                            $sales_qty_carton_display_previous += ($qty / $pcsPerCarton);
                                            $total_sales_carton_qty_previous += ($qty / $pcsPerCarton);
                
                                    }
                            
                                
                                
                        
                        }
                    
                        $total_sales_value_previous += (float)$sales_amount_previous;
                        

                        $available_qty_display_previous = $return_qty_display_previous+$open_qty_display_previous+$purchase_qty_display_previous+$transfer_recieved_qty_display_previous; 
                        $available_qty_carton_display_previous=$return_qty_carton_display_previous+$open_carton_qty_display_previous+$transfer_recieved_qty_carton_display_previous+$purchase_carton_qty_display_previous; 
                        $available_amount_previous = $return_amount_previous+$open_amount_previous+$purchase_amount_previous;    
                        
                        $total_available_amount_previous+=$available_amount_previous;
                        $total_available_qty_previous+=$available_qty_display_previous;
                        $total_available_carton_qty_previous+=$available_qty_carton_display_previous;



                        $closing_qty_display_new_previous = ($available_qty_display_previous ) - $sales_qty_display_previous;
                        $closing_qty_carton_display_new_previous = ($available_qty_carton_display_previous) - $sales_qty_carton_display_previous;
                        $closing_amount_new_previous = ($available_amount_previous) - $sales_amount_previous;




                        $total_closing_qty_new_previous += ($available_qty_display_previous ) - $sales_qty_display_previous;
                        $total_closing_carton_qty_new_previous += ($available_qty_carton_display_previous) - $sales_qty_carton_display_previous;
                        $total_closing_amount_new_previous += ($available_amount_previous) - $sales_amount_previous;



                    }else{
                        $get_opening = $preparedData[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                        $opening_qty = $get_opening['main_qty'];
                        $open_amount = $get_opening['main_amount']; 
                    }


                    
                    $get_purchase = $preparedPurchaseData[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                    $purchase_qty = $get_purchase['main_qty'];
                    $purchase_amount = $get_purchase['main_amount'];

                    $get_sales_return = $preparedSalesReturnData[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                    
             
                    $sales_return_qty = $get_sales_return['main_qty'];
                    $return_amount = $get_sales_return['main_amount'];

                    
                    $get_transfer_received = $preparedTransferRecievedData[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                    
                    $transfer_received_qty = $get_transfer_received['main_qty'];
                    $transfer_received_amount = $get_transfer_received['main_amount'];



                    
                    //$get_unpacking_in = $preparedUnpackingInData[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                    
                    //$unpacking_in_qty = $get_unpacking_in['main_qty'];
                    //$unpacking_in_amount = $get_unpacking_in['main_amount'];

                    $unpacking_in_qty=0;
                    $unpacking_in_amount =0;


                    
                    //$get_available = $preparedAvailableData[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                    
                    //$available_qty = ($get_available['main_qty']);
                    //$available_amount = ($get_available['main_amount']);

                    
                    $get_transfer = $preparedTransferData[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                    
                    $transfer_qty = $get_transfer['main_qty'];
                    $transfer_amount = $get_transfer['main_amount'];




                    
                    $get_sales = $preparedSalesData[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                    
                    $sales_qty = $get_sales['main_qty'];
                    $sales_amount = $get_sales['main_amount'];


                    
                    //$get_unpacking_out = $preparedUnpackingOutData[$item->product_id][$item->flavour_id][$item->distributor_id] ?? ['main_qty' => '', 'main_amount' => 0];
                    
                    //$unpacking_out_qty = $get_unpacking_out['main_qty'];
                    //$unpacking_out_amount = $get_unpacking_out['main_amount'];
                    $unpacking_out_qty=0;
                    $unpacking_out_amount =0;
                    // closing amount and qty

                    // $closing_qty = '';
                    // $closing_amount = 0;
                    // foreach ($master->get_product_price($item->product_id) as $k => $productPrice) {
                    //     $qty = MasterFormsHelper::get_Stock(
                    //         $item->product_id,
                    //         $item->flavour_id,
                    //         $productPrice->uom_id,
                    //         $item->distributor_id,
                    //     );
                    //     // dump($qty);
                    //     $uom_name = $master->uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
                    //     if ($qty > 0) {
                    //         $closing_qty .= ($closing_qty ? ' , ' : '') . number_format($qty) . 'x' . $uom_name;
                    //         $value = $qty * $productPrice->trade_price;
                    //         $closing_amount += $value;
                    //     }
                    // }

                    $closing_amount = 0;
		    
                    $carton_qty = [];
		    //$pcsPerCartonClosing = ProductPrice::select('pcs_per_carton')->where('product_id' , $item->product_id)->where('uom_id','!=',7)->where('status', 1)->where('start_date' ,'<=', date('Y-m-d'))->orderBy('start_date','desc')->value('pcs_per_carton');
                    //foreach ($master->get_product_price($item->product_id) as $k => $productPrice) {
                        //$qty = MasterFormsHelper::get_Stock_with_date(
                            //$item->product_id,
                            //$item->flavour_id,
                            //$productPrice->uom_id,
                            //$item->distributor_id,
                            //$from,
                            //$to,
                        //);
                        
                       	 //$pcs_per_carton =  $productPrice->pcs_per_carton == 0 ? 1 : $productPrice->pcs_per_carton;
                    	
                   
                        //if ($qty > 0) {

                            //$value = $qty * $productPrice->trade_price;
                            //$closing_amount += $value;
			    
                            //if ($qty >= $productPrice->pcs_per_carton && $productPrice->uom_id != 7) {

                               
                              
                                //$cartons = floor($qty / $pcs_per_carton);
				//$cartons = ($qty / $pcs_per_carton);


                                //$carton_qty[7] = isset($carton_qty[7]) ? $carton_qty[7]+$cartons  : $cartons ;

                                // Calculate the remaining pieces
                                //$qty = $qty % $pcs_per_carton;

                            //}
				
                            //$carton_qty[$productPrice->uom_id] = isset($carton_qty[$productPrice->uom_id]) ? ($carton_qty[$productPrice->uom_id]+$qty) : $qty;



                        //}
                    //}
                    
                    //$closing_qty = '';
                    
                    //foreach ($carton_qty as $uom_id => $qty) {
                        //$uom_name = $master->uom_name($uom_id); // Get UOM name for each product_price UOM
			//$qty_explode=explode('x',$qty);
			//if(isset($qty_explode[0])){
				//$qty=$qty_explode[0];	
			//}
			//if(isset($qty_explode[1])){
				//$pcsPerCarton=$qty_explode[1];
				//echo $pcsPerCarton."<br/>";	
			//}

                        //$closing_qty .= ($closing_qty ? ' , ' : '') . number_format($qty). 'x' . $uom_name; 
                        //. 'x' . $uom_name;
                    //}
                    // dump($carton_qty);
                    // dump($opening_qty , $item->product_id, $item->flavour_id , $item->distributor_id); 
                    
                    
                    
                    $closing_qty_display = 0;
                    $closing_qty_carton_display = 0;  
                    //$closing_unit_display = ''; 
                    
                    //foreach (explode(',', $closing_qty) as $val) {
                            
                            //$qty_explode = explode('x', $val);
                            //$qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                            //$unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
			    //$pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 0;
			    
			    //if($pcsPerCartonClosing ==0 || $pcsPerCartonClosing  ==''){ $pcsPerCartonClosing=1; }
                            //if(str_replace(' ', '', $unit)=='Bo'){
                              //  $unit='Box';
                            //}
                            //if(str_replace(' ', '', $unit)=='Carton'){
                                //$total_closing_carton_qty += $qty;
                                //$closing_qty_carton_display += $qty;
				//if($qty > 0){
				  
				  //$closing_qty_display += ($qty * $pcsPerCartonClosing);
				  //$total_closing_qty += ($qty * $pcsPerCartonClosing);
                                //}
                            //}else{
                                //$total_closing_qty += $qty;
                                //$closing_qty_display += $qty;
				//if($qty > 0){
				 
				 //$total_closing_carton_qty += ($qty / $pcsPerCartonClosing);
                                 //$closing_qty_carton_display += ($qty / $pcsPerCartonClosing);
				//}
                            //}
                           
                            //$closing_unit_display .= $unit . '<br/>';
                        
                    //}
                    //$total_closing_amount+=(float)$closing_amount;
                    
                     
                    $open_qty_display =0; 
                    $open_carton_qty_display =0;   
                    $open_unit_display = ''; 
                    

                    if(!empty($from)){
                        $open_qty_display= $closing_qty_display_new_previous;
                        $open_carton_qty_display += $closing_qty_carton_display_new_previous;
                        $open_amount = $closing_amount_new_previous;
                        $total_opening_carton_qty += $open_carton_qty_display;
                        $total_opening_qty += $open_qty_display;
                    }else{
                        
                        foreach (explode(',', $opening_qty) as $val) {
                        
                                $qty_explode = explode('=>', $val);
                                $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                                $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
                
                                    $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
                                if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                                if(str_replace(' ', '', $unit)=='Bo'){
                                    $unit='Box';
                                }
                    
                                if(str_replace(' ', '', $unit)=='Carton'){
                                    $open_carton_qty_display+= $qty;
                                    $total_opening_carton_qty += $qty;
                                    
                                    $open_qty_display += ($qty * $pcsPerCarton);
                                    $total_opening_qty += ($qty * $pcsPerCarton);
                                    
                                                }else{
                                                    $open_qty_display += $qty;
                                                    $total_opening_qty += $qty;
                                    
                                    $open_carton_qty_display+= ($qty / $pcsPerCarton);
                                    $total_opening_carton_qty += ($qty / $pcsPerCarton);
                                    
                                }

                                
                                
                                $open_unit_display .= $unit . '<br/>';
                            
                        }
                        
                        
                        

                    }
                    
                    $total_opening_value += (float)$open_amount;
                    
                     
                    $purchase_qty_display =0;
                    $purchase_carton_qty_display =0;  
                    $purchase_unit_display = '';
                     
                 
                    foreach (explode(',', $purchase_qty) as $val) {
                        
                            $qty_explode = explode('=>', $val);
                            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
			    $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
                            if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
				if(str_replace(' ', '', $unit)=='Bo'){
                                $unit='Box';
                            }
                            
                            if(str_replace(' ', '', $unit)=='Carton'){
                                $purchase_carton_qty_display+=$qty;
                                $total_purchase_carton_qty +=$qty;
				//if($qty > 0){
				  $purchase_qty_display += ($qty * $pcsPerCarton);
				  $total_purchase_qty += ($qty * $pcsPerCarton);
				//}
                            }else{
                                $purchase_qty_display += $qty;
                                $total_purchase_qty += $qty;
				//if($qty > 0){
				  $purchase_carton_qty_display+= ($qty / $pcsPerCarton);
				  $total_purchase_carton_qty += ($qty / $pcsPerCarton);
				//}
                            }
                            if(str_replace(' ', '', $unit)=='Bo'){
                                $unit='Box';
                            }
                            $purchase_unit_display .= $unit . '<br/>';
                        
                    }
                    
                    
                    
                    $total_purchase_value += (float)$purchase_amount;
                    
                    
                
                    
                    $sales_qty_display = 0;  
                    $sales_qty_carton_display=0;
                    $sales_unit_display = ''; 
                    
                    foreach (explode(',', $sales_qty) as $val) {
                        
                            $qty_explode = explode('=>', $val);
                            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
				
			                $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
                            if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                                if(str_replace(' ', '', $unit)=='Bo'){
                                    $unit='Box';
                                }
                                if(str_replace(' ', '', $unit)=='Carton'){
                                        $sales_qty_carton_display += $qty;
                                        $total_sales_carton_qty += $qty;
                       
                                        $sales_qty_display += ($qty * $pcsPerCarton);
                                        $total_sales_qty += ($qty * $pcsPerCarton);
                        
                                    }else{
                                        $sales_qty_display += $qty;
                                        $total_sales_qty += $qty;
				
                                        $sales_qty_carton_display += ($qty / $pcsPerCarton);
                                        $total_sales_carton_qty += ($qty / $pcsPerCarton);
			
                                }
                           
                            
                            $sales_unit_display .= $unit . '<br/>';
                       
                    }
                   
                    $total_sales_value += (float)$sales_amount;

                    
                    $return_qty_display = 0;
                    $return_qty_carton_display = 0;  
                    $return_unit_display = ''; 
                    
                    foreach (explode(',', $sales_return_qty) as $val) {
                       
                            $qty_explode = explode('=>', $val);
                            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
			    $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                            if(str_replace(' ', '', $unit)=='Bo'){
                                $unit='Box';
                            }
                            if(str_replace(' ', '', $unit)=='Carton'){
                                $return_qty_carton_display+=$qty;
                                $total_return_carton_qty += $qty;
				//if($qty > 0){
				  $return_qty_display += ($qty * $pcsPerCarton);
				  $total_return_qty += ($qty * $pcsPerCarton);
				//}
                            }else{
                                $return_qty_display += $qty;
                                $total_return_qty += $qty;
				//if($qty > 0){
				  
				  $return_qty_carton_display+= ($qty / $pcsPerCarton);
				  $total_return_carton_qty += ($qty / $pcsPerCarton);
				//}
                            }
                            
                            
                            $return_unit_display .= $unit . '<br/>';

                    }
                    
                    $total_return_value += (float)$return_amount;
                    
                    
                    $available_qty_display = 0; 
                    $available_qty_carton_display=0; 
                    $available_unit_display = '';
                    
                    
                    
                    
                    //foreach (explode(',', $available_qty) as $val) {
                        
                            //$qty_explode = explode('=>', $val);
                            //$qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                            //$unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
			    //$pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
//if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                            //if(str_replace(' ', '', $unit)=='Bo'){
                                //$unit='Box';
                           // }
                            //if(str_replace(' ', '', $unit)=='Carton'){
                                //$available_qty_carton_display += $qty;
                                //$total_available_carton_qty += $qty;
				//if($qty > 0){
				  //$available_qty_display += ($qty * $pcsPerCarton);
				  //$total_available_qty += ($qty * $pcsPerCarton);
				//}
                            //}else{
                                //$available_qty_display += $qty;
                                //$total_available_qty += $qty;
				//if($qty > 0){
				  //$available_qty_carton_display += ($qty / $pcsPerCarton);
				  //$total_available_carton_qty += ($qty / $pcsPerCarton);
				//}
                            //}
                            
                            
                            //$available_unit_display .= $unit . '<br/>';
                       
                    //}
                   
                    //$total_available_amount+=(float)$available_amount;
                    
                    
                    
                    $transfer_qty_display = 0; 
                    $transfer_qty_carton_display = 0;  
                    $transfer_unit_display = ''; 
                    
                    foreach (explode(',', $transfer_qty) as $val) {
                       
                            $qty_explode = explode('=>', $val);
                            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
			    $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                            if(str_replace(' ', '', $unit)=='Bo'){
                                $unit='Box';
                            }

                            if(str_replace(' ', '', $unit)=='Carton'){
                                $total_transfer_carton_qty += $qty;
                                $transfer_qty_carton_display += $qty;
				//if($qty > 0){
				  $total_transfer_qty += ($qty * $pcsPerCarton);
				  $transfer_qty_display += ($qty * $pcsPerCarton);
				//}
                            }else{
                                $total_transfer_qty += $qty;
                                $transfer_qty_display += $qty;
				//if($qty > 0){
				  $total_transfer_carton_qty += ($qty / $pcsPerCarton);
				  $transfer_qty_carton_display += ($qty / $pcsPerCarton);
				//}
                            }
                            
                            
                            $transfer_unit_display .= $unit . '<br/>';
                        
                    }
                    
                    $total_transfer_amount+=(float)$transfer_amount;
                    
                    $total_unpacking_in_qty += (float)$unpacking_in_qty;
                    $total_unpacking_in_amount+= (float)$unpacking_in_amount;
                    
                    $total_unpacking_out_qty += (float)$unpacking_out_qty;
                    $total_unpacking_out_amount+=(float)$unpacking_out_amount;
                    
                    
                    
                    
                    $transfer_recieved_qty_display = 0; 
                    $transfer_recieved_qty_carton_display = 0;  
                    $transfer_recieved_unit_display = ''; 
                    
                    foreach (explode(',', $transfer_received_qty) as $val) {
                        
                            $qty_explode = explode('=>', $val);
                            $qty = isset($qty_explode[0]) ? (float)$qty_explode[0] : 0;
                            $unit = isset($qty_explode[1]) ? $qty_explode[1] : '-';
			    $pcsPerCarton = isset($qty_explode[2]) ? (float)$qty_explode[2] : 1;
if($pcsPerCarton ==0 || $pcsPerCarton  ==''){ $pcsPerCarton=1; }
                            if(str_replace(' ', '', $unit)=='Bo'){
                                $unit='Box';
                            }
                            if(str_replace(' ', '', $unit)=='Carton'){
                                $total_transfer_recieved_carton_qty += $qty;
                                $transfer_recieved_qty_carton_display += $qty;
				//if($qty > 0){
				  $total_transfer_recieved_qty += ($qty * $pcsPerCarton);
				  $transfer_recieved_qty_display += ($qty * $pcsPerCarton);
				//}
                            }else{
                                $total_transfer_recieved_qty += $qty;
                                $transfer_recieved_qty_display += $qty;
				//if($qty > 0){
				  $total_transfer_recieved_carton_qty += ($qty / $pcsPerCarton);
				  $transfer_recieved_qty_carton_display += ($qty / $pcsPerCarton);
				//}
                            }
                            
                            
                            $transfer_recieved_unit_display .= $unit . '<br/>';
                        
                    }
                    
                    $total_transfer_recieved_amount+=(float)$transfer_received_amount;
                    
                    
                $available_qty_display = $return_qty_display+$open_qty_display+$purchase_qty_display+$transfer_recieved_qty_display; 
                $available_qty_carton_display=$return_qty_carton_display+$open_carton_qty_display+$transfer_recieved_qty_carton_display+$purchase_carton_qty_display; 
                $available_amount = $return_amount+$open_amount+$purchase_amount;    
                
                $total_available_amount+=$available_amount;
                $total_available_qty+=$available_qty_display;
                $total_available_carton_qty+=$available_qty_carton_display;
                    
                    
                    
                @endphp
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $item->distributor_name }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $flavours[$item->flavour_id] ?? 'N/A' }}</td>
                    <!-- <td>{{ $distributors[$item->distributor_id] ?? 'N/A' }}</td> -->

                    {{-- opening --}}


                    @if($type!='carton')<td>{!! number_format($open_qty_display,0) !!}</td>@endif
                    @if($type!='qty')<td>{!! number_format($open_carton_qty_display,2) !!}</td>@endif
                    <td>{{ number_format($open_amount, 0) }}</td>

                    {{-- Purchase --}}

                     @if($type!='carton')<td>{!! number_format($purchase_qty_display,0) !!}</td>@endif
                    @if($type!='qty')<td>{!! number_format($purchase_carton_qty_display,2) !!}</td>@endif
                    <td>{{ number_format($purchase_amount,0) }}</td>

                    {{-- Return --}}

                    
                     @if($type!='carton')<td>{!! number_format($return_qty_display,0) !!}</td>@endif
                    @if($type!='qty')<td>{!! number_format($return_qty_carton_display,2) !!}</td>@endif
                    <td>{{ number_format($return_amount, 0) }}</td>

                    {{-- Transfer Recived --}}

                    
                     @if($type!='carton')<td>{!! number_format($transfer_recieved_qty_display,0) !!}</td>@endif
                    @if($type!='qty')<td>{!! number_format($transfer_recieved_qty_carton_display,2) !!}</td>@endif
                    <td>{{ number_format($transfer_received_amount, 0) }}</td>


                    

                    {{-- Available --}}

                    
                     @if($type!='carton')<td>{!! number_format($available_qty_display,0) !!}</td>@endif
                    @if($type!='qty')<td>{!! number_format($available_qty_carton_display,2) !!}</td>@endif
                    <td>{{ number_format($available_amount, 0) }}</td>


                    {{-- Transfer --}}

                   
                    @if($type!='carton')<td>{!! number_format($transfer_qty_display,0) !!}</td>@endif
                    @if($type!='qty')<td>{!! number_format($transfer_qty_carton_display,2) !!}</td>@endif
                    <td>{{  number_format($transfer_amount, 0) }}</td>


                    {{-- Sales --}}

                    
                     @if($type!='carton')<td>{!! number_format($sales_qty_display-$return_qty_display,0) !!}</td>@endif
                    @if($type!='qty')<td>{!! number_format($sales_qty_carton_display-$return_qty_carton_display,2) !!}</td>@endif
                    <td>{{ number_format($sales_amount-$return_amount, 0) }}</td>

<!-- new code  -->
                    @php
                        $closing_qty_display_new = ($available_qty_display) - ($sales_qty_display - $return_qty_display) - ($transfer_qty_display);
                        $closing_qty_carton_display_new = ($available_qty_carton_display) - ($sales_qty_carton_display - $return_qty_carton_display) - ($transfer_qty_carton_display);
                        $closing_amount_new = ($available_amount) - ($sales_amount - $return_amount) - ($transfer_amount);
                        
                        if (round($closing_qty_display_new, 2) == 0) {

                        $closing_amount_new = 0;


                        }
                            $total_closing_qty_new += ($available_qty_display) - ($sales_qty_display - $return_qty_display) - ($transfer_qty_display);
                            $total_closing_carton_qty_new += ($available_qty_carton_display) - ($sales_qty_carton_display - $return_qty_carton_display) - ($transfer_qty_carton_display);
                            $total_closing_amount_new += ($available_amount) - ($sales_amount - $return_amount) - ($transfer_amount);

                        if (round($total_closing_qty_new, 2) == 0) {

                        $total_closing_amount_new = 0;


                        }
                    @endphp


                    @if($type!='carton')<td>{!! number_format($closing_qty_display_new,0) !!}</td>@endif
                    @if($type!='qty')<td>{!! number_format($closing_qty_carton_display_new,2) !!}</td>@endif
                    <td>{{ number_format($closing_amount_new, 0) }}</td>
                    <!-- new code  -->

                    <!-- old code  -->
                    <!-- @if($type!='carton')<td>{!! number_format($closing_qty_display,0) !!}</td>@endif
                    @if($type!='qty')<td>{!! number_format($closing_qty_carton_display,2) !!}</td>@endif
                    <td>{{ number_format($closing_amount, 0) }}</td> -->

                     <!-- old code  -->

                </tr>
                @php
                    $i++;
                @endphp
            @endforeach

        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-center">Total</th>
                @if($type!='carton')<th>{{ number_format($total_opening_qty, 0) }}</th>@endif
                @if($type!='qty')<th>{{ number_format($total_opening_carton_qty, 2) }}</th>@endif
                <th>{{ number_format($total_opening_value, 0) }}</th>
                @if($type!='carton')<th>{{ number_format($total_purchase_qty, 0) }}</th>@endif
                @if($type!='qty')<th>{{ number_format($total_purchase_carton_qty, 2) }}</th>@endif
                <th>{{ number_format($total_purchase_value, 0) }}</th>
                
                @if($type!='carton')<th>{{ number_format($total_return_qty, 0) }}</th>@endif
                @if($type!='qty')<th>{{ number_format($total_return_carton_qty, 2) }}</th>@endif
                <th>{{ number_format($total_return_value, 0) }}</th>
                @if($type!='carton') <th>{{ number_format($total_transfer_recieved_qty, 0) }}</th>@endif
                @if($type!='qty') <th>{{ number_format($total_transfer_recieved_carton_qty, 2) }}</th>@endif
                <th>{{ number_format($total_transfer_recieved_amount, 0) }}</th>  
                
                
                
                
                @if($type!='carton')<th>{{ number_format($total_available_qty, 0) }}</th>@endif
               @if($type!='qty') <th>{{ number_format($total_available_carton_qty, 2) }}</th>@endif
                <th>{{ number_format($total_available_amount, 0) }}</th>
                
               @if($type!='carton') <th>{{ number_format($total_transfer_qty, 0) }}</th>@endif
               @if($type!='qty') <th>{{ number_format($total_transfer_carton_qty, 2) }}</th>@endif
                <th>{{ number_format($total_transfer_amount, 0) }}</th>
                
                @if($type!='carton')<th>{{ number_format($total_sales_qty-$total_return_qty, 0) }}</th>@endif
                @if($type!='qty')<th>{{ number_format($total_sales_carton_qty-$total_return_carton_qty, 2) }}</th>@endif
                <th>{{ number_format($total_sales_value-$total_return_value, 0) }}</th>



                <!-- new code  -->


                 @if($type!='carton')<th>{{ number_format($total_closing_qty_new, 0) }}</th>@endif
                
                @if($type!='qty')<th>{{ number_format($total_closing_carton_qty_new, 2) }}</th>@endif
                
                <th>{{ number_format($total_closing_amount_new, 0) }}</th>


                <!-- new code  -->

                <!-- new old code  -->
                <!-- @if($type!='carton')<th>{{ number_format($total_closing_qty, 0) }}</th>@endif
                
                @if($type!='qty')<th>{{ number_format($total_closing_carton_qty, 2) }}</th>@endif
                
                <th>{{ number_format($total_closing_amount, 0) }}</th> -->

                  <!-- new old code  -->
            </tr>
            
        </tfoot>
        
    </table>
</div>
