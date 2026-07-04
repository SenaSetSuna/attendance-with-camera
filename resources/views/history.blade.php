<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - PX PRSN</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex h-screen bg-slate-50 font-sans text-slate-800 overflow-hidden">

    @include('sidebar')

    <div class="flex-1 flex flex-col p-8 overflow-y-auto">
        <h2 class="text-2xl font-bold text-slate-800 mb-6">History Presensi Keseluruhan</h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 flex-1 flex flex-col overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">Waktu Scan</th>
                            <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">Subject</th>
                            <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">Sesi</th>
                            <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">NIM</th>
                            <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">Nama Lengkap</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($history as $h)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-sm text-slate-600">{{ \Carbon\Carbon::parse($h->created_at)->format('d M Y, H:i') }}</td>
                            <td class="py-4 px-6 text-sm font-medium text-slate-900">{{ $h->subject }}</td>
                            <td class="py-4 px-6 text-sm text-slate-600">{{ $h->nama_sesi }}</td>
                            <td class="py-4 px-6 text-sm font-medium text-slate-900">{{ $h->nim }}</td>
                            <td class="py-4 px-6 text-sm text-slate-600">{{ $h->nama_lengkap }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>