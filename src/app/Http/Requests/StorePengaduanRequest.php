<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengaduanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Pengaduan ─────────────────────────────────────────
            'tanggal_pengaduan' => ['required', 'date'],
            'sumber_laporan'    => ['required', 'in:span_lapor,manual'],
            'jenis_aduan'       => ['required', 'array', 'min:1'],
            'jenis_aduan.*'     => ['string', 'in:pencemaran_air,pencemaran_udara,pencemaran_limbah_b3,pencemaran_limbah_non_b3,pencemaran_sampah,pencemaran_lainnya'],
            'uraian_pengaduan'  => ['required', 'string', 'min:20'],
            'lokasi_kejadian'   => ['nullable', 'string'],
            'kecamatan_id'      => ['nullable', 'exists:master_kecamatan,id'],
            'kelurahan_id'      => ['nullable', 'exists:master_kelurahan,id'],
            'koordinat_lat'     => ['nullable', 'numeric', 'between:-90,90'],
            'koordinat_lng'     => ['nullable', 'numeric', 'between:-180,180'],
            'dokumen_pendukung' => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],

            // ── Pelapor ───────────────────────────────────────────
            'nama_pelapor'      => ['required', 'string', 'max:255'],
            'jenis_pelapor'     => ['required', 'in:perorangan,lembaga,badan_hukum'],
            'nik'               => ['nullable', 'string', 'max:20'],
            'no_telp_pelapor'   => ['nullable', 'string', 'max:20'],
            'email_pelapor'     => ['nullable', 'email', 'max:255'],
            'alamat_pelapor'    => ['nullable', 'string'],
            'kecamatan_pelapor' => ['nullable', 'exists:master_kecamatan,id'],
            'kelurahan_pelapor' => ['nullable', 'exists:master_kelurahan,id'],
            'anonim'            => ['nullable', 'boolean'],
            'nama_lembaga'      => ['nullable', 'string', 'max:255', 'required_if:jenis_pelapor,lembaga,badan_hukum'],
            'jabatan_di_lembaga' => ['nullable', 'string', 'max:255'],
            'npwp_pelapor'      => ['nullable', 'string', 'max:25'],
            'nib_pelapor'       => ['nullable', 'string', 'max:20'],

            // ── Terlapor ──────────────────────────────────────────
            'nama_terlapor'     => ['required', 'string', 'max:255'],
            'jenis_terlapor'    => ['required', 'in:perorangan,lembaga,badan_hukum,objek_lainnya'],
            'jenis_usaha'       => ['nullable', 'string', 'max:255'],
            'no_telp_terlapor'  => ['nullable', 'string', 'max:20'],
            'alamat_terlapor'   => ['nullable', 'string'],
            'nib_terlapor'      => ['nullable', 'string', 'max:20'],
            'npwp_terlapor'     => ['nullable', 'string', 'max:25'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pelapor.required'      => 'Nama pelapor wajib diisi.',
            'nama_terlapor.required'     => 'Nama / perusahaan terlapor wajib diisi.',
            'nama_lembaga.required_if'   => 'Nama Lembaga/Organisasi atau Nama Perusahaan wajib diisi untuk jenis Pelapor ini.',
            'jenis_terlapor.required'    => 'Jenis terlapor wajib dipilih.',
            'tanggal_pengaduan.required' => 'Tanggal pengaduan wajib diisi.',
            'sumber_laporan.required'    => 'Sumber laporan wajib dipilih.',
            'jenis_aduan.required'       => 'Jenis aduan wajib dipilih minimal 1.',
            'jenis_aduan.min'            => 'Pilih minimal 1 jenis aduan.',
            'uraian_pengaduan.required'  => 'Uraian pengaduan wajib diisi.',
            'uraian_pengaduan.min'       => 'Uraian pengaduan minimal 20 karakter.',
            'dokumen_pendukung.max'      => 'Ukuran dokumen maksimal 5MB.',
            'dokumen_pendukung.mimes'    => 'Format dokumen: PDF, JPG, atau PNG.',
        ];
    }
}
