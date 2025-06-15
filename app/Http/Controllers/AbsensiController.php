<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

//use statement untuk PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AbsensiController extends Controller
{
    public function index()
    {
        // Untuk menampilkan daftar jadwal yang bisa diisi absensinya oleh guru
        $guru = Auth::user()->guru;
        $dayOfWeek = Carbon::today()->isoFormat('dddd');
        $jadwalHariIni = Jadwal::where('guru_id', $guru->id)
            ->where('hari', $dayOfWeek)
            ->with(['kelas', 'mataPelajaran'])
            ->orderBy('jam_mulai')
            ->get();

        return view('absensi.index', compact('jadwalHariIni'));
    }

    public function create(Jadwal $jadwal_id) // Menggunakan route model binding
    {
        $jadwal = $jadwal_id;
        $siswaInKelas = Siswa::where('kelas_id', $jadwal->kelas_id)->get();
        $tanggalSekarang = Carbon::now()->format('Y-m-d');

        // Cek apakah absensi untuk jadwal ini sudah diisi hari ini
        $absensiSudahDiisi = Absensi::where('jadwal_id', $jadwal->id)
            ->where('tanggal_absensi', $tanggalSekarang)
            ->exists();

        return view('absensi.create', compact('jadwal', 'siswaInKelas', 'tanggalSekarang', 'absensiSudahDiisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'tanggal_absensi' => 'required|date',
            'absensi_data' => 'required|array',
            'absensi_data.*.siswa_id' => 'required|exists:siswa,id',
            'absensi_data.*.status' => 'required|in:Hadir,Sakit,Izin,Alfa',
            'absensi_data.*.keterangan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $jadwalId = $request->input('jadwal_id');
        $tanggalAbsensi = $request->input('tanggal_absensi');
        $waktuInput = Carbon::now();

        foreach ($request->input('absensi_data') as $data) {
            Absensi::updateOrCreate(
                [
                    'siswa_id' => $data['siswa_id'],
                    'jadwal_id' => $jadwalId,
                    'tanggal_absensi' => $tanggalAbsensi,
                ],
                [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'],
                    'waktu_input' => $waktuInput,
                    'diinput_oleh_user_id' => $user->id,
                ]
            );
        }

        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil disimpan!');
    }

    public function rekap(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $mapelId = $request->input('mapel_id');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $query = Absensi::with(['siswa.kelas', 'jadwal.mataPelajaran', 'jadwal.guru']);

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }
        if ($mapelId) {
            $query->whereHas('jadwal', function ($q) use ($mapelId) {
                $q->where('mapel_id', $mapelId);
            });
        }
        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('tanggal_absensi', [$tanggalMulai, $tanggalSelesai]);
        }

        $absensiData = $query->orderBy('tanggal_absensi')->get()->groupBy('siswa.nama_lengkap');

        // ... di AbsensiController@rekap, setelah $absensiData
        $rekapitulasiPerSiswa = [];

        foreach ($absensiData as $siswaName => $absensisPerSiswa) {
            $totalHadir = $absensisPerSiswa->where('status', 'Hadir')->count();
            $totalSakit = $absensisPerSiswa->where('status', 'Sakit')->count();
            $totalIzin = $absensisPerSiswa->where('status', 'Izin')->count();
            $totalAlfa = $absensisPerSiswa->where('status', 'Alfa')->count();
            $totalAbsensi = $absensisPerSiswa->count();

            $persentaseKehadiran = ($totalAbsensi > 0) ? round(($totalHadir / $totalAbsensi) * 100, 2) : 0;

            $rekapitulasiPerSiswa[$siswaName] = [
                'hadir' => $totalHadir,
                'sakit' => $totalSakit,
                'izin' => $totalIzin,
                'alfa' => $totalAlfa,
                'total' => $totalAbsensi,
                'persentase_kehadiran' => $persentaseKehadiran,
            ];
        }

        $kelas = Kelas::all();
        $mapel = MataPelajaran::all();

        return view('absensi.rekap', compact('absensiData', 'kelas', 'mapel', 'kelasId', 'mapelId', 'tanggalMulai', 'tanggalSelesai'));
    }

    public function exportExcel(Request $request)
    {
        // 1. Ambil data absensi berdasarkan filter yang sama seperti di rekap()
        $kelasId = $request->input('kelas_id');
        $mapelId = $request->input('mapel_id');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $query = Absensi::with(['siswa.kelas', 'jadwal.mataPelajaran', 'jadwal.guru']);

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }
        if ($mapelId) {
            $query->whereHas('jadwal', function ($q) use ($mapelId) {
                $q->where('mapel_id', $mapelId);
            });
        }
        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('tanggal_absensi', [$tanggalMulai, $tanggalSelesai]);
        }

        $absensiData = $query->orderBy('tanggal_absensi')->get();

        // Mengelompokkan data berdasarkan siswa untuk rekapitulasi yang lebih baik di Excel
        $absensiGroupedBySiswa = $absensiData->groupBy('siswa.nama_lengkap');

        // 2. Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Absensi Siswa');

        // Styles untuk header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // White font
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4F81BD'], // Blue background
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        // Styles untuk data
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ];

        // 3. Tambahkan Judul Laporan (Merge Cells)
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'Rekapitulasi Absensi Siswa SMA');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->mergeCells('A2:G2');
        $filterInfo = "Tanggal: " . ($tanggalMulai ?? 'Semua') . " s/d " . ($tanggalSelesai ?? 'Semua');
        if ($kelasId) {
            $filterInfo .= ", Kelas: " . (Kelas::find($kelasId)->nama_kelas ?? 'N/A');
        }
        if ($mapelId) {
            $filterInfo .= ", Mata Pelajaran: " . (MataPelajaran::find($mapelId)->nama_mapel ?? 'N/A');
        }
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);


        // 4. Isi Header Tabel
        $headers = ['No.', 'NIS', 'Nama Siswa', 'Kelas', 'Tanggal', 'Mata Pelajaran', 'Waktu', 'Status', 'Keterangan', 'Guru Penginput'];
        $columnIndex = 1; // A
        $rowIndex = 4; // Dimulai dari baris 4 setelah judul dan filter

        foreach ($headers as $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex) . $rowIndex, $header);
            $columnIndex++;
        }
        $sheet->getStyle('A' . $rowIndex . ':' . $sheet->getHighestColumn() . $rowIndex)->applyFromArray($headerStyle);
        $sheet->getRowDimension($rowIndex)->setRowHeight(25);


        // 5. Isi Data Absensi
        $row = $rowIndex + 1; // Mulai mengisi data dari baris setelah header
        $no = 1;

        if ($absensiData->isEmpty()) {
            $sheet->setCellValue('A' . $row, 'Tidak ada data absensi ditemukan.');
            $sheet->mergeCells('A' . $row . ':J' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            foreach ($absensiData as $absensi) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $absensi->siswa->nis ?? '-');
                $sheet->setCellValue('C' . $row, $absensi->siswa->nama_lengkap ?? '-');
                $sheet->setCellValue('D' . $row, $absensi->siswa->kelas->nama_kelas ?? '-');
                $sheet->setCellValue('E' . $row, Carbon::parse($absensi->tanggal_absensi)->isoFormat('D MMMM YYYY'));
                $sheet->setCellValue('F' . $row, $absensi->jadwal->mataPelajaran->nama_mapel ?? '-');
                $sheet->setCellValue('G' . $row, Carbon::parse($absensi->jadwal->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($absensi->jadwal->jam_selesai)->format('H:i'));
                $sheet->setCellValue('H' . $row, $absensi->status);
                $sheet->setCellValue('I' . $row, $absensi->keterangan ?? '-');
                $sheet->setCellValue('J' . $row, $absensi->inputBy->name ?? '-');

                $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray($dataStyle);
                $row++;
            }
        }


        // 6. Atur lebar kolom otomatis
        foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // 7. Siapkan untuk download file
        $fileName = 'rekap_absensi_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        // Menggunakan StreamedResponse untuk menghindari masalah memori pada file besar
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
