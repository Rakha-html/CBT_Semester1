@extends('layouts.app')

@section('title', 'Tugas Saya - SIM Monitoring SPMB')
@section('page-title', 'Tugas Saya')
@section('page-subtitle', 'Daftar tugas yang ditugaskan kepada Anda')

@section('content')

@if($tugas->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200/80 p-10 text-center shadow-xs">
        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-lg bg-blue-50 text-blue-600 border border-blue-100">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h3 class="text-sm font-semibold text-slate-900 mb-1">Belum Ada Tugas</h3>
        <p class="text-xs text-slate-500 font-medium">Saat ini belum ada tugas yang ditugaskan kepada Anda.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 items-start">
        @foreach($tugas as $t)
        @php
            $latestPengumpulan = $t->pengumpulan->first();
            $needsRevision = $latestPengumpulan && $latestPengumpulan->status_verifikasi === 'perlu_revisi';
        @endphp
        <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between cursor-pointer group {{ $needsRevision ? 'ring-1 ring-amber-300' : '' }}"
             id="card-tugas-{{ $t->id }}"
             onclick="toggleTaskCard({{ $t->id }})">
            <div>
                {{-- Header Status & Priority --}}
                <div class="flex items-start justify-between gap-2 mb-2.5">
                    <span id="badge-status-{{ $t->id }}" class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $t->badge_status['class'] }}">
                        {{ $t->badge_status['label'] }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $t->badge_prioritas['class'] }}">
                        {{ $t->badge_prioritas['label'] }}
                    </span>
                </div>

                {{-- Revision Warning --}}
                @if($needsRevision)
                    <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-md bg-amber-50 border border-amber-200/80 mb-2.5">
                        <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="text-xs font-medium text-amber-700">Perlu Revisi Laporan</span>
                    </div>
                @endif

                {{-- Title --}}
                <h3 class="text-sm font-semibold text-slate-900 mb-1 leading-snug group-hover:text-blue-600 transition-colors">{{ $t->judul_tugas }}</h3>

                {{-- Divisi --}}
                <p class="text-xs text-slate-500 font-medium mb-2.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                    {{ $t->divisi->nama_divisi }}
                </p>

                {{-- Description --}}
                @if($t->deskripsi)
                    <p class="text-xs text-slate-600 mb-3.5 line-clamp-2 leading-relaxed">{{ $t->deskripsi }}</p>
                @endif

                {{-- Overall Progress Display with % --}}
                <div class="mb-3 p-2.5 rounded-lg bg-slate-50/80 border border-slate-100">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-semibold text-slate-700">Progres Saat Ini</span>
                        <div class="flex items-center gap-1.5">
                            @if($t->tipe_target === 'numerik' && $t->target_jumlah)
                                <span class="text-xs text-slate-500 font-medium font-mono">({{ $t->jumlah_tercapai }}/{{ $t->target_jumlah }})</span>
                            @endif
                            <span id="progres-display-{{ $t->id }}" class="text-xs font-semibold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200/80">
                                {{ $t->progres_persen }}%
                            </span>
                        </div>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div id="progres-bar-{{ $t->id }}" class="h-full rounded-full transition-all duration-500 {{ $t->progres_persen >= 100 ? 'bg-emerald-600' : 'bg-blue-600' }}"
                             style="width: {{ $t->progres_persen }}%"></div>
                    </div>
                </div>

                {{-- Card Click Toggle Indicator --}}
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium group-hover:text-slate-800 transition-colors select-none">
                    <span class="flex items-center gap-1.5 font-medium">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        <span id="toggle-label-{{ $t->id }}">Klik kartu untuk atur status &amp; progres</span>
                    </span>
                    <svg id="chevron-{{ $t->id }}" class="w-3.5 h-3.5 transform transition-transform duration-200 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Fitur Kategori & Satuan Persen (%) - DITAMPILKAN SETELAH USER MENGKLIK CARD TUGAS --}}
                <div id="section-update-{{ $t->id }}" class="hidden mt-3 pt-3 border-t border-slate-100" onclick="event.stopPropagation()">
                    <form id="form-update-{{ $t->id }}" action="{{ route('staff.update-status', $t) }}" method="POST" onsubmit="submitQuickUpdate(event, {{ $t->id }})"
                          class="p-3.5 rounded-lg bg-slate-50/70 border border-slate-200/80 space-y-3">
                        @csrf
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-800">
                                Atur Status &amp; Progres
                            </span>
                            <span id="save-status-{{ $t->id }}" class="text-xs font-semibold hidden"></span>
                        </div>

                        {{-- Dropdown Kategori Tugas --}}
                        <div>
                            <label for="status-select-{{ $t->id }}" class="block text-xs font-semibold text-slate-700 mb-1">
                                Kategori Tugas
                            </label>
                            <select name="status" id="status-select-{{ $t->id }}" onchange="handleStatusChange({{ $t->id }}, this.value)"
                                    class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                                <option value="belum_dikerjakan" {{ $t->status === 'belum_dikerjakan' ? 'selected' : '' }}>Belum Dikerjakan</option>
                                <option value="sedang_dikerjakan" {{ $t->status === 'sedang_dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                                <option value="selesai" {{ $t->status === 'selesai' || $t->status === 'sudah_dikerjakan' ? 'selected' : '' }}>Sudah Dikerjakan</option>
                            </select>
                        </div>

                        {{-- Input Progres Persen (HANYA KOLOM INPUT TANPA OPSI 20%, 50%, 100%) --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="progres-input-{{ $t->id }}" class="text-xs font-semibold text-slate-700">
                                    Capaian Progres (Satuan %)
                                </label>
                                <span class="text-xs text-slate-500 font-medium">100% &rarr; Otomatis Selesai</span>
                            </div>
                            <div class="relative flex items-center">
                                <input type="number" min="0" max="100" name="progres_persen" id="progres-input-{{ $t->id }}"
                                       value="{{ $t->progres_persen }}"
                                       oninput="handleProgresInput({{ $t->id }}, this.value)"
                                       class="w-full pl-3 pr-7 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
                                       placeholder="0 - 100">
                                <span class="absolute right-3 text-xs font-semibold text-slate-500 pointer-events-none select-none">%</span>
                            </div>
                        </div>

                        {{-- Tombol Simpan Perubahan (Royal Blue) --}}
                        <button type="submit" id="btn-save-{{ $t->id }}"
                                class="w-full py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm shadow-blue-600/20 transition-colors flex items-center justify-center gap-1.5">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            {{-- Footer Tenggat & Lapor --}}
            <div class="flex items-center justify-between pt-3 mt-3 border-t border-slate-100">
                @if($t->tenggat_waktu)
                    <span class="text-xs {{ $t->tenggat_waktu->isPast() && $t->status !== 'selesai' ? 'text-red-600 font-semibold' : 'text-slate-500 font-medium' }}">
                        {{ $t->tenggat_waktu->isPast() && $t->status !== 'selesai' ? '⚠ ' : '' }}{{ $t->tenggat_waktu->format('d M Y') }}
                    </span>
                @else
                    <span class="text-xs text-slate-500 font-medium">Tanpa tenggat</span>
                @endif

                <a href="{{ route('staff.lapor', $t) }}" onclick="event.stopPropagation()"
                   class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200/80 rounded-md transition-colors">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    Kirim Bukti / Lapor
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Script Sinkronisasi Progres, Kategori & Card Toggle --}}
    <script>
        function toggleTaskCard(taskId) {
            const section = document.getElementById('section-update-' + taskId);
            const chevron = document.getElementById('chevron-' + taskId);
            const label = document.getElementById('toggle-label-' + taskId);

            if (section.classList.contains('hidden')) {
                section.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
                if (label) label.innerText = 'Tutup pengaturan';
            } else {
                section.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
                if (label) label.innerText = 'Klik kartu untuk atur status & progres';
            }
        }

        function handleProgresInput(taskId, val) {
            let num = parseInt(val, 10);
            if (isNaN(num)) num = 0;
            if (num < 0) num = 0;
            if (num > 100) {
                num = 100;
                document.getElementById('progres-input-' + taskId).value = 100;
            }

            const statusSelect = document.getElementById('status-select-' + taskId);
            const progresDisplay = document.getElementById('progres-display-' + taskId);
            const progresBar = document.getElementById('progres-bar-' + taskId);

            if (progresDisplay) progresDisplay.innerText = num + '%';
            if (progresBar) {
                progresBar.style.width = num + '%';
                if (num >= 100) {
                    progresBar.className = 'h-full rounded-full transition-all duration-500 bg-emerald-600';
                } else {
                    progresBar.className = 'h-full rounded-full transition-all duration-500 bg-blue-600';
                }
            }

            // ATURAN: Jika user mengetik progres = 100%, otomatis opsi kategori tugas menjadi "sudah dikerjakan" (selesai)
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

        function handleStatusChange(taskId, status) {
            const input = document.getElementById('progres-input-' + taskId);
            const progresDisplay = document.getElementById('progres-display-' + taskId);
            const progresBar = document.getElementById('progres-bar-' + taskId);

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

            let updatedVal = parseInt(input.value, 10);
            if (progresDisplay) progresDisplay.innerText = updatedVal + '%';
            if (progresBar) {
                progresBar.style.width = updatedVal + '%';
                if (updatedVal >= 100) {
                    progresBar.className = 'h-full rounded-full transition-all duration-500 bg-emerald-600';
                } else {
                    progresBar.className = 'h-full rounded-full transition-all duration-500 bg-blue-600';
                }
            }
        }

        function submitQuickUpdate(e, taskId) {
            e.preventDefault();
            const form = document.getElementById('form-update-' + taskId);
            const statusMsg = document.getElementById('save-status-' + taskId);
            const btnSave = document.getElementById('btn-save-' + taskId);
            const formData = new FormData(form);

            statusMsg.innerText = 'Menyimpan...';
            statusMsg.className = 'text-xs text-blue-600 font-semibold inline-block';
            btnSave.disabled = true;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnSave.disabled = false;
                if (data.success) {
                    statusMsg.innerText = '✓ Tersimpan';
                    statusMsg.className = 'text-xs text-emerald-600 font-semibold inline-block';

                    // Update badge di header kartu
                    const badge = document.getElementById('badge-status-' + taskId);
                    if (badge) {
                        badge.innerText = data.status_label;
                        if (data.status === 'selesai' || data.status === 'sudah_dikerjakan') {
                            badge.className = 'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/80';
                        } else if (data.status === 'sedang_dikerjakan') {
                            badge.className = 'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/80';
                        } else {
                            badge.className = 'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200/80';
                        }
                    }

                    setTimeout(() => {
                        statusMsg.className = 'hidden';
                    }, 3500);
                } else {
                    statusMsg.innerText = 'Gagal';
                    statusMsg.className = 'text-xs text-red-600 font-semibold inline-block';
                }
            })
            .catch(() => {
                btnSave.disabled = false;
                form.submit();
            });
        }
    </script>
@endif

@endsection

