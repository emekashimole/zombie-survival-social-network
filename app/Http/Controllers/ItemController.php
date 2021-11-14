<?php

namespace App\Http\Controllers;

use App\Enums\SurvivorStatus;
use App\Exceptions\ActionNotAllowedException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Services\ItemService;
use App\Services\SurvivorService;
use App\Utils\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    protected ItemService $itemService;
    protected SurvivorService $survivorService;

    public function __construct(ItemService $itemService, SurvivorService $survivorService)
    {
        $this->itemService = $itemService;
        $this->survivorService = $survivorService;
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

    public function tradeItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'survivors' => 'required|array|size:2',
            'survivors.*' => 'required|array:id,items',
            'survivors.*.id' => 'exists:survivors,id',
            'survivors.*.items.*' => 'array:id,quantity',
            'survivors.*.items.*.id' => 'exists:items,id',
            'survivors.*.items.*.quantity' => 'numeric|min:1',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return ApiResponse::ofClientError(errors: $errors);
        }

        DB::beginTransaction();

        try {
            $survivorAData = $request->input('survivors.0');
            $survivorBData = $request->input('survivors.1');

            $survivorA = $this->survivorService->getSurvivorById($survivorAData['id']);
            if (!$survivorA)
                throw new ResourceNotFoundException("Survivor Not Found");
            if ($survivorA->status === SurvivorStatus::INFECTED)
                throw new ActionNotAllowedException("C'mon now, you know the rules, infected survivors can no longer trade items");
            
            $survivorB = $this->survivorService->getSurvivorById($survivorBData['id']);
            if (!$survivorB)
                throw new ResourceNotFoundException("Survivor Not Found");
            if ($survivorB->status === SurvivorStatus::INFECTED)
                throw new ActionNotAllowedException("C'mon now, you know the rules, infected survivors can no longer trade items");

            $survivorAItems = $survivorAData['items'];
            $this->itemService->itemOwnershipCheck($survivorAItems, $survivorA);

            $survivorBItems = $survivorBData['items'];
            $this->itemService->itemOwnershipCheck($survivorBItems, $survivorB);

            $samePointsValue = $this->itemService->samePointsValueCheck($survivorAItems, $survivorBItems);
            if (!$samePointsValue)
                throw new ActionNotAllowedException("Items being traded do not offer the same amount of points");

            $this->itemService->tradeItems($survivorA, $survivorAItems, $survivorB, $survivorBItems);

            DB::commit();

            return ApiResponse::ofMessage("Items trade has been successfully completed");
        } catch (ResourceNotFoundException $e) {
            DB::rollBack();
            return ApiResponse::ofNotFound($e->getMessage());
        } catch (ActionNotAllowedException $e) {
            DB::rollBack();
            return ApiResponse::ofClientError($e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error trading survivor items", [$e]);
            return ApiResponse::ofInternalServerError("Error trading survivor items");
        }
    }
}
