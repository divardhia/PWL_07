<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Kelas; 
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mahasiswa = Mahasiswa::with('kelas')->get();
        $paginate = Mahasiswa::orderBy('Nim', 'asc')->paginate(3);
        return view('mahasiswa.index', ['mahasiswa' => $mahasiswa, 'paginate' => $paginate]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kelas = Kelas::all(); //mendapatkan data dari tabel kelas
        return view('mahasiswa.create',['kelas' => $kelas]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         //melakukan valNimasi data
         $request->validate([
            'Nim' => 'required',
            'Nama' => 'required',
            'Kelas' => 'required',
            'Jurusan' => 'required',
            // 'tanggalLahir' => 'required',
            // 'No_Handphone' => 'required',
            // 'email' => 'required',
        ]);

        $mahasiswa = new Mahasiswa;
        $mahasiswa->nim = $request->get('Nim');
        $mahasiswa->nama = $request->get('Nama');
        $mahasiswa->jurusan = $request->get('Jurusan');
        $mahasiswa->save();

        $kelas = new Kelas;
        $kelas->id = $request->get('Kelas');

        //fungsi eloquent untuk menambah data dengan relasi belongTo
        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();
        //jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $Nim
     * @return \Illuminate\Http\Response
     */
    public function show($Nim)
    {
         //menampilkan detail data dengan menemukan/berdasarkan Nim Mahasiswa
         $Mahasiswa = Mahasiswa::with('kelas')->where('nim', $Nim)->first();

         return view('mahasiswa.detail', compact('Mahasiswa')); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $Nim
     * @return \Illuminate\Http\Response
     */
    public function edit($Nim)
    {
        $Mahasiswa = Mahasiswa::with('kelas')->where('nim', $Nim)->first();
        $kelas = Kelas::all(); //mendapatkan data dari tabel kelas
        return view('mahasiswa.edit', compact('Mahasiswa', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $Nim
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $Nim)
    {
         // melakukan valNimasi data
         $request->validate([
            'Nim' => 'required',
            'Nama' => 'required',
            'Kelas' => 'required',
            'Jurusan' => 'required',
            // 'tanggalLahir' => 'required',
            // 'No_Handphone' => 'required',
            // 'email' => 'required',
        ]);

        $mahasiswa = Mahasiswa::with('kelas')->where('nim', $Nim)->first();
        $mahasiswa->nim = $request->get('Nim');
        $mahasiswa->nama = $request->get('Nama');
        $mahasiswa->jurusan = $request->get('Jurusan');
        $mahasiswa->save();

        $kelas = new Kelas;
        $kelas->id = $request->get('Kelas');

         //fungsi eloquent untuk menambah data dengan relasi belongTo
         $mahasiswa->kelas()->associate($kelas);
         $mahasiswa->save();
         //jika data berhasil ditambahkan, akan kembali ke halaman utama
         return redirect()->route('mahasiswa.index')
             ->with('success', 'Mahasiswa Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $Nim
     * @return \Illuminate\Http\Response
     */
    public function destroy($Nim)
    {
        Mahasiswa::find($Nim)->delete();
        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa Berhasil Dihapus');
    }

    public function search(Request $request)
    {
        $mahasiswa = Mahasiswa::when($request->keyword, function ($query) use ($request) {
            $query->where('Nama', 'like', "%{$request->keyword}%")
                ->orWhere('Nim', 'like', "%{$request->keyword}%")
                ->orWhere('Kelas', 'like', "%{$request->keyword}%")
                ->orWhere('Jurusan', 'like', "%{$request->keyword}%");
        })->paginate(5);
        $mahasiswa->appends($request->only('keyword'));
        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function nilai($Nim)
    {
        $mahasiswa = Mahasiswa::with('kelas', 'matakuliah')->where('Nim',$Nim)->first();
        return view('mahasiswa.nilai', compact('mahasiswa'));
    }
}
