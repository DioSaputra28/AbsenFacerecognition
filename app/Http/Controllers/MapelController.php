<?php

namespace App\Http\Controllers;

use App\Model\Kelas as ModelKelas;
use App\Model\Mapel as ModelMapel;
use App\Models\Kelas;
use App\Models\Mapel;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Tambahkan ini untuk menggunakan Session::flash
use Yajra\DataTables\DataTables;

class MapelController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = ModelMapel::orderBy('created_at', 'DESC')->get();

            return DataTables::of($data)
                ->addColumn('guru', function ($s) {
                    return '<a href="' . route('guru.show', $s->guru->username) . '">' . $s->guru->nama . '</a>';
                })
                ->addColumn('kelas', function ($s) {
                    return '<a href="' . route('kelas.show', $s->kelas->id) . '">' . $s->kelas->nama_kelas . '</a>';
                })
                ->addColumn('aksi', function ($s) {
                    return '<div class="btn-group dropup mb-1">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Aksi
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="' . route('mapel.edit', $s->id) . '">Edit</a>
                            <form id="data-' . $s->id . '" action="' . route('mapel.destroy', $s->id) . '" method="post">
                                ' . csrf_field() . '
                                ' . method_field('delete') . '
                            </form>
                            <button onclick="confirmDelete(' . $s->id . ')" class="dropdown-item">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>';
                })
                ->rawColumns(['aksi', 'kelas', 'guru'])
                ->addIndexColumn()
                ->toJson();
        }

        $pengajar = User::where('role', 'guru')->where('status', true)->get();
        $kelas = ModelKelas::orderBy('id', 'DESC')->get();

        return view('mapel.index', compact('pengajar', 'kelas'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|min:4',
                'pengajar' => 'required',
                'kelas' => 'required',
            ]);

            $data = new ModelMapel();
            $data->nama = $request->nama;
            $data->pengajar = $request->pengajar;
            $data->kelas_id = $request->kelas;
            $data->deskripsi = $request->deskripsi ?? '';
            $data->save();

            Session::flash('success', 'Mapel baru telah berhasil dibuat');
            return back();
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            return back();
        }
    }

    public function edit($id)
    {
        $data = ModelMapel::findOrFail($id);
        $pengajar = User::where('role', 'guru')->where('status', true)->get();
        $kelas = ModelKelas::orderBy('id', 'DESC')->get();

        return view('mapel.edit', compact('data', 'pengajar', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'pengajar' => 'required',
            'kelas' => 'required',
        ]);

        try {
            $data = ModelMapel::findOrFail($id);
            $data->nama = $request->nama;
            $data->pengajar = $request->pengajar;
            $data->kelas_id = $request->kelas;
            $data->deskripsi = $request->deskripsi ?? '';
            $data->update();

            Session::flash('success', 'Data Mapel Berhasil diperbaharui');
            return redirect()->route('mapel.index');
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $data = ModelMapel::findOrFail($id);
            $data->delete();

            Session::flash('success', 'Data Mapel Berhasil dihapus');
            return back();
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            return back();
        }
    }
}
