@extends('layouts.app')

@php
    $title      = 'QR Code Scanner';
    $breadcrumb = [['label' => 'QR Code Scanner', 'url' => route('qr-scanner.index')]];
@endphp

@section('title', 'QR Code Scanner')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">QR Code Scanner</h1>
    <p class="text-sm text-gray-500 mt-1">Instantly locate substance datasheets, live volume profiles, and storage warnings.</p>
</div>

{{-- Two Column Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start" x-data="qrScannerEngine()" x-init="initScanner()">

    {{-- LEFT: Scanner Viewport & Controls (matching Image 2) --}}
    <div class="lg:col-span-7 flex flex-col items-center">

        {{-- Dark Viewport Card --}}
        <div class="qr-viewport w-full shadow-lg">

            {{-- Video Stream Element --}}
            <video id="qr-video"
                   class="absolute inset-0 w-full h-full object-cover rounded-2xl"
                   style="border-radius: 16px; object-fit: cover; width: 100%; height: 100%;"
                   playsinline autoplay muted
                   x-show="scanning"></video>

            <canvas id="qr-canvas" class="hidden"></canvas>

            {{-- Scanner Frame Overlay (matching Image 2) --}}
            <div class="relative z-10 pointer-events-none flex items-center justify-center">
                <div class="qr-target-box">
                    {{-- 4 Corner Brackets --}}
                    <div class="qr-bracket qr-bracket-tl"></div>
                    <div class="qr-bracket qr-bracket-tr"></div>
                    <div class="qr-bracket qr-bracket-bl"></div>
                    <div class="qr-bracket qr-bracket-br"></div>

                    {{-- 3 White Markers (QR Position Detection) --}}
                    <div class="qr-marker qr-marker-tl" :style="scanning ? 'opacity: 0.15;' : 'opacity: 1;'"></div>
                    <div class="qr-marker qr-marker-tr" :style="scanning ? 'opacity: 0.15;' : 'opacity: 1;'"></div>
                    <div class="qr-marker qr-marker-bl" :style="scanning ? 'opacity: 0.15;' : 'opacity: 1;'"></div>

                    {{-- Red Laser Line --}}
                    <div class="qr-laser-line" :class="{ 'laser-active': scanning }"></div>
                </div>
            </div>

            {{-- Live Indicator badge inside viewport --}}
            <div x-show="scanning" class="absolute top-4 left-4 z-20 flex items-center gap-2 px-3 py-1 bg-black/70 backdrop-blur-xs rounded-full text-xs font-semibold text-emerald-400 border border-emerald-500/30">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Camera Live</span>
            </div>
        </div>

        {{-- Controls Below Viewport --}}
        <div class="w-full flex flex-col items-center mt-6">

            {{-- Action Button --}}
            <button type="button"
                    @click="toggleCamera()"
                    class="inline-flex items-center justify-center gap-2.5 text-white font-semibold text-sm rounded-xl shadow-md transition-all cursor-pointer hover:brightness-105 active:scale-[0.98]"
                    :style="scanning ? 'background-color: #dc2626; padding: 12px 32px;' : 'background-color: #2563eb; padding: 12px 32px;'">
                <template x-if="!scanning">
                    <span class="flex items-center gap-2">
                        <i data-lucide="scan-qr-code" class="w-4 h-4 stroke-[2.2]"></i>
                        Scan QR Code
                    </span>
                </template>
                <template x-if="scanning">
                    <span class="flex items-center gap-2">
                        <i data-lucide="camera-off" class="w-4 h-4 stroke-[2.2]"></i>
                        Matikan Kamera
                    </span>
                </template>
            </button>

            {{-- Manual Entry Link --}}
            <p class="text-xs text-gray-500 text-center mt-3 font-medium">
                Can't read code?
                <button type="button"
                        onclick="document.getElementById('manual-modal').classList.remove('hidden')"
                        class="text-blue-600 hover:text-blue-700 underline font-semibold cursor-pointer ml-0.5">
                    Enter barcode number manually
                </button>
            </p>

            {{-- Camera Switcher (if multiple webcams / DroidCam detected) --}}
            <div x-show="videoDevices.length > 1" class="mt-4 flex items-center gap-2 text-xs text-gray-500 bg-white px-3.5 py-1.5 rounded-lg border border-gray-200 shadow-xs">
                <i data-lucide="camera" class="w-3.5 h-3.5 text-gray-400"></i>
                <span class="font-medium">Kamera:</span>
                <select x-model="selectedDeviceId" @change="switchCamera()" class="border-0 bg-transparent text-gray-800 font-semibold focus:outline-none cursor-pointer">
                    <template x-for="(dev, idx) in videoDevices" :key="dev.deviceId">
                        <option :value="dev.deviceId" x-text="dev.label || ('Camera ' + (idx + 1))"></option>
                    </template>
                </select>
            </div>

            {{-- Error Message Alert --}}
            <div x-show="error" x-cloak class="mt-4 w-full p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 flex items-start gap-2.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 flex-shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-semibold" x-text="error"></p>
                    <p class="text-[11px] text-rose-500 mt-0.5">Pastikan Anda telah memberikan izin akses kamera ke browser (klik ikon gembok/kamera di address bar).</p>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Live Scan Result & Recent Scans (5 Columns) --}}
    <div class="lg:col-span-5 space-y-5">

        {{-- Live Scan Result Panel --}}
        <div id="result-panel" class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-xs hidden">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Live Scan Result</span>
                <span id="result-status-badge" class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700">SAFE</span>
            </div>
            <div id="result-content"></div>
        </div>

        {{-- Idle Result State --}}
        <div id="result-idle" class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-xs">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Live Scan Result</span>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-400 tracking-wider">IDLE</span>
            </div>
            <div class="py-5 text-center">
                <i data-lucide="scan-line" class="w-9 h-9 text-gray-300 mx-auto mb-2"></i>
                <p class="text-sm font-semibold text-gray-700">Scan a QR code to inspect substance data</p>
                <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">Point camera at container barcode or enter identifier code manually.</p>
            </div>
        </div>

        {{-- Recent Terminal Scans --}}
        <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-xs">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Recent Terminal Scans</h3>
            <div class="divide-y divide-gray-100">
                @php
                    $recentScans = [
                        ['name' => 'Sodium Hydroxide',      'code' => 'NaOH-89',  'time' => '2 min ago',  'id' => 1],
                        ['name' => 'Hydrochloric Acid 37%', 'code' => 'HCl-12',   'time' => '10 min ago', 'id' => 2],
                        ['name' => 'Ethanol (Anhydrous)',   'code' => 'ETH-84',   'time' => '1 hour ago', 'id' => 3],
                        ['name' => 'Acetone (AR Grade)',    'code' => 'ACT-80',   'time' => '3 hours ago','id' => 4],
                        ['name' => 'Nitric Acid 65%',       'code' => 'HNO3-42',  'time' => 'Yesterday',  'id' => 5],
                    ];
                @endphp
                @foreach($recentScans as $scan)
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="text-xs font-bold text-gray-900">{{ $scan['name'] }}</p>
                        <p class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $scan['code'] }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-xs text-gray-400 font-medium">{{ $scan['time'] }}</span>
                        <a href="{{ route('chemicals.show', $scan['id']) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                            View
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Not Found Panel --}}
        <div id="not-found-panel" class="bg-rose-50 border border-rose-200 rounded-xl p-4 hidden">
            <div class="flex items-start gap-3">
                <i data-lucide="x-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-xs font-bold text-rose-800">Chemical Not Found</p>
                    <p class="text-xs text-rose-600 mt-1" id="not-found-msg"></p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Manual Entry Modal --}}
