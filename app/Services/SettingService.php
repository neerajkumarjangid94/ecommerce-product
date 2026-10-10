<?php

namespace App\Services;

use App\Models\Color;
use App\Models\Size;

class SettingService
{
    /**
     * Store a new color.
     */
    public function storeColor(array $data): Color
    {
        return Color::create([
            'name'     => $data['color_name'],
            'hex_code' => $data['color_hex_code'] ?? null,
        ]);
    }

    /**
     * Delete a color.
     */
    public function deleteColor(Color $color): void
    {
        $color->delete();
    }

    /**
     * Store a new size.
     */
    public function storeSize(array $data): Size
    {
        return Size::create([
            'name' => $data['size_name'],
        ]);
    }

    /**
     * Delete a size.
     */
    public function deleteSize(Size $size): void
    {
        $size->delete();
    }
}
