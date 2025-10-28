<?php

namespace App\Http\Controllers\Backend;

use App\Models\Category;
use App\Models\Brand;
use App\Models\UOM;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\ProductFlavour;
use App\Models\ProductPrice;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PDF;
use Auth;
use App\Helpers\MasterFormsHelper;
use App\Imports\YourImportClass;

class ProductController extends Controller
{

    public $master;
    public function __construct()
    {
        $this->master = new MasterFormsHelper();
        $this->page = 'pages.Products.Product.';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Product $product)
    {
        $product =   $product->where('status', 1)->select('id', 'product_name', 'product_code', 'uom_id', 'category_id', 'brand_id', 'product_type_id')->get();
      
     
        if ($request->ajax()) :
            return view($this->page . 'ProductListAjax', compact('product'));
        endif;
        
        return view($this->page . 'ProductList');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item_code = Product::UniqueNo();
        return  view($this->page . 'AddProduct', compact('item_code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();
        try {

            $input = $request->except('flavour_name' , 'uom_id' , 'retail_price' , 'trade_price','pcs_per_carton');
            $input['product_code'] = Product::UniqueNo();
            // dd($request->file('image') , $request->all());
            $fileName ='';
            if ($request->file('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                // dd( $fileName = $file->getClientOriginalName());
                $file->storeAs('uploads', $fileName, 'public'); // 'uploads' is the directory to store files.
            }
            $input['image'] = $fileName;

            $product = Product::create($input);
            $product_id = $product->id;
// dd($product_id);
            $flavour_data= $request->input('flavour_name');
            foreach ($flavour_data as $flavour_name) {
                ProductFlavour::create([
                    'product_id' => $product_id,
                    'flavour_name' => $flavour_name,
                ]);
            }


            $product_price_data= $request->input('retail_price');
            if ($product_price_data) {
                foreach ($product_price_data as $key => $price) {
                    ProductPrice::create([
                        'product_id' => $product_id,
                        'uom_id' => $request->uom_id[$key],
                        'retail_price' => $request->retail_price[$key],
                        'trade_price' => $request->trade_price[$key],
                        'pcs_per_carton' => $request->pcs_per_carton[$key],
                    ]);
                }
            }



            DB::commit();

            return response()->json(['success' => 'Product Successfully Saved']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error'=>'Oops! There might be a error '. $th->getMessage() . '-' . $th->getLine() ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return  view($this->page . 'EditProduct', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::beginTransaction();
        try {
            // Exclude fields that are not required for the main product update
            $input = $request->except('flavour_name','delete_price_id','product_price_id','flavour_id', 'uom_id' , 'retail_price' , 'trade_price','pcs_per_carton');
            
            $fileName = '';
            // Handle the image upload if it's provided
            if ($request->file('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
    
                // Delete previous image if it exists
                if (Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
    
                // Store the new image
                $file->storeAs('uploads', $fileName, 'public');
            }
            $input['image'] = $fileName;
    
            // Update the product record
            $product_id = $product->id;
            $product->update($input);
    
            // Handle the flavours
            $flavour_data = $request->input('flavour_name');
            if ($flavour_data) {
                foreach ($flavour_data as $key2 => $flavour_name) {
                    // If flavour ID exists, update, else create a new flavour
                    ProductFlavour::updateOrCreate(
                        ['id' => $request->flavour_id[$key2] ?? null], // Check if flavour_id exists
                        [
                            'product_id' => $product_id,
                            'flavour_name' => $flavour_name,
                        ]
                    );
                }
            }
    
            // Handle the price deletion if provided
            if ($request->delete_price_id) {
                $delete_price_id = explode(',', $request->delete_price_id);
                ProductPrice::whereIn('id', $delete_price_id)->update(['status' => 0]);
            }
    
            // Handle the product prices (create new or update existing)
            $product_price_data = $request->input('retail_price');
            if ($product_price_data) {
                foreach ($product_price_data as $key => $price) {
                    // Create or update product price
                    ProductPrice::updateOrCreate(
                        ['id' => $request->product_price_id[$key] ?? null], // If price ID exists, update; otherwise create new
                        [
                            'product_id' => $product_id,
                            'uom_id' => $request->uom_id[$key],
                            'retail_price' => $price,
                            'trade_price' => $request->trade_price[$key],
                            'pcs_per_carton' => $request->pcs_per_carton[$key],
                        ]
                    );
                }
            }
    
            DB::commit();
    
            return response()->json(['success' => 'Updated successfully.']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => 'Oops! There might be an error: ' . $th->getMessage()]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::where('id', $id)->update(['status' => 0]);
        ProductFlavour::where('product_id' , $id)->update(['status' => 0]);
        return response()->json(['success' => 'Deleted Successfully!']);
    }

    public function ProductMasterData()
    {
        // echo "ProductMasterData";

        return  view($this->page . 'ProductMasterData'); //, compact('item_code'));

    }

    public function import_product(Request $request){
        return view($this->page .'ImportProduct');
    }

    public function import_product_store(Request $request){

        DB::beginTransaction();
            try {
                // dd($request->file('file'));
                $file = Excel::toArray(new YourImportClass, $request->file('file') );
                $file = $file[0];
                $shopExistis = [];
                $distributorExistis = [];
                $distributorNotExist = [];
                $zoneNotExist = [];
                $UOMNotExist = [];
                $UomPackingNotExist = [];
                $tsoNotExist = [];
                $ProductExistis = [];
                $distributor_code=0;

                // dd($file , $file[0][1] , $file[0][18], trim("SHOP NAME"));
                // dd($file[0][0] == trim("Product Name") , $file[0][1] == trim("Flavour") , $file[0][2] == trim("Product Category") , $file[0][3] == trim("Product type") , $file[0][4] == trim("Brand") , $file[0][5] == trim("Product Unit") , $file[0][6] == trim("Packing Unit") , $file[0][7] == trim("Locality") , $file[0][8] == trim("SKU")
                // , $file[0][9] == trim("Product Description") , $file[0][10] == trim("Packing Size") , $file[0][11] == trim("Carton Size") , $file[0][12] == trim("QC required") , $file[0][13] == trim("Part #") , $file[0][14] == trim("Width") , $file[0][15] == trim("Height") , $file[0][16] == trim("Length")  , $file[0][17] == trim("Weight")
                // , $file[0][18] == trim("Retail Price Per Piece") , $file[0][19] == trim("Retail Price Per Box/Pouch") , $file[0][20] == trim("Retail Price Per Carton") , $file[0][21] == trim("Trade Price Per Piece") , $file[0][22] == trim("Trade Price Per Box/Pouch") , $file[0][23] == trim("Trade Price Per Carton"));
                if($file[0][0] == trim("Product Name") && $file[0][1] == trim("Flavour") && $file[0][2] == trim("Product Category") && $file[0][3] == trim("Product type") && $file[0][4] == trim("Brand") && $file[0][5] == trim("Product Unit") && $file[0][6] == trim("Trade Price") && $file[0][7] == trim("Retail Price") && $file[0][8] == trim("Locality") && $file[0][9] == trim("SKU")
                && $file[0][10] == trim("Product Description") && $file[0][11] == trim("Packing Size") && $file[0][12] == trim("Carton Size") && $file[0][13] == trim("QC required") && $file[0][14] == trim("Part #") && $file[0][15] == trim("Width") && $file[0][16] == trim("Height") && $file[0][17] == trim("Length")  && $file[0][18] == trim("Weight")){
                    // if($file[0][1] == trim("SHOP NAME") && $file[0][2] == trim("City") && $file[0][3] == trim("State") && $file[0][6] == trim("Route") && $file[0][7] == trim("Day")){
                    foreach ($file as $key => $value) {
                        // dd($value);
                        if($key == 0) continue ;

                        $product_data = Product::where('product_name', $value[0])->first();
                        $product_flavour = null;
                        $product_price = null;
                        $product_name = null;
                        if ($product_data) {
                            $product_name = $product_data->product_name ?? null;
                            $product_id = $product_data->id;
                            $product_flavour = ProductFlavour::where(['product_id' => $product_data->id , 'flavour_name' => $value[1]])->value('flavour_name');
                            $uom_id = UOM::where('uom_name' , $value[5])->value('id');
                            $product_price = ProductPrice::where(['product_id' => $product_data->id , 'uom_id' => $uom_id])->value('uom_id');
                            // if ($product_flavour) {
                            //     # code...
                            // }
                        }
                        if($product_name && $product_price) array_push($ProductExistis,$value[0]) ;
                        if($product_name && $product_price) continue ;


                        // $tso_id = $request->tso_id;
                        // $tso_id = TSO::where('tso_code',$value[2])->value('id');
                        $category_id = Category::where('name' , $value[2])->value('id');
                        if (!$category_id) {
                            $data = [
                                'name' => $value[2],
                            ];

                          
                            $category = Category::create($data);
                            $category_id = $category->id;

                           
                        }
                    
                        $product_type_id = ProductType::where('type_name' , $value[3])->value('id');
                        if (!$product_type_id) {
                            $data = [
                                'type_name' => $value[3],
                            ];
                            $product_type = ProductType::create($data);
                            $product_type_id = $product_type->id;
                        }

                       
                        $brand_id = Brand::where('brand_name' , $value[4])->value('id');
                        if (!$brand_id) {
                            $data = [
                                'brand_name' => $value[4],
                            ];
                            $brand = Brand::create($data);
                            $brand_id = $brand->id;
                        }
                      
                        // $uom_id = UOM::where('uom_name' , $value[5])->value('id');
                        // if(!$uom_id) array_push($UOMNotExist,$value[5]);
                        // if(!$uom_id) continue ;
                        // if (!$uom_id) {
                        //     $data = [
                        //         'uom_name' => $value[4],
                        //     ];
                        //     $uom = UOM::create($data);
                        //     $uom_id = $uom->id;
                        // }

                        // $packing_uom_id = UOM::where('uom_name' , $value[6])->value('id');
                        // if(!$packing_uom_id) array_push($UomPackingNotExist,$value[6]);
                        // if(!$packing_uom_id) continue ;
                        // if (!$packing_uom_id) {
                        //     $data = [
                        //         'uom_name' => $value[5],
                        //     ];
                        //     $weight_uom = UOM::create($data);
                        //     $packing_uom_id = $weight_uom->id;
                        // }
                      
                        if ($product_name == null) {
                            # code...
                            $input['product_code'] =  Product::UniqueNo();
                            $input['product_name'] = $value[0];
                            $input['category_id'] = $category_id;
                            $input['product_type_id'] = $product_type_id;
                            $input['brand_id'] = $brand_id;
                            // $input['uom_id'] = $uom_id;
                            // $input['packing_uom_id'] = $packing_uom_id;
                            // $input['retail_price'] = $value[18];
                            // $input['retail_price_packing'] = $value[19];
                            // $input['retail_price_carton'] = $value[20];
                            // $input['trade_price'] = $value[21];
                            // $input['trade_price_packing'] = $value[22];
                            // $input['trade_price_carton'] = $value[23] ?? 0;
                            // $input['maximum_qty'] = $value[10];
                            // $input['minimum_qty'] = $zone_id;
                            // $input['reorder_qty'] = $value[11];
                            $input['weight'] = $value[18];
                            $input['length'] = $value[17];
                            $input['height'] = $value[16];
                            $input['width'] = $value[15];
                            $input['qc_reuired'] = $value[13] == 'yes' ? 1 : 0;
                            $input['packing_size'] = $value[11];
                            $input['carton_size'] = $value[12];
                            $input['SKU'] = $value[9];
                            $input['locality'] = $value[8] == 'imported' ? 1 : 0;
                            $input['username'] = Auth::user()->name;
                            $input['date'] = date('Y-m-d');
                            $product = Product::create($input);
                            $product_id = $product->id;

                            $produc_flavour = explode(',',$value[1]);
                            foreach ($produc_flavour as $key => $value1) {
                                $data = [
                                    'product_id' => $product_id,
                                    'flavour_name' => $value1,
                                ];
                                $flavour = ProductFlavour::create($data);

                            }

                        }

                       
                        // if ($product_flavour == null) {

                        //     $data = [
                        //         'product_id' => $product_id,
                        //         'flavour_name' => $value[1],
                        //     ];
                        //     $flavour = ProductFlavour::create($data);

                        // }
                        if ($product_price == null) {

                            $uom_id = UOM::where('uom_name' , $value[5])->value('id');
                            // dd($uom_id);
                            $data = [
                                'product_id' => $product_id,
                                'uom_id' => $uom_id,
                                'trade_price' => $value[6],
                                'retail_price' => $value[7],
                                'pcs_per_carton' => $value[19],
                            ];
                            $flavour = ProductPrice::create($data);
                        }
                      
                    
                    }
                   
                    DB::commit();
                    if($distributorExistis){Session::flash('distributorExistis',$distributorExistis);}
                    if($distributorNotExist){Session::flash('distributorNotExist',$distributorNotExist);}
                    if($zoneNotExist){Session::flash('zoneNotExist',$zoneNotExist);}
                    if($UOMNotExist){Session::flash('UOMNotExist',$UOMNotExist);}
                    if($UomPackingNotExist){Session::flash('UomPackingNotExist',$UomPackingNotExist);}
                    if($ProductExistis){Session::flash('ProductExistis',$ProductExistis);}


                    return redirect()->back()->with('success', 'Product Import Successfully');
                }else{
                    // dd('cehcalsd');
                    return redirect()->back()->with('catchError',"Format Not Match");
                }
            } catch (Exception $th) {
                //throw $th;
                DB::rollback();
                return response()->json(['catchError' => $th->getMessage()]);
            }
    }


}
