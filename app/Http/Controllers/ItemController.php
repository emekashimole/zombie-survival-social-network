<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Services\ItemService;
use App\Utils\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    protected ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $items = $this->itemService->getAllItems()->get();
            $items = ItemResource::collection($items);
            return ApiResponse::ofData($items);
        } catch (Exception $e) {
            Log::error("Error fetching list of Items: ", [$e]);
            return ApiResponse::ofInternalServerError("Error fetching list of Items");
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'points' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return ApiResponse::ofClientError(errors: $errors);
        }

        try {
            $itemInfo = $request->only(['name', 'points']);

            $item = $this->itemService->addItem($itemInfo);
            $item = new ItemResource($item);
            return ApiResponse::ofData($item);
        } catch (Exception $e) {
            Log::error("Error adding new Item", [$e]);
            return ApiResponse::ofInternalServerError("Error adding new Item");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        try {
            $item = new ItemResource($item);
            return ApiResponse::ofData($item);
        } catch (Exception $e) {
            Log::error("Error fetching Item data", [$e]);
            return ApiResponse::ofInternalServerError("Error fetching Item data");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'points' => 'numeric'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return ApiResponse::ofClientError(errors: $errors);
        }

        try {
            $itemInfo = $request->only(['points']);
            $item = $this->itemService->updateItem($item, $itemInfo);

            $item = new ItemResource($item);
            return ApiResponse::ofData($item);
        } catch (Exception $e) {
            Log::error("Error updating Item data", [$e]);
            return ApiResponse::ofInternalServerError("Error updating Item data");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        try {
            $this->itemService->deleteItem($item);

            return ApiResponse::ofMessage("Item has been deleted successfully");
        } catch (Exception $e) {
            Log::error("Error deleting Item", [$e]);
            return ApiResponse::ofInternalServerError("Error deleting Item");
        }
    }
}
