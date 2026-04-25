<?php

namespace App\Http\Requests\Dashboard\Owner;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

abstract class OwnerDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->isOwner($this->user());
    }

    protected function isOwner(?User $user): bool
    {
        $adminEmail = (string) env('ADMIN_EMAIL', 'admin@kopikeliling.com');

        return $user !== null
            && ($user->isOwner() || $user->email === $adminEmail);
    }
}
