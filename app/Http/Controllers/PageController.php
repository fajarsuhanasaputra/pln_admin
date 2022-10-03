<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\KaryawanImport;
use App\Exports\KaryawanExport;
use App\Imports\TADImport;
use App\Exports\TADExport;
use App\Models\Karyawan;
use App\Models\TAD;
use Illuminate\Support\Facades\Session;
use Excel;

class PageController extends Controller
{
    public function dashboard()
    {
        return view('pages/dashboard');
    }
    // export excel karyawan
    public function exportKaryawan()
    {
        return Excel::download(new KaryawanExport, 'karyawan.xlsx');
    }
    // export excel tad
    public function exportTAD()
    {
        return Excel::download(new TADExport, 'tad.xlsx');
    }
    public function karyawan_view()
    {
        $datas = Karyawan::latest()->paginate(7);
        return view('pages/karyawan', [ 'karyawans' => $datas ]);
    }

    public function importFile(Request $request)
    {
        if ($request->file('file')) {
            $import = Excel::import(new KaryawanImport, $request->file('file'));
            if ($import) {
                Session::flash('success', 'Data berhasil ditambahkan');
                return redirect('/data/karyawan');
            } else {
                Session::flash('failed', 'Data berhasil ditambahkan');
                return redirect('/data/karyawan');
            } 
        } else {
            return redirect('/data/karyawan')->with('info', 'Pilih file');
        }
    }

    public function removeRecord(Request $request, $id)
    {
        $category = $request->query('category');
        if ($category == 'karyawan') {
            $res = Karyawan::find($id)->delete();
            if (!$res) {
                Session::flash('failed', 'Data gagal dihapus');
                return redirect('/data/karyawan');
            }
            Session::flash('success', 'Data berhasil dihapus');
            return redirect('/data/karyawan');
        }
        if ($category == 'tad') {
            $res = TAD::find($id)->delete();
            if (!$res) {
                Session::flash('failed', 'Data gagal dihapus');
                return redirect('/data/tad');
            }
            Session::flash('success', 'Data berhasil dihapus');
            return redirect('/data/tad');
        }

        return redirect()->back();
    }

    public function detailKaryawan(Request $request, $id) {
        $category = $request->query('category');
        if ($category == 'karyawan') {
            $res = Karyawan::find($id);
            return view('pages/detail_karyawan', compact('res'));
        }
        if ($category == 'tad') {
            $res = TAD::find($id);
            return view('pages/detail_karyawan', compact('res'));
        }

        return redirect()->back();
    }

    public function formKaryawan(Request $request, $id) {
        $category = $request->query('category');
        if ($category == 'karyawan') {
            $res = Karyawan::find($id);
            return view('pages/edit_karyawan', compact('res'));
        }

        if ($category == 'tad') {
            $res = TAD::find($id);
            return view('pages/edit_karyawan', compact('res'));
        }

        return redirect()->back();
    }

    public function editKaryawan(Request $request, $id) {
        $category = $request->query('category');
        try {
            if ($category == 'karyawan') {
                $res = Karyawan::find($id);
                $res->nid = $request->nid;
                $res->nama = $request->nama;
                $res->kelamin = $request->kelamin;
                $res->tempat_lahir = $request->tempat_lahir;
                $res->tanggal_lahir = $request->tanggal_lahir;
                $res->pendidikan = $request->pendidikan;
                $res->status_kepegawaian = $request->status_kepegawaian;
                $res->jabatan = $request->jabatan;
                $res->bagian = $request->bagian;
                $res->bidang = $request->bidang;
                $res->jurusan = $request->jurusan;
                $res->telp = $request->telp;
                $res->email = $request->email;
                $res->alamat = $request->alamat;
                $res->agama = $request->agama;
                $res->no_ktp = $request->no_ktp;
                $res->npwp = $request->npwp;
                $res->bpjs_kesehatan = $request->bpjs_kesehatan;
                $res->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan;
                $res->update();

                Session::flash('success', 'Data berhasil diperbaharui');
                return redirect('/data/karyawan');
            }

            if ($category == 'tad') {
                $res = TAD::find($id);
                $res->nama = $request->nama;
                $res->kelamin = $request->kelamin;
                $res->tempat_lahir = $request->tempat_lahir;
                $res->tanggal_lahir = $request->tanggal_lahir;
                $res->pendidikan = $request->pendidikan;
                $res->status_kontrak = $request->status_kontrak;
                $res->jabatan = $request->jabatan;
                $res->posisi = $request->posisi;
                $res->bidang = $request->bidang;
                $res->jurusan = $request->jurusan;
                $res->usia = $request->usia;
                $res->alamat = $request->alamat;
                $res->agama = $request->agama;
                $res->mkp = $request->mkp;
                $res->masa_kerja = $request->masa_kerja;
                $res->no_ktp = $request->no_ktp;
                $res->npwp = $request->npwp;
                $res->bpjs_kesehatan = $request->bpjs_kesehatan;
                $res->bpjs_ketenagakerjaan = $request->bpjs_ketenagakerjaan;
                $res->update();

                Session::flash('success', 'Data berhasil diperbaharui');
                return redirect('/data/tad');
            }

            return;
        } catch (\Throwable $th) {
            if ($category == 'karyawan') {
                Session::flash('failed', 'Data gagal diperbaharui');
                return redirect('/data/karyawan');
            }

            if ($category == 'tad') {
                Session::flash('failed', 'Data gagal diperbaharui');
                return redirect('/data/tad');
            }
        }
    }

