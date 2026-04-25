<?php

namespace App\Http\Requests\Dashboard\Driver;

use Illuminate\Foundation\Http\FormRequest;

abstract class DriverDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDriver() ?? false;
    }
}