<div id="manual-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 border border-gray-100 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900">Enter Barcode Manually</h3>
            <button type="button" onclick="document.getElementById('manual-modal').classList.add('hidden')"
                    class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <p class="text-xs text-gray-500 mb-3">Input chemical code, batch number, or identifier tag (e.g. <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-700">CHM-0001</code>).</p>
        <input type="text" id="manual-code"
               placeholder="e.g. CHM-0001"
               class="block w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 mb-4"
               onkeydown="if(event.key==='Enter'){ submitManual(); }">
        <button type="button" onclick="submitManual()"
                class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-xs transition-colors cursor-pointer">
            Lookup Substance
        </button>
    </div>
</div>

@endsection

@push('styles')
<style>
.qr-viewport {
    background-color: #0d1322 !important;
    border-radius: 16px !important;
    width: 100% !important;
    height: 380px !important;
    min-height: 380px !important;
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
    box-shadow: 0 10px 25px -5px rgba(13, 19, 34, 0.4) !important;
}

.qr-target-box {
    width: 240px !important;
    height: 240px !important;
    border: 2px dashed rgba(59, 130, 246, 0.45) !important;
    border-radius: 24px !important;
    position: relative !important;
    box-sizing: border-box !important;
}

.qr-bracket {
    position: absolute !important;
    width: 34px !important;
    height: 34px !important;
    pointer-events: none !important;
    z-index: 12 !important;
}
.qr-bracket-tl {
    top: -2px !important;
    left: -2px !important;
    border-top: 4px solid #2563eb !important;
    border-left: 4px solid #2563eb !important;
    border-top-left-radius: 16px !important;
}
.qr-bracket-tr {
    top: -2px !important;
    right: -2px !important;
    border-top: 4px solid #2563eb !important;
    border-right: 4px solid #2563eb !important;
    border-top-right-radius: 16px !important;
}
.qr-bracket-bl {
    bottom: -2px !important;
    left: -2px !important;
    border-bottom: 4px solid #2563eb !important;
    border-left: 4px solid #2563eb !important;
    border-bottom-left-radius: 16px !important;
}
.qr-bracket-br {
    bottom: -2px !important;
    right: -2px !important;
    border-bottom: 4px solid #2563eb !important;
    border-right: 4px solid #2563eb !important;
    border-bottom-right-radius: 16px !important;
}

.qr-marker {
    position: absolute !important;
    width: 34px !important;
    height: 34px !important;
    background-color: #ffffff !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.25) !important;
    z-index: 11 !important;
    transition: opacity 0.3s ease !important;
}
.qr-marker-tl { top: 20px !important; left: 20px !important; }
.qr-marker-tr { top: 20px !important; right: 20px !important; }
.qr-marker-bl { bottom: 20px !important; left: 20px !important; }

.qr-laser-line {
    position: absolute;
    left: 8px;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    height: 3px;
    background: linear-gradient(90deg, rgba(239, 68, 68, 0.1) 0%, #ef4444 15%, #ff6b6b 50%, #ef4444 85%, rgba(239, 68, 68, 0.1) 100%);
    box-shadow: 0 0 12px 2px rgba(239, 68, 68, 0.85), 0 0 4px #ef4444;
    border-radius: 9999px;
    z-index: 13;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.qr-laser-line::before {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    top: -12px;
    bottom: -12px;
    background: radial-gradient(ellipse at center, rgba(239, 68, 68, 0.35) 0%, rgba(239, 68, 68, 0) 75%);
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.qr-laser-line.laser-active {
    top: 0;
    animation: laserSweep 2.2s ease-in-out infinite;
}

.qr-laser-line.laser-active::before {
    opacity: 1;
}

@keyframes laserSweep {
    0% {
        transform: translateY(16px);
        opacity: 0.85;
    }
    50% {
        transform: translateY(220px);
        opacity: 1;
    }
    100% {
        transform: translateY(16px);
        opacity: 0.85;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
function qrScannerEngine() {
    return {
        scanning: false,
        error: '',
        stream: null,
        animFrame: null,
        videoDevices: [],
        selectedDeviceId: '',

        async initScanner() {
            // Camera does NOT auto-start on load as requested
            try {
                if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    this.videoDevices = devices.filter(d => d.kind === 'videoinput');
                    // Preselect DroidCam if present
                    const droid = this.videoDevices.find(d => d.label.toLowerCase().includes('droidcam'));
                    if (droid) {
                        this.selectedDeviceId = droid.deviceId;
                    } else if (this.videoDevices.length > 0) {
                        this.selectedDeviceId = this.videoDevices[0].deviceId;
                    }
                }
            } catch (e) {
                console.warn('Device enumeration note:', e);
            }
        },

        async toggleCamera() {
            if (this.scanning) {
                this.stopCamera();
            } else {
                await this.startCamera();
            }
        },

        async startCamera() {
            this.error = '';

            // Stop any existing stream
            this.stopCamera();

            let stream = null;

            // Strategy 1: Try with selected device if chosen
            if (this.selectedDeviceId) {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            deviceId: { exact: this.selectedDeviceId },
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    });
                } catch (e) {
                    console.warn('Direct deviceId attempt failed, falling back...', e);
                }
            }

            // Strategy 2: Try ideal environment constraint (works on mobile, fallback on desktop)
            if (!stream) {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: { ideal: 'environment' },
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    });
                } catch (e) {
                    console.warn('Ideal facingMode failed, trying generic video...', e);
                }
            }

            // Strategy 3: Pure video: true constraint (compatible with all webcams & DroidCam virtual cameras)
            if (!stream) {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: true });
                } catch (e) {
                    console.error('All camera attempts failed:', e);
                    this.error = 'Kamera tidak dapat diakses (' + (e.name || e.message) + '). Silakan pastikan izin kamera diizinkan di browser.';
                    return;
                }
            }

            this.stream = stream;
            const video = document.getElementById('qr-video');
            if (video) {
                video.srcObject = stream;
                video.setAttribute('playsinline', 'true');
                video.muted = true;
                try {
                    await video.play();
                } catch (playErr) {
                    console.warn('Play interrupted, waiting for loadeddata:', playErr);
                    video.onloadeddata = () => video.play();
                }
            }

            this.scanning = true;

            // Refresh devices list after permission is granted (to get clear device labels)
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                this.videoDevices = devices.filter(d => d.kind === 'videoinput');
                const currentTrack = stream.getVideoTracks()[0];
                if (currentTrack) {
                    const settings = currentTrack.getSettings();
                    if (settings && settings.deviceId) {
                        this.selectedDeviceId = settings.deviceId;
                    }
                }
            } catch (err) {}

            this.scanFrame();
        },

        stopCamera() {
            this.scanning = false;
            if (this.animFrame) {
                cancelAnimationFrame(this.animFrame);
                this.animFrame = null;
            }
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            const video = document.getElementById('qr-video');
            if (video) {
                video.srcObject = null;
            }
        },

        async switchCamera() {
            if (this.scanning) {
                await this.startCamera();
            }
        },

        scanFrame() {
            if (!this.scanning) return;

            const video = document.getElementById('qr-video');
            const canvas = document.getElementById('qr-canvas');

            if (video && video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d', { willReadFrequently: true });
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

                if (typeof jsQR !== 'undefined') {
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert"
                    });
                    if (code && code.data) {
                        this.stopCamera();
                        lookupCode(code.data);
                        return;
                    }
                }
            }

            this.animFrame = requestAnimationFrame(() => this.scanFrame());
        }
    };
}

