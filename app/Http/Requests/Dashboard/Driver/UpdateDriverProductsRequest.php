<?php

namespace App\Http\Requests\Dashboard\Driver;

class UpdateDriverProductsRequest extends DriverDashboardRequest
{
    public function rules(): array
    {
        return [
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer'],
        ];
    }
}
