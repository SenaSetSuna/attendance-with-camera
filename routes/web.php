<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use App\Models\Pertemuan;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function (Request $request) {
    if ($request->session()->has('logged_in')) return redirect('/dashboard');
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate(['username' => 'required', 'password' => 'required']);
    $admin = Admin::where('username', $credentials['username'])->where('password', $credentials['password'])->first();

    if ($admin) {
        $request->session()->put('logged_in', true);
        $request->session()->put('username', $admin->username);
        return redirect('/sesi'); 
    }
    return back()->withErrors(['username' => 'Username atau password salah.']);
});

Route::post('/logout', function (Request $request) {
    $request->session()->flush();
    return redirect('/login');
});

// Menu Sesi Presensi
Route::get('/sesi', function (Request $request) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    
    $subjects = Pertemuan::select('subject')->distinct()->pluck('subject');
    $selected_subject = $request->input('subject');
    
    $query = Pertemuan::orderBy('created_at', 'desc');
    if ($selected_subject) {
        $query->where('subject', $selected_subject);
    }
    
    $pertemuans_grouped = $query->get()->groupBy('subject');
    
    return view('sesi', compact('subjects', 'selected_subject', 'pertemuans_grouped'));
});

Route::post('/sesi/{id}/toggle', function (Request $request, $id) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    $pertemuan = Pertemuan::findOrFail($id);
    $pertemuan->is_active = !$pertemuan->is_active; 
    $pertemuan->save();
    return back()->with('success', 'Status sesi berhasil diubah.');
});

// Buat Subject Baru dengan Validasi Duplikat
Route::post('/buat-subject', function (Request $request) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    $request->validate(['subject' => 'required|string']);
    
    // Cek apakah subject sudah ada (Case Insensitive)
    $exists = Pertemuan::whereRaw('LOWER(subject) = ?', [strtolower($request->subject)])->exists();
    
    if ($exists) {
        return back()->with('error', 'Gagal: Subject "' . $request->subject . '" sudah terdaftar!');
    }

    Pertemuan::create([
        'nama' => 'Pertemuan 1',
        'subject' => $request->subject,
        'is_active' => 1
    ]);

    return back()->with('success', 'Subject Baru Berhasil Ditambahkan!');
});

// Buat Sesi Baru
Route::post('/buat-pertemuan', function (Request $request) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    $request->validate(['nama' => 'required|string', 'subject' => 'required|string']);
    
    Pertemuan::create([
        'nama' => $request->nama,
        'subject' => $request->subject,
        'is_active' => 1 
    ]);

    return back()->with('success', 'Sesi Presensi Baru Berhasil Dibuat!');
});

// Hapus Sesi Individual
Route::post('/hapus-sesi/{id}', function (Request $request, $id) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    Presensi::where('pertemuan_id', $id)->delete(); // Hapus data presensi terkait dulu
    Pertemuan::destroy($id); // Baru hapus sesi
    return back()->with('success', 'Sesi berhasil dihapus.');
});

// Hapus Keseluruhan Subject
Route::post('/hapus-subject', function (Request $request) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    $subject = $request->input('subject');
    
    $pertemuan_ids = Pertemuan::where('subject', $subject)->pluck('id');
    Presensi::whereIn('pertemuan_id', $pertemuan_ids)->delete(); // Hapus semua presensi
    Pertemuan::where('subject', $subject)->delete(); // Hapus semua sesi di subject ini
    
    return back()->with('success', 'Subject ' . $subject . ' beserta seluruh sesinya berhasil dihapus.');
});

Route::get('/dashboard', function (Request $request) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    
    $selected_pertemuan_id = $request->input('pertemuan_id');
    $active_pertemuan = $selected_pertemuan_id ? Pertemuan::find($selected_pertemuan_id) : null;
    
    $hadir = collect();
    $belum_hadir = collect();

    if ($active_pertemuan) {
        $semua_mahasiswa = Mahasiswa::all();
        $nim_hadir = Presensi::where('pertemuan_id', $active_pertemuan->id)->pluck('nim')->toArray();
        
        $hadir = $semua_mahasiswa->whereIn('nim', $nim_hadir);
        $belum_hadir = $semua_mahasiswa->whereNotIn('nim', $nim_hadir);
    }
    
    return view('dashboard', compact('active_pertemuan', 'hadir', 'belum_hadir'));
})->name('dashboard');

