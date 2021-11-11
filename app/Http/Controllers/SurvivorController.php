<?php

namespace App\Http\Controllers;

use App\Enums\SurvivorGender;
use App\Enums\SurvivorStatus;
use App\Http\Resources\SurvivorResource;
use App\Models\Survivor;
use App\Services\SurvivorService;
use App\Utils\ApiResponse;
use App\Utils\Constants;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SurvivorController extends Controller
{
    protected SurvivorService $survivorService;

    public function __construct(SurvivorService $survivorService)
    {
        $this->survivorService = $survivorService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'query' => 'string'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return ApiResponse::ofClientError(errors: $errors);
        }

        try {
            $queryString = $request->query('query');
            if ($queryString)
                $survivors = $this->searchService->searchSurvivors($queryString)
                    ->paginate(Constants::PAGINATION_PER_PAGE);
            else
                $survivors = $this->survivorService->getAllSurvivors()
                    ->paginate(Constants::PAGINATION_PER_PAGE);

            return ApiResponse::ofPaginatedData($survivors);
        } catch (Exception $e) {
            Log::error("Error fetching list of Survivors: ", [$e]);
            return ApiResponse::ofInternalServerError("Error fetching list of Survivors");
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
            'age' => 'required|numeric',
            'gender' => [
                'required',
                Rule::in(SurvivorGender::getValues())
            ],
            'lastLocation.lat' => 'required|numeric',
            'lastLocation.long' => 'required|numeric',
            'items.*' => 'array:id,quantity',
            'items.*.id' => 'numeric|exists:items,id',
            'items.*.quantity' => 'numeric|min:1'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return ApiResponse::ofClientError(errors: $errors);
        }

        DB::beginTransaction();

        try {
            $survivorInfo = $request->only(['name', 'age', 'gender', 'lastLocation']);
            $items = $request->input('items');

            $survivor = $this->survivorService->addSurvivor($survivorInfo);
            $this->survivorService->registerSurvivorItems($survivor, $items);
            
            DB::commit();

            $survivor = new SurvivorResource($survivor);
            return ApiResponse::ofData($survivor);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error adding new Survivor", [$e]);
            return ApiResponse::ofInternalServerError("Error adding new Survivor");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Survivor $survivor)
    {
        try {
            $survivor = new SurvivorResource($survivor);
            return ApiResponse::ofData($survivor);
        } catch (Exception $e) {
            Log::error("Error fetching Survivor data", [$e]);
            return ApiResponse::ofInternalServerError("Error fetching Survivor data");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Survivor  $survivor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Survivor $survivor)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'age' => 'numeric',
            'gender' => [
                Rule::in(SurvivorGender::getValues())
            ],
            'lastLocation.lat' => 'numeric|required_with:lastLocation.long',
            'lastLocation.long' => 'numeric|required_with:lastLocation.lat',
            'status' => [
                Rule::in(SurvivorStatus::getValues())
            ]
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return ApiResponse::ofClientError(errors: $errors);
        }

        try {
            $survivorInfo = $request->all();
            $survivor = $this->survivorService->updateSurvivor($survivor, $survivorInfo);

            $survivor = new SurvivorResource($survivor);
            return $survivor;
        } catch (Exception $e) {
            Log::error("Error updating Survivor data", [$e]);
            return ApiResponse::ofInternalServerError("Error updating Survivor data");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Survivor  $survivor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survivor $survivor)
    {
        try {
            $this->survivorService->deleteSurvivor($survivor);

            return ApiResponse::ofMessage("Survivor has been deleted successfully");
        } catch (Exception $e) {
            Log::error("Error deleting Survivor", [$e]);
            return ApiResponse::ofInternalServerError("Error deleting Survivor");
        }
    }
}
