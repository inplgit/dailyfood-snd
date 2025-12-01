<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SlabCategory;
use App\Models\SlabCategoryDetail;
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
}
