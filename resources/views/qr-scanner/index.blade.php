@extends('layouts.app')

@php
    $title      = 'QR Scanner';
    $breadcrumb = [['label' => 'QR Scanner', 'url' => route('qr-scanner.index')]];
@endphp

@section('title', 'QR Scanner')

@section('content')

<div class="max-w-4xl">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900">QR Code Scanner</h1>
        <p class="text-sm text-gray-500 mt-0.5">Scan a chemical QR code or enter a code manually to look up inventory details</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- Scanner Card -->
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i data-lucide="scan-line" class="w-4 h-4 text-blue-500"></i>Camera Scanner
                </h2>
            </div>
            <div class="p-5" x-data="qrScanner()">

                <!-- Video Feed -->
                <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio:4/3;">
                    <video id="qr-video" class="w-full h-full object-cover" playsinline></video>
                    <canvas id="qr-canvas" class="hidden"></canvas>

                    <!-- Scan overlay -->
                    <div x-show="scanning" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-48 h-48 border-2 border-blue-400 rounded-lg opacity-70 relative">
                            <div class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-blue-400 rounded-tl"></div>
                            <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-blue-400 rounded-tr"></div>
                            <div class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-blue-400 rounded-bl"></div>
                            <div class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-blue-400 rounded-br"></div>
                            <div class="absolute inset-x-0 top-0 h-0.5 bg-blue-400 opacity-80" style="animation: scan 2s linear infinite;"></div>
                        </div>
                    </div>

                    <!-- Placeholder when not scanning -->
                    <div x-show="!scanning" class="absolute inset-0 flex flex-col items-center justify-center text-white">
                        <i data-lucide="camera-off" class="w-12 h-12 mb-3 opacity-40"></i>
                        <p class="text-sm opacity-60">Camera not active</p>
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    <button @click="startCamera()"
                            x-show="!scanning"
                            class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="camera" class="w-4 h-4"></i>Start Camera
                    </button>
                    <button @click="stopCamera()"
                            x-show="scanning"
                            class="flex-1 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="camera-off" class="w-4 h-4"></i>Stop Camera
                    </button>
                </div>

                <p x-show="error" x-text="error" class="mt-3 text-xs text-red-600 bg-red-50 border border-red-200 rounded p-2"></p>
            </div>
        </div>

        <!-- Manual + Result Card -->
        <div class="space-y-5">

            <!-- Manual Entry -->
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i data-lucide="keyboard" class="w-4 h-4 text-blue-500"></i>Manual Entry
                </h2>
                <div class="flex gap-2">
                    <input type="text" id="manual-code" placeholder="e.g. CHM-0001 or CHEM:CHM-0001"
                           class="flex-1 rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                           @keydown.enter="lookupCode(document.getElementById('manual-code').value)">
                    <button onclick="lookupCode(document.getElementById('manual-code').value)"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition-colors">
                        Lookup
                    </button>
                </div>
            </div>

            <!-- Result Panel -->
            <div id="result-panel" class="bg-white border border-gray-200 rounded-lg p-5 hidden">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <i data-lucide="flask-conical" class="w-4 h-4 text-blue-500"></i>Chemical Found
                </h2>
                <div id="result-content"></div>
            </div>

            <!-- Not Found Panel -->
            <div id="not-found-panel" class="bg-red-50 border border-red-200 rounded-lg p-5 hidden">
                <div class="flex items-center gap-3">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm font-semibold text-red-700">Chemical Not Found</p>
                        <p class="text-xs text-red-500 mt-0.5" id="not-found-msg"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
@keyframes scan {
    0% { top: 0; }
    50% { top: calc(100% - 2px); }
    100% { top: 0; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
function qrScanner() {
    return {
        scanning: false,
        error: '',
        stream: null,
        animFrame: null,
        async startCamera() {
            this.error = '';
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                const video = document.getElementById('qr-video');
                video.srcObject = this.stream;
                await video.play();
                this.scanning = true;
                this.scanFrame();
            } catch (e) {
                this.error = 'Camera access denied or not available. Use manual entry instead.';
            }
        },
        stopCamera() {
            this.scanning = false;
            if (this.stream) this.stream.getTracks().forEach(t => t.stop());
            if (this.animFrame) cancelAnimationFrame(this.animFrame);
        },
        scanFrame() {
            if (!this.scanning) return;
            const video = document.getElementById('qr-video');
            const canvas = document.getElementById('qr-canvas');
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                if (code) {
                    this.stopCamera();
                    lookupCode(code.data);
                    return;
                }
            }
            this.animFrame = requestAnimationFrame(() => this.scanFrame());
        }
    }
}

async function lookupCode(code) {
    if (!code.trim()) return;
    const result = document.getElementById('result-panel');
    const notFound = document.getElementById('not-found-panel');
    result.classList.add('hidden');
    notFound.classList.add('hidden');

    try {
        const resp = await fetch('{{ route("qr-scanner.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ code })
        });
        const data = await resp.json();

        if (data.found) {
            const c = data.chemical;
            document.getElementById('result-content').innerHTML = `
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">${c.chemical_name}</p>
                            <p class="text-xs text-gray-400 font-mono">${c.chemical_code}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-xs font-medium">${c.status}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100">
                        <div><p class="text-xs text-gray-400">Current Stock</p><p class="text-lg font-bold text-gray-900">${c.current_stock} ${c.unit}</p></div>
                        <div><p class="text-xs text-gray-400">Location</p><p class="text-sm font-medium text-gray-700">${c.location || '—'}</p></div>
                        <div><p class="text-xs text-gray-400">CAS Number</p><p class="text-sm font-mono text-gray-700">${c.cas_number || '—'}</p></div>
                        <div><p class="text-xs text-gray-400">Expiry Date</p><p class="text-sm font-medium text-gray-700">${c.expiry_date || '—'}</p></div>
                    </div>
                    <a href="${c.url}" class="mt-3 w-full inline-flex items-center justify-center gap-2 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition-colors">
                        View Full Details →
                    </a>
                </div>`;
            result.classList.remove('hidden');
        } else {
            document.getElementById('not-found-msg').textContent = data.message;
            notFound.classList.remove('hidden');
        }
    } catch (e) {
        document.getElementById('not-found-msg').textContent = 'Network error. Please try again.';
        notFound.classList.remove('hidden');
    }
}
</script>
@endpush