function submitManual() {
    const input = document.getElementById('manual-code');
    const val = input ? input.value.trim() : '';
    if (val) {
        document.getElementById('manual-modal').classList.add('hidden');
        lookupCode(val);
    }
}

async function lookupCode(code) {
    if (!code || !code.trim()) return;

    const resultPanel = document.getElementById('result-panel');
    const resultIdle  = document.getElementById('result-idle');
    const notFound    = document.getElementById('not-found-panel');

    resultPanel.classList.add('hidden');
    resultIdle.classList.add('hidden');
    notFound.classList.add('hidden');

    try {
        const resp = await fetch('{{ route("qr-scanner.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code })
        });
        const data = await resp.json();

        if (data.found) {
            const c = data.chemical;
            const badgeClass = c.status === 'CRITICAL' || c.status === 'EXPIRED'
                ? 'bg-red-100 text-red-700'
                : c.status === 'LOW' ? 'bg-amber-100 text-amber-700'
                : 'bg-emerald-100 text-emerald-700';

            const badge = document.getElementById('result-status-badge');
            badge.className = `text-xs font-bold px-2.5 py-0.5 rounded-full ${badgeClass}`;
            badge.textContent = c.status === 'NORMAL' ? 'SAFE' : c.status;

            document.getElementById('result-content').innerHTML = `
                <p class="text-base font-bold text-gray-900">${c.chemical_name}</p>
                <p class="text-xs text-gray-400 font-mono mt-0.5 mb-4">Code: ${c.chemical_code} ${c.location ? '· Loc: ' + c.location : ''}</p>
                <div class="bg-gray-50 rounded-xl p-3.5 space-y-2 mb-4 border border-gray-100">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500 font-medium">Current Stock</span>
                        <span class="font-bold text-gray-900">${c.current_stock} ${c.unit}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500 font-medium">Storage Location</span>
                        <span class="font-semibold text-gray-700">${c.location || '—'}</span>
                    </div>
                    ${c.expiry_date ? `
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500 font-medium">Expiry Date</span>
                        <span class="font-semibold text-gray-700">${c.expiry_date}</span>
                    </div>` : ''}
                </div>
                <a href="${c.url}"
                   class="block w-full py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold rounded-xl text-center shadow-xs transition-colors">
                    Open Chemical Detail
                </a>`;
            resultPanel.classList.remove('hidden');
        } else {
            document.getElementById('not-found-msg').textContent = data.message;
            notFound.classList.remove('hidden');
            resultIdle.classList.remove('hidden');
        }
    } catch (e) {
        document.getElementById('not-found-msg').textContent = 'Network error or unable to process scan request. Please try again.';
        notFound.classList.remove('hidden');
        resultIdle.classList.remove('hidden');
    }
}
</script>
@endpush
