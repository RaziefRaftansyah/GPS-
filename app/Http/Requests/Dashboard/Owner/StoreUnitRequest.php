<?php

namespace App\Http\Requests\Dashboard\Owner;

class StoreUnitRequest extends OwnerDashboardRequest
{
    protected $errorBag = 'unitForm';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:80', 'unique:units,code'],
            'status' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