// Log Attendance
Route::post('/presensi', function (Request $request) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    $request->validate(['nim' => 'required|string', 'pertemuan_id' => 'required|integer']);
    
    $nim = $request->input('nim');
    $pertemuan_id = $request->input('pertemuan_id');

    $pertemuan = Pertemuan::findOrFail($pertemuan_id);
    if (!$pertemuan->is_active) {
        return back()->with('error', 'Gagal: Sesi ' . $pertemuan->nama . ' saat ini berstatus TIDAK AKTIF.');
    }

    $mahasiswa = Mahasiswa::where('nim', $nim)->first();
    if (!$mahasiswa) return back()->with('error', 'Gagal: NIM ' . $nim . ' tidak terdaftar.');

    $alreadyScanned = Presensi::where('nim', $nim)->where('pertemuan_id', $pertemuan_id)->exists();
    if ($alreadyScanned) return back()->with('error', 'Perhatian: ' . $mahasiswa->nama_lengkap . ' sudah presensi untuk sesi ini.');

    Presensi::insert([
        'nim' => $nim,
        'pertemuan_id' => $pertemuan_id,
        'status' => 'hadir'
    ]);

    return back()->with('success', 'Berhasil: Presensi ' . $mahasiswa->nama_lengkap . ' dicatat.');
});

// Route untuk Membatalkan Presensi (Remove from Hadir)
Route::post('/batal-presensi', function (Request $request) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    $request->validate(['nim' => 'required|string', 'pertemuan_id' => 'required|integer']);
    
    $nim = $request->input('nim');
    $pertemuan_id = $request->input('pertemuan_id');

    $presensi = Presensi::where('nim', $nim)->where('pertemuan_id', $pertemuan_id)->first();

    if ($presensi) {
        $presensi->delete();
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();
        return back()->with('success', 'Berhasil: Presensi ' . ($mahasiswa ? $mahasiswa->nama_lengkap : $nim) . ' telah dibatalkan.');
    }

    return back()->with('error', 'Data presensi tidak ditemukan.');
});

Route::get('/history', function (Request $request) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    $history = Presensi::join('mahasiswas', 'presensis.nim', '=', 'mahasiswas.nim')
        ->join('pertemuans', 'presensis.pertemuan_id', '=', 'pertemuans.id')
        ->select('presensis.*', 'mahasiswas.nama_lengkap', 'pertemuans.nama as nama_sesi', 'pertemuans.subject')
        ->orderBy('presensis.created_at', 'desc')
        ->get();
    return view('history', compact('history'));
});

Route::get('/siswa', function (Request $request) {
    if (!$request->session()->has('logged_in')) return redirect('/login');
    $mahasiswas = Mahasiswa::all();
    return view('siswa', compact('mahasiswas'));
});

Route::get('/export', function (Request $request) {
    if (!$request->session()->has('logged_in')) return redirect('/login');

    $fileName = 'Data_Presensi.csv';
    $presensis = Presensi::join('mahasiswas', 'presensis.nim', '=', 'mahasiswas.nim')
        ->join('pertemuans', 'presensis.pertemuan_id', '=', 'pertemuans.id')
        ->select('presensis.created_at', 'pertemuans.subject', 'pertemuans.nama as nama_sesi', 'presensis.nim', 'mahasiswas.nama_lengkap', 'presensis.status')
        ->orderBy('presensis.created_at', 'desc')
        ->get();

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];
    $columns = ['Tanggal Waktu', 'Subject', 'Sesi', 'NIM', 'Nama Lengkap', 'Status'];
    $callback = function() use($presensis, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);
        foreach ($presensis as $p) {
            fputcsv($file, [$p->created_at, $p->subject, $p->nama_sesi, $p->nim, $p->nama_lengkap, $p->status]);
        }
        fclose($file);
    };
    return response()->stream($callback, 200, $headers);
});