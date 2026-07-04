<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Presensi - PX PRSN</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex h-screen bg-slate-50 font-sans text-slate-800 overflow-hidden">

    @include('sidebar')

    <div class="flex-1 flex flex-col p-8 overflow-y-auto">
        
        @if(session('success'))
            <div class="mb-4 bg-green-50 text-green-700 px-4 py-3 rounded-xl shadow-sm border border-green-200 font-medium">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 text-red-600 px-4 py-3 rounded-xl shadow-sm border border-red-200 font-medium">{{ session('error') }}</div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Daftar Sesi Presensi</h2>
            
            <div class="relative">
                <form id="form-subject" action="/buat-subject" method="POST" class="hidden absolute right-0 top-12 bg-white p-4 shadow-xl border border-slate-100 rounded-xl w-72 z-20">
                    @csrf
                    <input type="text" name="subject" placeholder="Masukkan nama subject" class="bg-slate-100 text-slate-700 border border-slate-200 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 w-full mb-2" required autocomplete="off">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Buat Subject</button>
                </form>

                <button onclick="document.getElementById('form-subject').classList.toggle('hidden')" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-blue-700 shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Subject Baru
                </button>
            </div>
        </div>

        <!-- Pill Filter -->
        <div class="flex gap-3 mb-8 overflow-x-auto pb-2 border-b border-slate-200">
            <a href="/sesi" class="px-6 py-2 rounded-full text-sm font-semibold transition-all {{ !$selected_subject ? 'bg-blue-600 text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">All Subjects</a>
            @foreach($subjects as $sub)
            <a href="/sesi?subject={{ urlencode($sub) }}" class="px-6 py-2 rounded-full text-sm font-semibold transition-all {{ $selected_subject == $sub ? 'bg-blue-600 text-white shadow-md' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">{{ $sub }}</a>
            @endforeach
        </div>

        <div class="flex-1 flex flex-col gap-6">
            @forelse($pertemuans_grouped as $subject_name => $sessions)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                
                <!-- Subject Header -->
                <div class="flex justify-between items-center p-5 bg-white border-b border-slate-100">
                    <button onclick="toggleSubject('{{ Str::slug($subject_name) }}')" class="flex items-center gap-2 text-left focus:outline-none w-full">
                        <svg id="icon-{{ Str::slug($subject_name) }}" class="w-5 h-5 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        <h3 class="font-bold text-lg text-slate-800">{{ $subject_name }}</h3>
                        <span class="text-xs font-bold bg-slate-100 text-slate-500 px-3 py-1 rounded-full">{{ $sessions->count() }} Sesi</span>
                    </button>
                    
                    <form action="/hapus-subject" method="POST" onsubmit="return confirm('Hapus subject {{ $subject_name }} beserta SEMUA sesi di dalamnya?');">
                        @csrf <input type="hidden" name="subject" value="{{ $subject_name }}">
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 transition-colors" title="Hapus Subject">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
                
                <!-- Sesi List -->
                <div id="subject-{{ Str::slug($subject_name) }}" class="block divide-y divide-slate-50">
                    @foreach($sessions as $p)
                    <div class="p-5 flex justify-between items-center hover:bg-slate-50 transition-colors">
                        <div>
                            <h4 class="font-semibold text-slate-800">{{ $p->nama }}</h4>
                            <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y, H:i') }}</p>
                        </div>
                        
                        <div class="flex gap-6 items-center">
                            <!-- Status Toggle -->
                            <form action="/sesi/{{ $p->id }}/toggle" method="POST" class="flex items-center gap-2">
                                @csrf
                                <span class="text-xs font-medium text-slate-500">status</span>
                                <button type="submit" class="px-3 py-1 text-xs font-bold rounded-md border {{ $p->is_active ? 'bg-green-50 text-green-600 border-green-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                    {{ $p->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </button>
                            </form>

                            <a href="/dashboard?pertemuan_id={{ $p->id }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                Buka Presensi
                            </a>

                            <!-- Delete Session -->
                            <form action="/hapus-sesi/{{ $p->id }}" method="POST" onsubmit="return confirm('Hapus sesi ini?');">
                                @csrf
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach

                    <div class="p-3 bg-slate-50/50 pl-5">
                        <button onclick="document.getElementById('add-sesi-{{ Str::slug($subject_name) }}').classList.toggle('hidden')" class="text-xs font-bold text-blue-600 hover:underline">
                            + Tambah Sesi
                        </button>
                        <form id="add-sesi-{{ Str::slug($subject_name) }}" action="/buat-pertemuan" method="POST" class="hidden flex gap-2 mt-2">
                            @csrf
                            <input type="hidden" name="subject" value="{{ $subject_name }}">
                            <input type="text" name="nama" class="border border-slate-200 rounded px-2 py-1 text-xs outline-none focus:ring-2 focus:ring-blue-500" required placeholder="Nama sesi" autocomplete="off">
                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center p-10 bg-white rounded-xl border border-dashed border-slate-300">
                <p class="text-slate-500 font-medium">Belum ada subject yang dibuat.</p>
            </div>
            @endforelse
        </div>
    </div>

    <script>
        function toggleSubject(id) {
            const menu = document.getElementById('subject-' + id);
            const icon = document.getElementById('icon-' + id);
            menu.classList.toggle('hidden');
            icon.style.transform = menu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    </script>
</body>
</html>