    public function getChartData() {
        try {
            $karyawanFilteredByPjb_L = Karyawan::where('status_kepegawaian', '=', 'pjb')->where('kelamin', '=', 'laki-laki')->count();
            $karyawanFilteredByPjb_P = Karyawan::where('status_kepegawaian', '=', 'pjb')->where('kelamin', '=', 'perempuan')->count();
            $karyawanFilteredByPjbs_L = Karyawan::where('status_kepegawaian', '=', 'pjbs')->where('kelamin', '=', 'laki-laki')->count();
            $karyawanFilteredByPjbs_P = Karyawan::where('status_kepegawaian', '=', 'pjbs')->where('kelamin', '=', 'perempuan')->count();
            $karyawanFilteredByTad_L = TAD::where('kelamin', '=', 'laki-laki')->count();
            $karyawanFilteredByTad_P = TAD::where('kelamin', '=', 'perempuan')->count();

            $k_sma = Karyawan::where('pendidikan', '=', 'SMA')->count();
            $k_smk = Karyawan::where('pendidikan', '=', 'SMK')->count();
            $k_ma = Karyawan::where('pendidikan', '=', 'MA')->count();
            $k_d3 = Karyawan::where('pendidikan', '=', 'D3')->count();
            $k_s1 = Karyawan::where('pendidikan', '=', 'S1')->count();
            $k_s2 = Karyawan::where('pendidikan', '=', 'S2')->count();

            $t_sma = TAD::where('pendidikan', '=', 'SMA')->count();
            $t_smk = TAD::where('pendidikan', '=', 'SMK')->count();
            $t_ma = TAD::where('pendidikan', '=', 'MA')->count();
            $t_d3 = TAD::where('pendidikan', '=', 'D3')->count();
            $t_s1 = TAD::where('pendidikan', '=', 'S1')->count();
            $t_s2 = TAD::where('pendidikan', '=', 'S2')->count();

            $data = [
                'status' => '200',
                'data' => [
                    'jumlah_karyawan' => [
                        'pjb' => [
                            'laki_laki' => $karyawanFilteredByPjb_L,
                            'perempuan' => $karyawanFilteredByPjb_P,
                        ],
                        'pjbs' => [
                            'laki_laki' => $karyawanFilteredByPjbs_L,
                            'perempuan' => $karyawanFilteredByPjbs_P,
                        ],
                        'tad' => [
                            'laki_laki' => $karyawanFilteredByTad_L,
                            'perempuan' => $karyawanFilteredByTad_P,
                        ],
                    ],
                    'pendidikan' => [
                        'SMU' => $k_sma + $k_smk + $k_ma + $t_sma + $t_smk + $t_ma,
                        'D3' => $k_d3 + $t_d3,
                        'S1' => $k_s1 + $t_s1,
                        'S2' => $k_s2 + $t_s2,
                    ],
                ],
            ];
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json([ 'message' => 'Gagal memuat data chart' ], 400);
        }
    }

    public function tad() {
        $tad = TAD::latest()->paginate(7);
        return view('pages/tad', compact('tad'));
    }

    public function importFileTAD(Request $request)
    {
        if ($request->file('file')) {
            $import = Excel::import(new TADImport, $request->file('file'));
            if ($import) {
                Session::flash('success', 'Data berhasil ditambahkan');
                return redirect('/data/tad');
            } else {
                Session::flash('failed', 'Data berhasil ditambahkan');
                return redirect('/data/tad');
            } 
        } else {
            return redirect('/data/karyawan')->with('info', 'Pilih file');
        }
    }
}