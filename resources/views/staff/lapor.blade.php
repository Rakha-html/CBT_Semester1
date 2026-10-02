@extends('layouts.app')

@section('title', 'Lapor Progres - SIM Monitoring SPMB')
@section('page-title', 'Lapor Progres')
@section('page-subtitle', $tugas->judul_tugas)

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs mb-5">
    <a href="{{ route('staff.tugas-saya') }}" class="text-blue-600 hover:text-blue-700 font-semibold transition-colors">Tugas Saya</a>
    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-slate-800 font-medium">Lapor</span>
</nav>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form (Left) --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Revision Alert --}}
        @if($pengumpulanRevisi)
        <div class="bg-amber-50 border border-amber-200/80 rounded-xl p-4 text-xs">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <h4 class="font-semibold text-amber-900">Catatan Revisi dari Ketua SPMB</h4>
            </div>
            <p class="text-amber-800 leading-relaxed">{{ $pengumpulanRevisi->catatan_ketua }}</p>
            <p class="text-xs text-amber-700/90 font-medium mt-1.5">Dikirim {{ $pengumpulanRevisi->updated_at->diffForHumans() }}</p>
        </div>
        @endif

        {{-- Submit Form --}}
        <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Kirim Laporan Progres</h3>

            <form method="POST" action="{{ route('staff.submit-laporan', $tugas) }}" class="space-y-4">
                @csrf

                {{-- Adaptive Input berdasarkan tipe_target --}}
                @if($tugas->tipe_target === 'numerik')
                    <div>
                        <label for="jumlah_progres" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Jumlah Progres <span class="text-red-500">*</span>
                        </label>
                        <input id="jumlah_progres" name="jumlah_progres" type="number" min="1" required
                            value="{{ old('jumlah_progres') }}"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors"
                            placeholder="Masukkan kuantitas progres">
                        <p class="text-xs text-slate-500 font-medium mt-1">
                            Target saat ini: <span class="font-semibold text-slate-700">{{ $tugas->jumlah_tercapai }}/{{ $tugas->target_jumlah }}</span>
                        </p>
                        @error('jumlah_progres')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @elseif($tugas->tipe_target === 'dokumen')
                    <div>
                        <label for="tautan_berkas" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Link Dokumen / Berkas <span class="text-red-500">*</span>
                        </label>
                        <input id="tautan_berkas" name="tautan_berkas" type="text" required
                            value="{{ old('tautan_berkas') }}"
                            class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors"
                            placeholder="https://drive.google.com/... atau tautan file">
                        <p class="text-xs text-slate-500 font-medium mt-1">Lampirkan link Google Drive, Docs, Sheet, atau berkas terkait.</p>
                        @error('tautan_berkas')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @elseif($tugas->tipe_target === 'checklist')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Checklist Kesiapan</label>
                        <div class="space-y-1.5 p-3 bg-slate-50/70 rounded-lg border border-slate-200/80">
                            <label class="flex items-center gap-2.5 cursor-pointer p-1.5 rounded-md hover:bg-white transition-colors">
                                <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20" onchange="updateChecklist()">
                                <span class="text-xs text-slate-700 font-medium">Semua item telah diperiksa dan siap</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer p-1.5 rounded-md hover:bg-white transition-colors">
                                <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20" onchange="updateChecklist()">
                                <span class="text-xs text-slate-700 font-medium">Dokumentasi foto/arsip telah diverifikasi</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer p-1.5 rounded-md hover:bg-white transition-colors">
                                <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20" onchange="updateChecklist()">
                                <span class="text-xs text-slate-700 font-medium">Tidak ada kendala yang belum terselesaikan</span>
                            </label>
                        </div>
                        <input type="hidden" name="tautan_berkas" id="checklist_status" value="">
                    </div>
                @endif

                {{-- Kategori Status & Progres Persen --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-3.5 rounded-lg bg-slate-50/70 border border-slate-200/80">
                    <div>
                        <label for="lapor_status" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kategori Tugas
                        </label>
                        <select id="lapor_status" name="status" onchange="handleLaporStatusChange(this.value)"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                            <option value="belum_dikerjakan" {{ old('status', $tugas->status) === 'belum_dikerjakan' ? 'selected' : '' }}>Belum Dikerjakan</option>
                            <option value="sedang_dikerjakan" {{ old('status', $tugas->status) === 'sedang_dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="selesai" {{ old('status', $tugas->status) === 'selesai' || old('status', $tugas->status) === 'sudah_dikerjakan' ? 'selected' : '' }}>Sudah Dikerjakan</option>
                        </select>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="lapor_progres_persen" class="text-xs font-semibold text-slate-700">
                                Capaian Progres (Satuan %)
                            </label>
                            <span class="text-xs text-slate-500 font-medium">100% = Selesai</span>
                        </div>
                        <div class="relative flex items-center">
                            <input id="lapor_progres_persen" name="progres_persen" type="number" min="0" max="100"
                                value="{{ old('progres_persen', $tugas->progres_persen) }}"
                                oninput="handleLaporProgresInput(this.value)"
                                class="w-full pl-3 pr-7 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
                                placeholder="0 - 100">
                            <span class="absolute right-3 text-xs font-semibold text-slate-500 pointer-events-none">%</span>
                        </div>
                    </div>
                </div>

                {{-- Catatan Kendala --}}
                <div>
                    <label for="catatan_kendala" class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Kendala</label>
                    <textarea id="catatan_kendala" name="catatan_kendala" rows="3"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-xs text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors resize-none"
                        placeholder="Jelaskan jika ada kendala atau hambatan yang ditemui...">{{ old('catatan_kendala') }}</textarea>
                    @error('catatan_kendala')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-2.5 pt-4 border-t border-slate-100">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm shadow-blue-600/20 transition-colors">
                        Kirim Laporan
                    </button>
                    <a href="{{ route('staff.tugas-saya') }}"
                        class="px-3.5 py-2 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Task Info Sidebar (Right) --}}
    <div class="space-y-6">
        {{-- Task Details --}}
        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
            <h4 class="text-xs font-semibold text-slate-900 uppercase tracking-wider mb-3">Detail Tugas</h4>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-slate-500 font-medium">Divisi</p>
                    <p class="text-xs font-semibold text-slate-900 mt-0.5">{{ $tugas->divisi->nama_divisi }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Tipe Target</p>
                    <p class="text-xs font-semibold text-slate-900 mt-0.5 capitalize">{{ $tugas->tipe_target }}</p>
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-slate-500 font-medium">Progres Saat Ini</p>
                        <p class="text-xs font-semibold text-slate-900">{{ $tugas->progres_persen }}%</p>
                    </div>
                    @if($tugas->tipe_target === 'numerik' && $tugas->target_jumlah)
                        <p class="text-xs text-slate-500 font-medium mt-0.5 font-mono">Target: {{ $tugas->jumlah_tercapai }}/{{ $tugas->target_jumlah }}</p>
                    @endif
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $tugas->progres_persen >= 100 ? 'bg-emerald-600' : 'bg-blue-600' }}" style="width: {{ $tugas->progres_persen }}%"></div>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">Kategori Status</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium mt-0.5 {{ $tugas->badge_status['class'] }}">
                        {{ $tugas->badge_status['label'] }}
                    </span>
                </div>
                @if($tugas->tenggat_waktu)
                <div>
                    <p class="text-xs text-slate-500 font-medium">Tenggat</p>
                    <p class="text-xs font-semibold mt-0.5 {{ $tugas->tenggat_waktu->isPast() ? 'text-red-600' : 'text-slate-900' }}">
                        {{ $tugas->tenggat_waktu->format('d M Y') }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Riwayat Pengumpulan --}}
        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs">
            <h4 class="text-xs font-semibold text-slate-900 uppercase tracking-wider mb-3">Riwayat Pengumpulan</h4>
            <div class="divide-y divide-slate-100">
                @forelse($tugas->pengumpulan->sortByDesc('dikirim_pada') as $p)
                <div class="py-2.5 first:pt-0 last:pb-0">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 font-medium">{{ $p->dikirim_pada->format('d M Y, H:i') }}</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-xs font-medium {{ $p->badge_verifikasi['class'] }}">
                            {{ $p->badge_verifikasi['label'] }}
                        </span>
                    </div>
                    @if($p->jumlah_progres > 0)
                        <p class="text-xs text-slate-700 font-medium">Progres: +{{ $p->jumlah_progres }}</p>
                    @endif
                    @if($p->catatan_ketua)
                        <p class="text-xs text-slate-600 font-medium mt-0.5 italic">"{{ Str::limit($p->catatan_ketua, 80) }}"</p>
                    @endif
                </div>
                @empty
                <p class="text-xs text-slate-500 font-medium">Belum ada pengumpulan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function updateChecklist() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        const checked = Array.from(checkboxes).filter(cb => cb.checked).length;
        const total = checkboxes.length;
        document.getElementById('checklist_status').value = `Checklist: ${checked}/${total} selesai`;
    }

    function handleLaporProgresInput(val) {
        let num = parseInt(val, 10);
        if (isNaN(num)) num = 0;
        if (num < 0) num = 0;
        if (num > 100) {
            num = 100;
            document.getElementById('lapor_progres_persen').value = 100;
        }

        const statusSelect = document.getElementById('lapor_status');
        if (num >= 100) {
            statusSelect.value = 'selesai';
        } else if (num === 0) {
            if (statusSelect.value === 'selesai') {
                statusSelect.value = 'belum_dikerjakan';
            }
        } else if (num > 0 && num < 100) {
            statusSelect.value = 'sedang_dikerjakan';
        }
    }

    function handleLaporStatusChange(status) {
        const input = document.getElementById('lapor_progres_persen');
        let currentVal = parseInt(input.value, 10) || 0;
        if (status === 'selesai') {
            input.value = 100;
        } else if (status === 'belum_dikerjakan') {
            input.value = 0;
        } else if (status === 'sedang_dikerjakan') {
            if (currentVal === 0 || currentVal === 100) {
                input.value = 50;
            }
        }
    }
</script>
@endpush
