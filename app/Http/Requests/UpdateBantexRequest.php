<?php

namespace App\Http\Requests;

use App\Models\Bantex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBantexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bantex = $this->route('bantex');
        $bantexId = $bantex instanceof Bantex ? $bantex->id : $bantex;

        return [
            'kode_bantex' => [
                'required',
                'string',
                'max:100',
                Rule::unique('bantex', 'kode_bantex')->ignore($bantexId),
            ],
            'tim' => ['required', 'string', 'max:50'],
            'nomor_rak' => ['required', 'string', 'max:50'],
            'baris' => ['required', 'string', 'max:50'],
            'urutan_bantex' => ['required', 'string', 'max:50'],
            'keterangan_lokasi' => ['nullable', 'string', 'max:500'],
            'dokumen' => ['nullable', 'array'],
            'dokumen.*.id' => ['nullable', 'integer'],
            'dokumen.*.no_pengadaan' => ['required_with:dokumen', 'string', 'max:100'],
            'dokumen.*.nama_pengadaan' => ['required_with:dokumen', 'string', 'max:255'],
            'dokumen.*.tahun' => ['required_with:dokumen', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_bantex.required' => 'Kode bantex wajib diisi.',
            'kode_bantex.unique' => 'Kode bantex sudah terdaftar di sistem.',
            'tim.required' => 'Kelompok / tim wajib diisi.',
            'nomor_rak.required' => 'Nomor rak wajib diisi.',
            'baris.required' => 'Baris rak wajib diisi.',
            'urutan_bantex.required' => 'Urutan bantex wajib diisi.',
            'dokumen.*.no_pengadaan.required_with' => 'Nomor pengadaan pada dokumen wajib diisi.',
            'dokumen.*.nama_pengadaan.required_with' => 'Nama pengadaan pada dokumen wajib diisi.',
            'dokumen.*.tahun.required_with' => 'Tahun pengadaan pada dokumen wajib diisi.',
        ];
    }
}
