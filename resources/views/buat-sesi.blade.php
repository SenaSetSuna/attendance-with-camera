<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Sesi Baru - PX PRSN</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex h-screen bg-slate-50 font-sans text-slate-800 overflow-hidden">

    @include('sidebar')

    <div class="flex-1 flex flex-col p-8 overflow-y-auto items-center justify-center">
        
        <div class="bg-white p-10 rounded-2xl shadow-sm border border-slate-200 w-full max-w-lg">
            <div class="flex items-center gap-4 mb-8">
                <a href="/sesi" class="p-2 bg-slate-100 text-slate-500 rounded-lg hover:bg-slate-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="text-2xl font-bold text-slate-800">Buat Sesi Presensi Baru</h2>
            </div>

            <form action="/buat-pertemuan" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block mb-2 text-sm font-semibold text-slate-700">Nama Subject (Mata Kuliah)</label>
                    <input type="text" name="subject" placeholder="Contoh: Sistem Cerdas" class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-slate-50 focus:bg-white" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold text-slate-700">Nama Pertemuan / Sesi</label>
                    <input type="text" name="nama" placeholder="Contoh: Pertemuan 1" class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 transition-all bg-slate-50 focus:bg-white" required>
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold shadow-md shadow-blue-600/20 transition-all">
                        Simpan & Buka Presensi
                    </button>
                </div>
            </form>
        </div>

    </div>
</body>
</html>