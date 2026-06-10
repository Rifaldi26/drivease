<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form edit halaman statis.
 *
 * Memastikan judul tidak kosong dan setiap section memiliki
 * struktur yang benar sebelum disimpan ke database.
 */
class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sudah dijaga middleware is_admin di route
    }

    public function rules(): array
    {
        return [
            'title'                    => ['required', 'string', 'max:255'],
            'sections'                 => ['required', 'array', 'min:1'],
            'sections.*.title'         => ['required', 'string', 'max:255'],
            'sections.*.intro'         => ['nullable', 'string'],
            'sections.*.items'         => ['nullable', 'array'],
            'sections.*.items.*.label' => ['nullable', 'string', 'max:255'],
            'sections.*.items.*.text'  => ['required_with:sections.*.items', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'               => 'Judul halaman tidak boleh kosong.',
            'sections.required'            => 'Halaman harus memiliki minimal satu section.',
            'sections.*.title.required'    => 'Setiap section harus memiliki judul.',
            'sections.*.items.*.text.required_with' => 'Isi poin tidak boleh kosong.',
        ];
    }
}
