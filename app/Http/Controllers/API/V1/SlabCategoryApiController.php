<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SlabCategory;
class SlabCategoryApiController extends Controller
{
    // public function getSlabs()
    // {
    //     try {

    //         $slabs = SlabCategory::with(['SlabCategoryDetail' => function ($q) {
    //             $q->where('status', 1)
    //               ->select('id', 'slab_categories_id', 'amount', 'to_amount', 'percentage');
    //         }])
    //             ->where('status', 1)
    //             ->select('id', 'slab_name', 'description', 'channel_id', 'active', 'date')
    //             ->get();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Slab list fetched successfully',
    //             'data' => $slabs
    //         ]);

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }


    public function getSlabs()
{
    try {

        $slabs = SlabCategory::with(['SlabCategoryDetail' => function ($q) {
            $q->where('status', 1)
              ->select('id', 'slab_categories_id', 'amount', 'to_amount', 'percentage');
        }])
            ->where('status', 1)
            ->select('id', 'slab_name', 'description', 'channel_id', 'active', 'date')
            ->get()
            ->map(function ($item) {

                // rename: slab_category_detail → channel
                $item->channel = $item->SlabCategoryDetail;

                // remove old key
                unset($item->SlabCategoryDetail);

                return $item;
            });

        return response()->json([
            'status' => true,
            'message' => 'Slab list fetched successfully',
            'data' => $slabs
        ]);

    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $th->getMessage()
        ], 500);
    }
}

}
