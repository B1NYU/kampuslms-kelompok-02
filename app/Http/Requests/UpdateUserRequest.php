<?php

namespace App\Http\Requests;

use App\Support\KataSandi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Parameter route harus bernama {user} (sudah demikian di web.php). */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Aturan yang bergantung pada kondisi data (peran/status) diperiksa di controller,
        // di dalam transaksi yang mengunci baris admin, supaya hasilnya tidak basi.
        Gate::authorize('update', $this->route('user'));

        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'nim_nip' => ['required', 'string', 'max:50', Rule::unique('users', 'nim_nip')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'dosen', 'mahasiswa'])],
            'password' => ['nullable', 'string', KataSandi::aturan()],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ];
    }

    public function messages(): array
    {
        return array_merge(KataSandi::pesan(), self::pesanUmum());
    }

    /** Dipakai bersama oleh StoreUserRequest. */
    public static function pesanUmum(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'nim_nip.required' => 'NIM/NIP wajib diisi.',
            'nim_nip.unique' => 'NIM/NIP ini sudah terdaftar.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'role.required' => 'Peran wajib dipilih.',
            'role.in' => 'Peran tidak dikenali.',
            'status.in' => 'Status tidak dikenali.',
        ];
    }
}
