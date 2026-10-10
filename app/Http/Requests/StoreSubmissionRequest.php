<?php

namespace App\Http\Requests;

use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/** Parameter route harus bernama {assignment} (sudah demikian di web.php). */
class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Seluruh syarat Q6 ada di SubmissionPolicy@create; pesan deny() ikut terbawa.
        Gate::authorize('create', [Submission::class, $this->route('assignment')]);

        return true;
    }

    public function rules(): array
    {
        return [
            // mimes = cek ISI berkas, extensions = cek EKSTENSI nama aslinya.
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,zip,txt', 'extensions:pdf,doc,docx,zip,txt'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required'   => 'Pilih berkas jawaban yang akan dikumpulkan.',
            'file.file'       => 'Berkas gagal diunggah. Ukurannya mungkin melebihi batas server.',
            'file.mimes'      => 'Berkas harus berformat PDF, DOC, DOCX, ZIP, atau TXT.',
            'file.extensions' => 'Berkas harus berformat PDF, DOC, DOCX, ZIP, atau TXT.',
            'file.max'        => 'Ukuran berkas maksimal 10 MB.',
            'note.max'        => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
