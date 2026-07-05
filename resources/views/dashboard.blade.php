<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Dashboard - PX PRSN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>

    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="flex h-screen bg-slate-50 font-sans text-slate-800 overflow-hidden relative">

    @include('sidebar')

    <div id="camera-modal" class="hidden absolute inset-0 bg-black/60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity">
        <div class="bg-white p-6 rounded-2xl shadow-2xl w-96 border border-slate-100 flex flex-col items-center">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Scanning Wajah...</h3>
            
            <div id="webcam-container" class="w-64 h-64 bg-slate-100 rounded-xl overflow-hidden shadow-inner border-2 border-slate-200 mb-4 flex items-center justify-center">
                <span class="text-slate-400 text-sm animate-pulse" id="loading-text">Memuat Kamera...</span>
            </div>
            
            <div id="label-container" class="text-sm font-semibold text-blue-600 mb-6 h-6">Menunggu deteksi...</div>
            
            <button type="button" onclick="stopCamera()" class="w-full bg-red-50 text-red-600 hover:bg-red-100 py-2.5 rounded-lg font-medium transition-colors border border-red-100">
                Tutup Kamera
            </button>
        </div>
    </div>

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
            <form id="presensiForm" action="/presensi" method="POST" class="flex flex-wrap items-center gap-4 mb-8 bg-blue-50 p-4 rounded-xl shadow-sm border border-blue-100">
                @csrf
                <input type="hidden" name="pertemuan_id" value="{{ $active_pertemuan->id }}">
                <div class="relative flex-1 max-w-md">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input id="nimInput" name="nim" type="text" placeholder="Ketik NIM" required autocomplete="off" class="w-full bg-white border border-blue-200 rounded-lg pl-10 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-blue-700 shadow-sm transition-all">
                    Presensi
                </button>
                
                <button type="button" onclick="startCamera()" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700 shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <div class="flex flex-col items-start leading-tight">
                        <span class="text-sm">Presensi Kamera</span>
                        <span class="text-[10px] opacity-80 uppercase tracking-wide">AI Scanner</span>
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
                            <th class="py-3 px-6 border-b border-slate-200 text-center">Status</th>
                            <th class="py-3 px-6 border-b border-slate-200 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($hadir as $s)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-sm font-medium text-slate-900">{{ $s->nim }}</td>
                            <td class="py-4 px-6 text-sm text-slate-600">{{ $s->nama_lengkap }}</td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Hadir</span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <form action="/batal-presensi" method="POST" onsubmit="return confirm('Batalkan presensi untuk {{ $s->nama_lengkap }}?');" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="nim" value="{{ $s->nim }}">
                                    <input type="hidden" name="pertemuan_id" value="{{ $active_pertemuan->id }}">
                                    <button type="submit" class="p-1.5 bg-white border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 rounded-md transition-all shadow-sm" title="Batalkan Presensi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-8 text-center text-slate-500 text-sm">Belum ada data presensi.</td></tr>
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
                            <th class="py-3 px-6 border-b border-slate-200 text-center">Status</th>
                            <th class="py-3 px-6 border-b border-slate-200 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($belum_hadir as $s)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-sm font-medium text-slate-900">{{ $s->nim }}</td>
                            <td class="py-4 px-6 text-sm text-slate-600">{{ $s->nama_lengkap }}</td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Belum Presensi</span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <span class="text-slate-300">-</span>
                            </td>
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

    <script type="text/javascript">
        // PASTE YOUR URL HERE!
        const URL = "https://teachablemachine.withgoogle.com/models/cpoqa4ywc/";

        let model, webcam, labelContainer, maxPredictions;
        let isWebcamRunning = false;
        let isSubmitting = false;

        async function startCamera() {
            document.getElementById('camera-modal').classList.remove('hidden');
            
            if (!model) {
                const modelURL = URL + "model.json";
                const metadataURL = URL + "metadata.json";
                model = await tmImage.load(modelURL, metadataURL);
                maxPredictions = model.getTotalClasses();
            }

            if (!isWebcamRunning) {
                const flip = true; 
                webcam = new tmImage.Webcam(256, 256, flip); 
                await webcam.setup(); 
                await webcam.play();
                isWebcamRunning = true;
                window.requestAnimationFrame(loop);

                const wcContainer = document.getElementById("webcam-container");
                wcContainer.innerHTML = ''; // Clear loading text
                wcContainer.appendChild(webcam.canvas);
                webcam.canvas.classList.add("rounded-xl");
                
                labelContainer = document.getElementById("label-container");
            }
        }

        function stopCamera() {
            if (webcam && isWebcamRunning) {
                webcam.stop();
                isWebcamRunning = false;
            }
            document.getElementById('camera-modal').classList.add('hidden');
        }

        async function loop() {
            if (isWebcamRunning) {
                webcam.update();
                await predict();
                window.requestAnimationFrame(loop);
            }
        }

        // NEW: Function to check how bright the room is
        function getBrightness(canvas) {
            const ctx = canvas.getContext('2d');
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            let colorSum = 0;

            // Sample pixels to calculate average brightness
            for (let i = 0; i < data.length; i += 16) {
                const r = data[i];
                const g = data[i+1];
                const b = data[i+2];
                colorSum += (r + g + b) / 3;
            }
            return colorSum / (data.length / 16);
        }

        async function predict() {
            if(isSubmitting) return;

            const prediction = await model.predict(webcam.canvas);
            
            // 1. Find the probability of the "Background" class specifically
            let backgroundProb = 0;
            let highestProb = 0;
            let bestClass = "";

            for (let i = 0; i < maxPredictions; i++) {
                if (prediction[i].className === "Background") {
                    backgroundProb = prediction[i].probability;
                }
                if (prediction[i].probability > highestProb) {
                    highestProb = prediction[i].probability;
                    bestClass = prediction[i].className;
                }
            }

            // 2. Logic: Only submit if:
            // - The best class is NOT Background
            // - Confidence is > 95% (Raise this from 90% to be stricter)
            // - The student probability is MUCH higher than the background probability
            const isStudent = (bestClass !== "Background" && bestClass !== "");
            const isConfident = (highestProb > 0.95); 
            const isClearlyNotBackground = (highestProb > (backgroundProb + 0.5));

            if (isStudent && isConfident && isClearlyNotBackground) {
                isSubmitting = true;
                stopCamera();
                document.getElementById('nimInput').value = bestClass;
                document.getElementById('presensiForm').submit();
            } else {
                labelContainer.innerHTML = (bestClass === "Background" ? "Menunggu..." : bestClass) + 
                                           " (" + (highestProb * 100).toFixed(0) + "%)";
            }
        }

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