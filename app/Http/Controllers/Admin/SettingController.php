<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\StoreSizeRequest;
use App\Models\Color;
use App\Models\Size;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(protected SettingService $settingService) {}

    /**
     * Show settings page with all colors and sizes.
     */
    public function index(\Illuminate\Http\Request $request): View
    {
        // If a return_to parameter is provided, save it
        if ($request->filled('return_to')) {
            session(['settings_return_to' => $request->query('return_to')]);
        } else {
            // Otherwise check previous URL if it came from outside settings
            $previous = url()->previous();
            if ($previous && !str_contains($previous, '/admin/settings')) {
                session(['settings_return_to' => $previous]);
            }
        }

        $colors = Color::latest()->get();
        $sizes  = Size::latest()->get();
        $returnUrl = session('settings_return_to', route('admin.product'));

        return view('admin.settings', compact('colors', 'sizes', 'returnUrl'));
    }

    /**
     * Store a new color.
     */
    public function storeColor(StoreColorRequest $request): RedirectResponse
    {
        $this->settingService->storeColor($request->validated());

        return redirect()->route('admin.settings')->with('success', 'Color added successfully!');
    }

    /**
     * Delete a color.
     */
    public function destroyColor(Color $color): RedirectResponse
    {
        $this->settingService->deleteColor($color);

        return redirect()->route('admin.settings')->with('success', 'Color deleted successfully!');
    }

    /**
     * Store a new size.
     */
    public function storeSize(StoreSizeRequest $request): RedirectResponse
    {
        $this->settingService->storeSize($request->validated());

        return redirect()->route('admin.settings')->with('success', 'Size added successfully!');
    }

    /**
     * Delete a size.
     */
    public function destroySize(Size $size): RedirectResponse
    {
        $this->settingService->deleteSize($size);

        return redirect()->route('admin.settings')->with('success', 'Size deleted successfully!');
    }
}
