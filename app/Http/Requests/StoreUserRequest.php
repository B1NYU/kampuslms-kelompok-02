<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\KataSandi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        Gate::authorize('create', User::class);

        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nim_nip' => ['required', 'string', 'max:50', 'unique:users,nim_nip'],
            // users.email NOT NULL + unique di skema, jadi wajib diisi di sini juga.
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'dosen', 'mahasiswa'])],
            'password' => ['required', 'string', KataSandi::aturan()],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ];
    }

    public function messages(): array
    {
        return array_merge(KataSandi::pesan(), UpdateUserRequest::pesanUmum());
    }
}
