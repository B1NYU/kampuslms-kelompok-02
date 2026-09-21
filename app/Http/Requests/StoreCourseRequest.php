<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->code)) {
            $this->merge([
                'code' => Str::upper(trim($this->code)),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9-]+$/',
                Rule::unique('courses', 'code'),
            ],
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sks' => ['required', 'integer', 'min:1', 'max:6'],
            'lecturer_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['required', Rule::in(['draft', 'active', 'archived'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'kode mata kuliah',
            'name' => 'nama mata kuliah',
            'description' => 'deskripsi',
            'sks' => 'SKS',
            'lecturer_id' => 'dosen pengampu',
            'status' => 'status',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Kode mata kuliah wajib diisi, misalnya IF101.',
            'code.max' => 'Kode mata kuliah maksimal 20 karakter.',
            'code.regex' => 'Kode mata kuliah hanya boleh berisi huruf besar, angka, dan tanda hubung (contoh: IF101 atau IF-101).',
            'code.unique' => 'Kode mata kuliah ini sudah dipakai. Silakan gunakan kode lain.',

            'name.required' => 'Nama mata kuliah wajib diisi.',
            'name.min' => 'Nama mata kuliah minimal 3 karakter.',
            'name.max' => 'Nama mata kuliah maksimal 150 karakter.',

            'description.max' => 'Deskripsi maksimal 2000 karakter.',

            'sks.required' => 'Jumlah SKS wajib diisi.',
            'sks.integer' => 'SKS harus berupa bilangan bulat, misalnya 2 atau 3.',
            'sks.min' => 'SKS minimal 1.',
            'sks.max' => 'SKS maksimal 6.',

            'lecturer_id.required' => 'Silakan pilih dosen pengampu.',
            'lecturer_id.exists' => 'Dosen yang dipilih tidak ditemukan. Silakan pilih ulang dari daftar.',

            'status.required' => 'Status mata kuliah wajib dipilih.',
            'status.in' => 'Status harus salah satu dari: draft, active, atau archived.',
        ];
    }
}
