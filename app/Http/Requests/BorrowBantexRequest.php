<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowBantexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_peminjam' => ['required', 'string', 'max:150'],
            'keperluan' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'nama_peminjam.max' => 'Nama peminjam maksimal 150 karakter.',
            'keperluan.required' => 'Keperluan peminjaman wajib diisi.',
            'keperluan.max' => 'Keperluan maksimal 500 karakter.',
        ];
    }
}
