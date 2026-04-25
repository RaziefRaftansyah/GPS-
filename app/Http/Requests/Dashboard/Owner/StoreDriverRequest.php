<?php

namespace App\Http\Requests\Dashboard\Owner;

class StoreDriverRequest extends OwnerDashboardRequest
{
    protected $errorBag = 'driverForm';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'device_id' => ['required', 'string', 'max:120', 'unique:users,device_id'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
