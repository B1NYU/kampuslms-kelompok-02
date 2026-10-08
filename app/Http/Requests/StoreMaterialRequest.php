<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id'    => ['required', 'integer', 'exists:courses,id'],
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'type'         => ['required', 'in:file,link'],
            'file'         => [
                'required_if:type,file',
                'nullable',
                'file',
                'mimes:pdf,ppt,pptx',
                'mimetypes:application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/zip',
                'max:51200' // 50 MB
            ],
            'external_url' => ['required_if:type,link', 'nullable', 'url:http,https', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'           => 'Judul materi wajib diisi.',
            'file.required_if'         => 'Pilih berkas PDF/PPTX yang akan diunggah.',
            'file.mimes'               => 'Berkas harus berformat PDF, PPT, atau PPTX.',
            'file.mimetypes'           => 'Format berkas tidak dikenali. Harap unggah berkas PDF, PPT, atau PPTX.',
            'file.max'                 => 'Ukuran berkas maksimal 50 MB.',
            'file.uploaded'            => 'Berkas gagal diunggah. Ukurannya mungkin melebihi batas server (upload_max_filesize).',
            'external_url.required_if' => 'URL tautan wajib diisi.',
            'external_url.url'         => 'URL harus diawali http:// atau https://.',
        ];
    }
}
