<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreSizeRequest;
use App\Models\Size;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;

class SizeController extends BaseApiController
{
    public function __construct(protected SettingService $settingService) {}

    /**
     * Display a listing of sizes.
     */
    public function sizesListing(): JsonResponse
    {
        $sizes = Size::latest()->get();

        return $this->sendResponse($sizes, 'Sizes retrieved successfully');
    }

    /**
     * Store a newly created size in storage.
     */
    public function createSizes(StoreSizeRequest $request): JsonResponse
    {
        $size = $this->settingService->storeSize($request->validated());

        return $this->sendResponse($size, 'Size created successfully', 201);
    }

    /**
     * Display the specified size.
     */
    public function sizeDetailsByid(Size $size): JsonResponse
    {
        return $this->sendResponse($size, 'Size details retrieved');
    }

    /**
     * Remove the specified size from storage.
     */
    public function destroy(Size $size): JsonResponse
    {
        $this->settingService->deleteSize($size);

        return $this->sendResponse(null, 'Size deleted successfully');
    }
}
