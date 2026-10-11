<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreColorRequest;
use App\Models\Color;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;

class ColorController extends BaseApiController
{
    public function __construct(protected SettingService $settingService) {}

    /**
     * Display a listing of colors.
     */
    public function colorslisting(): JsonResponse
    {
        $colors = Color::latest()->get();

        return $this->sendResponse($colors, 'Colors retrieved successfully');
    }

    /**
     * Store a newly created color in storage.
     */
    public function store(StoreColorRequest $request): JsonResponse
    {
        $color = $this->settingService->storeColor($request->validated());

        return $this->sendResponse($color, 'Color created successfully', 201);
    }

    /**
     * Display the specified color.
     */
    public function colorsDetailsbyid(Color $color): JsonResponse
    {
        return $this->sendResponse($color, 'Color details retrieved');
    }

    /**
     * Remove the specified color from storage.
     */
    public function destroy(Color $color): JsonResponse
    {
        $this->settingService->deleteColor($color);

        return $this->sendResponse(null, 'Color deleted successfully');
    }
}
