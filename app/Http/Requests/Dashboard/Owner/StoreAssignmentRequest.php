<?php

namespace App\Http\Requests\Dashboard\Owner;

class StoreAssignmentRequest extends OwnerDashboardRequest
{
    public function rules(): array
    {
        return [
            'driver_id' => ['required', 'integer', 'exists:users,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
