<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Dashboard - PX PRSN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="flex h-screen bg-slate-50 font-sans text-slate-800 overflow-hidden relative">

    @include('sidebar')

    <div class="flex-1 flex flex-col p-8 overflow-y-auto">
        
        @if(session('success'))
            <div class="mb-4 bg-green-50 text-green-700 px-4 py-3 rounded-xl shadow-sm border border-green-200 font-medium">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 text-red-600 px-4 py-3 rounded-xl shadow-sm border border-red-200 font-medium">{{ session('error') }}</div>
        @endif

        @if($active_pertemuan)
        
        <div class="mb-6 bg-white p-5 rounded-xl shadow-sm border border-slate-100 flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Mata Kuliah</p>
                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full {{ $active_pertemuan->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $active_pertemuan->is_active ? 'AKTIF' : 'TIDAK AKTIF' }}
                    </span>
                </div>
                <h2 class="text-2xl font-bold text-blue-600">{{ $active_pertemuan->subject }} <span class="text-slate-800">- {{ $active_pertemuan->nama }}</span></h2>
            </div>
            <a href="/sesi" class="text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors border border-slate-200 px-4 py-2 rounded-lg bg-slate-50 hover:bg-blue-50">
                Kembali ke Daftar Sesi
            </a>
        </div>

        @if($active_pertemuan->is_active)
            <form action="/presensi" method="POST" class="flex flex-wrap items-center gap-4 mb-8 bg-blue-50 p-4 rounded-xl shadow-sm border border-blue-100">
                @csrf
                <input type="hidden" name="pertemuan_id" value="{{ $active_pertemuan->id }}">
                <div class="relative flex-1 max-w-md">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input id="nimInput" name="nim" type="text" placeholder="Ketik NIM" required autocomplete="off" class="w-full bg-white border border-blue-200 rounded-lg pl-10 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-blue-700 shadow-sm transition-all">
                    Presensi
                </button>
                
                <button type="button" onclick="alert('Under Maintenance: Fitur Presensi Kamera sedang dalam tahap pengembangan untuk integrasi Teachable Machine. Silakan input manual.');" 
                        class="bg-slate-100 text-slate-600 px-5 py-2 rounded-lg font-medium hover:bg-slate-200 border border-slate-300 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <div class="flex flex-col items-start leading-tight">
                        <span class="text-sm">Presensi Kamera</span>
                        <span class="text-[10px] opacity-70 uppercase tracking-wide">Under Maintenance</span>
                    </div>
                </button>
            </form>
        @else
            <div class="mb-8 bg-slate-100 p-6 rounded-xl border border-slate-200 flex items-center justify-center gap-4 text-slate-500">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <div>
                    <h3 class="font-bold text-slate-700">Sesi Presensi Ini Sedang Tidak Aktif</h3>
                    <p class="text-sm">Siswa tidak dapat melakukan presensi. Aktifkan kembali melalui menu Sesi Presensi untuk membuka absen.</p>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex-1 flex flex-col overflow-hidden">
            <div class="flex border-b border-slate-200 bg-slate-50/50">
                <div id="btn-tab-hadir" onclick="switchTab('hadir')" class="px-6 py-4 text-blue-600 font-semibold border-b-2 border-blue-600 cursor-pointer transition-colors">
                    Siswa Presensi ({{ $hadir->count() }})
                </div>
                <div id="btn-tab-belum" onclick="switchTab('belum')" class="px-6 py-4 text-slate-500 font-medium hover:text-slate-700 hover:bg-slate-100 cursor-pointer flex items-center transition-colors">
                    Siswa Belum Presensi ({{ $belum_hadir->count() }})
                </div>
            </div>

            <div id="tab-hadir" class="overflow-x-auto block">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">NIM</th>
                            <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">Nama Lengkap</th>
                            <th class="py-3 px-6 border-b border-slate-200 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($hadir as $s)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-sm font-medium text-slate-900">{{ $s->nim }}</td>
                            <td class="py-4 px-6 text-sm text-slate-600">{{ $s->nama_lengkap }}</td>
                            <td class="py-4 px-6 text-right"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Hadir</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="py-8 text-center text-slate-500 text-sm">Belum ada data presensi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="tab-belum" class="overflow-x-auto hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">NIM</th>
                            <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">Nama Lengkap</th>
                            <th class="py-3 px-6 border-b border-slate-200 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($belum_hadir as $s)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-sm font-medium text-slate-900">{{ $s->nim }}</td>
                            <td class="py-4 px-6 text-sm text-slate-600">{{ $s->nama_lengkap }}</td>
                            <td class="py-4 px-6 text-right"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Belum Presensi</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @else
        <div class="flex-1 flex flex-col items-center justify-center text-slate-500 border-2 border-dashed border-slate-300 rounded-xl p-10 bg-white">
            <h3 class="text-xl font-bold text-slate-800 mb-2">Pilih Sesi Presensi</h3>
            <p class="text-slate-500 text-center max-w-md mb-8">Anda harus membuka sesi melalui menu daftar sesi terlebih dahulu.</p>
            <a href="/sesi" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-blue-700 shadow-md shadow-blue-600/20 transition-all">
                Ke Menu Sesi Presensi
            </a>
        </div>
        @endif
    </div>

    <script>
        function switchTab(tab) {
            document.getElementById('tab-hadir').style.display = tab === 'hadir' ? 'block' : 'none';
            document.getElementById('tab-belum').style.display = tab === 'belum' ? 'block' : 'none';
            
            const activeClass = "px-6 py-4 text-blue-600 font-semibold border-b-2 border-blue-600 cursor-pointer transition-colors";
            const inactiveClass = "px-6 py-4 text-slate-500 font-medium hover:text-slate-700 hover:bg-slate-100 cursor-pointer flex items-center transition-colors";
            
            document.getElementById('btn-tab-hadir').className = tab === 'hadir' ? activeClass : inactiveClass;
            document.getElementById('btn-tab-belum').className = tab === 'belum' ? activeClass : inactiveClass;
        }
    </script>
</body>
</html>