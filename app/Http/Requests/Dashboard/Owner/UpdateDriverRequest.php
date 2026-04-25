<?php

namespace App\Http\Requests\Dashboard\Owner;

use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends OwnerDashboardRequest
{
    public function rules(): array
    {
        /** @var User|null $driver */
        $driver = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($driver?->id),
            ],
            'device_id' => [
                'required',
                'string',
                'max:120',
                Rule::unique(User::class, 'device_id')->ignore($driver?->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
