<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use App\Model\Absen;
use App\Model\Kelas;
use App\Model\Mapel;
use App\Model\Semester;
use App\Model\AbsenPending;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AbsenController extends Controller
{
    public function index(Request $request)
    {
        if ($request->kelas) {
            $data = AbsenPending::where('kelas_id', $request->kelas)
                ->where('konfirm', false)
                ->get();
        } else {
            if (!empty(auth()->user()->kelasku->walikelas)) {
                if (auth()->user()->kelasku->walikelas) {
                    $data = AbsenPending::where('kelas_id', auth()->user()->kelasku->id)
                        ->where('konfirm', false)
                        ->get();
                } else {
                    $data = AbsenPending::where('konfirm', false)
                        ->get();
                }
            } else {
                // notify()->warning('Silahkan mengisi data siswa dan kelas terlebih dahulu');
                return back();
            }
        }
        $dataKelas = Kelas::pluck('nama_kelas');
        $dataMapel = Mapel::pluck('nama');
        $dataSemester = Semester::pluck('id');
        $kelas = Kelas::all();
        return view('presensi.indexGuru', compact('data', 'kelas','dataKelas','dataMapel', 'dataSemester'));
    }
    public function catat(Request $request)
    {
        
        // Jika ada request kenali wajah, jalankan skrip Python
        if ($request->has('kenali_wajah')) {
            $pythonScriptPath = base_path('public/Python/faceRecognition.py');
            $pythonExecutable = 'python3';
            $command = escapeshellcmd("$pythonExecutable $pythonScriptPath");
            $output = shell_exec($command);
    
            // Filter output Python untuk mendapatkan hasil yang relevan
            $lines = explode("\n", trim($output));
            $filteredOutput = array_filter($lines, function ($line) {
                return preg_match('/^\w+$/', $line);
            });
            $result = implode("\n", $filteredOutput);
            
            // Kembalikan view dengan hasil deteki   
        }
        // Jika validasi berhasil dan tidak ada request kenali wajah
        if (isset($result) && !empty($result)) {
            // Kembalikan view dengan hasil deteksi
            return view('presensi.catatPresensi', [
                'output' => $result,
                'kelas' => Kelas::where('id', $request->kelas)->pluck('nama_kelas')->first(),
                'mapel' => $request->mapel,
                'semester' => $request->semester,
                'kelas_id' => Kelas::where('nama_kelas', $request->kelas)->pluck('id')->first(),
            ]);
        }
    
        // Jika tidak ada output dari skrip Python, kembalikan view tanpa output
        return view('presensi.catatPresensi', [
            'kelas' => $request->kelas,
            'mapel' => $request->mapel,
            'semester' => $request->semester,
            'kelas_id' => Kelas::where('nama_kelas', $request->kelas)->pluck('id')->first()
        ]);
    }

    
    public function catatManual(Request $request){
        $validate = $request->validate([
            'mapel' => 'required',
            'kelas' => 'required',
            'semester' => 'required'

        ]);
        $kelas_id = Kelas::where('nama_kelas', $request->kelas)->pluck('id')->first();

        // $inputs = $request->input('mapel');
        $data = array();
        $data[] = [
            'mapel' => $request->mapel,
            'kelas' => $request->kelas,
            'semester' => $request->semester,

        ];
        // dd($request->all());

        if ($validate){
            return view('presensi.catatPresensiManual', [
                'kelas' => $request->kelas,
                'mapel' => $request->mapel,
                'semester' => $request->semester,
                'data' => User::where('kelas_id', $kelas_id)->get()
            ]);
        }
        return redirect('absensi');
        // return view('presensi.catatPresensi', compact('data', 'kelas'));
    }
    public function storeCatatDeteksi(Request $request)
    {
        $validate = $request->validate([
            'user_id' => 'required',
            'kelas_id' => 'required',
            'semester_id' => 'required'
        ]);
        
        // Jika validasi berhasil
        if ($validate) {
            // Membuat instance baru dari model Absen
            $absen = new Absen();
            $absen->user_id = $request->user_id;
            $absen->kelas_id = $request->kelas_id;
            $absen->semester_id = $request->semester_id;
            $absen->keterangan = $request->keterangan;
            $absen->time_in = Carbon::now();
            // Menyimpan data ke dalam database
            $absen->save();
            // Redirect kembali ke halaman absensi
            return view('presensi.catatPresensi', [
                'kelas' =>  Kelas::where('id', $request->kelas_id)->pluck('nama_kelas')->first(),
                'mapel' => $request->mapel,
                'semester' => $request->semester_id,
                'namaUser' => DB::table('users')->where('id', $request->user_id)->value('nama')
            ])->with('success', 'Data absensi berhasil disimpan.');
        }
    
        // Jika validasi gagal, kembali ke halaman sebelumnya dengan pesan error
        return back()->withErrors($validate);
    }
    public function storeCatat(Request $request){

        $validate = $request->validate([
            'inputs.*.user_id' => 'required',
            'inputs.*.kelas_id' => 'required',
            'inputs.*.semester_id' => 'required',
            'inputs.*.keterangan' => 'required',
        ]);

        if($validate){

            foreach ($request->inputs as $key => $value){

            $obj = $request->inputs[$key];
            if( isset($obj['keterangan'])) {
                    // Absen::insert($value);
                    $client = new Absen($value);
                    $client->time_in = Carbon::now();
                    $client->save();
                }
            }
            return redirect('absensi');
        }
        return back();
    }
    public function fotoSiswa(Request $request)
    {
        // Validasi input
        $request->validate([
            'gambar' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'nama_baru' => 'required|integer|max:255'
        ]);

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $namaBaru = $request->input('nama_baru');
            $extension = $gambar->getClientOriginalExtension();
            $namaFile = $namaBaru . '.' . $extension;
            $gambar->move(public_path('/Python/fotoPeserta/'), $namaFile);
            return back()->with('message', 'Gambar berhasil diupload.');
        } else {
            return response()->json(['error' => 'No image uploaded.'], 400);
        }
    }
    
    public function laporanHarian(Request $request)
    {
        $siswa = User::where('status', true)
            ->where('role', 'siswa')
            ->get();
        if (request()->ajax()) {
            if (!empty($request->start)) {
                if ($request->start === $request->end) {
                    $data = Absen::where('user_id', $request->id)
                        ->whereDate('created_at', $request->start)
                        ->get();
                } else {
                    $data = Absen::where('user_id', $request->id)
                        ->whereBetween('created_at', array($request->start, $request->end))
                        ->get();
                }
            } elseif (empty($request->start)) {
                $data = Absen::where('id', '');
            }
            return DataTables::of($data)
                ->addColumn('tgl', function ($s) {
                    return date('d F Y', strtotime($s->created_at));
                })
                ->addColumn('time', function ($s) {
                    return date('H:m', strtotime($s->time_in));
                })
                ->addColumn('semester', function ($s) {
                    $semester = $s->semester->kode;
                    return "Semester $semester";
                })
                ->addColumn('kelas', function ($s) {
                    return $s->kelas->nama_kelas;
                })
                ->addColumn('hadir', function ($s) {
                    return $s->hadir();
                })
                ->addColumn('izin', function ($s) {
                    return $s->izin();
                })
                ->addColumn('sakit', function ($s) {
                    return $s->sakit();
                })
                ->addColumn('alpha', function ($s) {
                    return $s->alpha();
                })
                ->rawColumns(['tgl', 'time', 'semester', 'kelas', 'hadir', 'izin', 'sakit', 'alpha'])
                ->addIndexColumn()
                ->toJson();
        }
        return view('presensi.presensiSiswa', compact('siswa'));
    }
    private function dateRange($time)
    {
        switch ($time) {
            case "day":
                $time = [Carbon::now()->subHours(24)->toDateTimeString(), Carbon::now()->toDateTimeString()];
                break;
            case "week":
                $time = [Carbon::now()->subDays(7)->toDateTimeString(), Carbon::now()->toDateTimeString()];
                break;
            case "month":
                $time = [Carbon::now()->subDays(30)->toDateTimeString(), Carbon::now()->toDateTimeString()];
                break;
            case "year":
                $time = [Carbon::now()->subDays(365)->toDateTimeString(), Carbon::now()->toDateTimeString()];
                break;
            default:
                $time = null;
        }
        return $time;
    }
    public function laporanHarianKelas(Request $request)
    {
        $kelas = Kelas::all();
        $semester = Semester::all();
        if (request()->ajax()) {
            if (!empty($request->kelas_id)) {

                $range = $this->dateRange($request->range);
                $data = Absen::where('kelas_id', $request->kelas_id)
                    ->where('semester_id', $request->semester_id)
                    ->whereBetween('created_at', $range)
                    ->get();
            } elseif (empty($request->kelas_id)) {
                $data = Absen::where('id', '');
            }
            return DataTables::of($data)
                ->addColumn('tgl', function ($s) {
                    if ($s->user->status == true) {
                        return date('d F Y', strtotime($s->created_at));
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' .
                            date('d F Y', strtotime($s->created_at)) . '</span>';
                    }
                })
                ->addColumn('nis', function ($s) {
                    if ($s->user->status == true) {
                        return $s->user->nis;
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->user->nis . '</span>';
                    }
                })
                ->addColumn('nama', function ($s) {
                    if ($s->user->status == true) {
                        return $s->user->nama;
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->user->nama . '</span>';
                    }
                })
                ->addColumn('hadir', function ($s) {
                    if ($s->user->status == true) {
                        return $s->hadir();
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->hadir() . '</span>';
                    }
                })
                ->addColumn('izin', function ($s) {
                    if ($s->user->status == true) {
                        return $s->izin();
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->izin() . '</span>';
                    }
                })
                ->addColumn('sakit', function ($s) {
                    if ($s->user->status == true) {
                        return $s->sakit();
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->sakit() . '</span>';
                    }
                })
                ->addColumn('alpha', function ($s) {
                    if ($s->user->status == true) {
                        return $s->sakit();
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->alpha() . '</span>';
                    }
                })
                ->rawColumns(['tgl', 'id', 'nis', 'nama', 'hadir', 'izin', 'sakit', 'alpha'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('presensi.laporanHarianKelas', compact('kelas', 'semester'));
    }

    // proses pengembangan
    public function laporanHarianTidakHadir(Request $request)
    {
        $kelas = Kelas::all();
        $semester = Semester::all();
        if (request()->ajax()) {
            if (!empty($request->kelas_id)) {

                $data = Absen::where('kelas_id', $request->kelas_id)
                    ->where('semester_id', $request->semester_id)
                    ->distinct()
                    ->get();
            } elseif (empty($request->kelas_id)) {
                $data = Absen::where('id', '');
            }
            return DataTables::of($data)
                ->addColumn('id', function ($s) {
                    if ($s->user->status == true) {
                        return $s->user_id;
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->user_id . '</span>';
                    }
                })
                ->addColumn('nis', function ($s) {
                    if ($s->user->status == true) {
                        return $s->user->nis;
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->user->nis . '</span>';
                    }
                })
                ->addColumn('nama', function ($s) {
                    if ($s->user->status == true) {
                        return $s->user->nama;
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->user->nama . '</span>';
                    }
                })
                ->addColumn('kelas', function ($s) {
                    if ($s->user->status == true) {
                        return $s->kelas->nama_kelas;
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->kelas->nama_kelas . '</span>';
                    }
                })
                ->addColumn('tgl', function ($s) {
                    if ($s->user->status == true) {
                        return date('d F Y', strtotime($s->created_at));
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' .
                            date('d F Y', strtotime($s->created_at)) . '</span>';
                    }
                })
                ->addColumn('dataSiswa', function ($s) {
                    if ($s->user->status == true) {
                        return $s->kelas->nama_kelas;
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->kelas->nama_kelas . '</span>';
                    }
                })
                ->addColumn('hadir', function ($s) {
                    if ($s->user->status == true) {
                        return $s->hadir();
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->hadir() . '</span>';
                    }
                })
                ->addColumn('izin', function ($s) {
                    if ($s->user->status == true) {
                        return $s->izin();
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->izin() . '</span>';
                    }
                })
                ->addColumn('sakit', function ($s) {
                    if ($s->user->status == true) {
                        return $s->sakit();
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->sakit() . '</span>';
                    }
                })
                ->addColumn('alpha', function ($s) {
                    if ($s->user->status == true) {
                        return $s->alpha();
                    } else {
                        return '<span title="Tidak Aktif" style="color:red">' . $s->alpha() . '</span>';
                    }
                })
                ->rawColumns(['tgl', 'id', 'nis', 'nama', 'kelas', 'dataSiswa', 'hadir', 'izin', 'sakit', 'alpha'])
                ->toJson();
        }
        notify()->error('Halaman yang anda tuju sedang dalam proses pengembangan');
        return back();
        // return view('presensi.laporanHarianTidakhadir', compact('kelas', 'semester'));
    }
}
