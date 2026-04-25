<?php

namespace App\Http\Requests\Dashboard\Owner;

use App\Models\Unit;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends OwnerDashboardRequest
{
    public function rules(): array
    {
        /** @var Unit|null $unit */
        $unit = $this->route('unit');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:80',
                Rule::unique(Unit::class, 'code')->ignore($unit?->id),
            ],
            'status' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
