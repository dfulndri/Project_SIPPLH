<?php

namespace App\Exports;

use App\Models\Pengaduan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class PengaduanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    private int $no = 0;

    public function __construct(protected Request $request) {}

    public function query()
    {
        $q = Pengaduan::with(['pelapor','terlapor','kecamatan','kelurahan','assignedTo']);

        if ($this->request->filled('status'))       $q->where('status',$this->request->status);
        if ($this->request->filled('kategori'))     $q->where('kategori',$this->request->kategori);
        if ($this->request->filled('dari'))         $q->whereDate('tanggal_pengaduan','>=',$this->request->dari);
        if ($this->request->filled('sampai'))       $q->whereDate('tanggal_pengaduan','<=',$this->request->sampai);
        if ($this->request->filled('kecamatan_id')) $q->where('kecamatan_id',$this->request->kecamatan_id);

        return $q->orderBy('tanggal_pengaduan');
    }

    public function headings(): array
    {
        return [
            'No','Nomor Pengaduan','Tanggal','Pelapor','Terlapor',
            'Kategori','Kecamatan','Kelurahan','Ditugaskan','Status','Catatan Admin'
        ];
    }

    public function map($row): array
    {
        return [
            ++$this->no,
            $row->nomor_pengaduan,
            $row->tanggal_pengaduan->format('d/m/Y'),
            $row->pelapor?->anonim ? 'Anonim' : ($row->pelapor?->nama_pelapor ?? '—'),
            $row->terlapor?->nama ?? '—',
            ucwords(str_replace('_',' ',$row->kategori)),
            $row->kecamatan?->nama_kecamatan ?? '—',
            $row->kelurahan?->nama_kelurahan ?? '—',
            $row->assignedTo?->name ?? '—',
            ucfirst($row->status),
            $row->catatan_admin ?? '',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '6A0000']],
        ]);

        $sheet->getStyle('A1:K' . ($sheet->getHighestRow()))
            ->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    }
}