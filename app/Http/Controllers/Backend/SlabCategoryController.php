<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SlabCategory;
use App\Models\SlabCategoryDetail;
use App\Models\Shop;
use DB;

class SlabCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, SlabCategory $slab_category)
    {
        $slab_category =   $slab_category->where('status', 1)->select('id', 'slab_name', 'description' , 'active' , 'date')->get();
   
        if ($request->ajax()) :
            return view('pages.Products.SlabCategory.SlabCategoryListAjax', compact('slab_category'));
        endif;

       
        return view('pages.Products.SlabCategory.SlabCategoryList');

    
       
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.Products.SlabCategory.AddSlabCategory');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'slab_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'channel_id' => 'required',
            'channel_id.*' => 'exists:categories,id',
          'amount' => 'required|array|min:1',
          'to_amount' => 'required|array|min:1',
            'percentage' => 'required|array',
            'percentage.*' => 'required|numeric|min:0|max:100',
        ]);
     
        DB::beginTransaction();
        try {
            // Create the slab category
            $scheme_product = SlabCategory::create([
                'slab_name' => $validated['slab_name'],
                'description' => $validated['description'],
                'channel_id' => $validated['channel_id'],
            ]);
    
            $slab_categories_id = $scheme_product->id;
    
            // Iterate over the category_id array
            foreach ($validated['amount'] as $key => $amount) {
                SlabCategoryDetail::create([
                    'slab_categories_id' => $slab_categories_id,
                    'amount' => $amount ?? 0,
                    'to_amount' => $amount ?? 0,
                    'percentage' => $validated['percentage'][$key] ?? 0, // Handle missing index gracefully
                ]);
            }
    
            DB::commit();
            return response()->json(['success' => 'Slab Created Successfully']);
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::error('Slab creation failed: ' . $th->getMessage());
            return response()->json(['error' => 'Failed to create slab category: ' . $th->getMessage()], 500);
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
    public function edit($id)
{
    // Attempt to find the slab category by ID
    $slabCategory = SlabCategory::with('SlabCategoryDetail')->find($id);

    // Handle the case where the slab category is not found
    if (!$slabCategory) {
        return redirect()
            ->route('slab-category.index') // Adjust this to your route name
            ->with('error', 'Slab category not found.');
    }

    return view('pages.Products.SlabCategory.EditSlabCategory', compact('slabCategory'));
}


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'slab_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'channel_id' => 'required',
            'channel_id.*' => 'exists:categories,id',
            'amount' => 'required|array|min:1',
            'to_amount' => 'required|array|min:1',
            'percentage' => 'required|array',
            'percentage.*' => 'required|numeric|min:0|max:100',
        ]);

    
        
        DB::beginTransaction();
        try {
            // Find the slab category to update
            $slabCategory = SlabCategory::findOrFail($id);
    
            // Update the slab category details
            $slabCategory->update([
                'slab_name' => $validated['slab_name'],
                'description' => $validated['description'],
                'channel_id' => $validated['channel_id'],
            ]);
            
            // Delete old SlabCategoryDetail records before adding new ones
            $slabCategory->SlabCategoryDetail()->delete();
    
            // Add new SlabCategoryDetail records
            foreach ($validated['amount'] as $key => $amount) {
                SlabCategoryDetail::create([
                    'slab_categories_id' => $slabCategory->id,
                    'amount' => $amount ?? 0,
                    'to_amount' => $validated['to_amount'][$key] ?? 0,
                    'percentage' => $validated['percentage'][$key] ?? 0, // Handle missing index gracefully
                ]);
            }
    
            DB::commit();
            return response()->json(['success' => 'Slab Category updated successfully']);
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::error('Slab category update failed: ' . $th->getMessage());
            return response()->json(['error' => 'Failed to update slab category: ' . $th->getMessage()], 500);
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
        SlabCategory::where('id', $id)->update(['status' => 0]);
        SlabCategoryDetail::where('slab_categories_id' , $id)->update(['status' => 0]);
        return response()->json(['success' => 'Deleted Successfully!']);
    }

public function calculateSlabDiscount(Request $request)
    {
        try {
            $request->validate([
                'shop_id' => 'required|integer|exists:shops,id',
                'total_amount' => 'required|numeric|min:0'
            ]);

            $shopId = $request->input('shop_id');
            $totalAmount = (float) $request->input('total_amount');

            // Get shop with channel
            $shop = Shop::with('channel')->find($shopId);
            
            if (!$shop) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shop not found'
                ], 404);
            }

            // Get channel ID
            $channelId = $shop->channel_id;

            // Calculate slab discount
            $slabDiscount = $this->getSlabDiscount($totalAmount, $channelId);

            return response()->json([
                'success' => true,
                'shop_id' => $shopId,
                'channel_id' => $channelId,
                'total_amount' => $totalAmount,
                'slab_discount' => $slabDiscount,
                'message' => $slabDiscount ? 'Slab discount calculated successfully' : 'No slab discount applicable'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating slab discount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get slab discount based on total amount and channel
     *
     * @param float $totalAmount Order total amount (before bulk discount)
     * @param int|null $channelId Shop's channel ID
     * @return array|null Returns discount details or null if no match
     */
    private function getSlabDiscount($totalAmount, $channelId)
    {
        // Validation
        if ($channelId === null) {
            return null;
        }

        // Get all active slabs for this channel
        $matchingSlabs = SlabCategory::with(['SlabCategoryDetail' => function ($q) {
                $q->where('status', 1)
                  ->select('id', 'slab_categories_id', 'amount', 'to_amount', 'percentage');
            }])
            ->where('channel_id', $channelId)
            ->where('active', 1)
            ->where('status', 1)
            ->select('id', 'slab_name', 'description', 'channel_id', 'active', 'date')
            ->get();

        if ($matchingSlabs->isEmpty()) {
            return null;
        }

        // Iterate through matching slabs
        foreach ($matchingSlabs as $slab) {
            // Check each slab detail range
            foreach ($slab->SlabCategoryDetail as $slabDetail) {
                $amount = (float) $slabDetail->amount;
                $toAmount = (float) $slabDetail->to_amount;
                $percentage = (float) $slabDetail->percentage;

                // Range matching logic
                $isInRange = false;

                if ($toAmount == 0) {
                    // Last/upper range (unlimited)
                    // Apply if totalAmount >= amount
                    $isInRange = $totalAmount >= $amount;
                }
                else if ($amount == $toAmount) {
                    // Fixed amount range
                    // Apply if totalAmount >= amount
                    $isInRange = $totalAmount >= $amount;
                }
                else {
                    // Normal range
                    // Apply if totalAmount is between amount and toAmount
                    $isInRange = ($totalAmount >= $amount && $totalAmount <= $toAmount);
                }

                // If range matches, calculate and return discount
                if ($isInRange) {
                    $slabDiscountAmount = ($totalAmount * $percentage) / 100.0;

                    return [
                        'slab_id' => $slab->id,
                        'slab_name' => $slab->slab_name,
                        'slab_details_id' => $slabDetail->id,
                        'slab_amount' => $slabDiscountAmount,
                        'percentage' => $percentage,
                        'amount_range' => $amount . ' - ' . ($toAmount == 0 ? '∞' : $toAmount)
                    ];
                }
            }
        }

        // No matching range found
        return null;
    }

}
