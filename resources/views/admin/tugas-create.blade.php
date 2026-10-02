@extends('layouts.app')

@section('title', 'Buat Tugas - SIM Monitoring SPMB')
@section('page-title', 'Buat Tugas Baru')
@section('page-subtitle', 'Tetapkan target dan tugaskan ke panitia')

@section('content')

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-slate-200/80 p-5 sm:p-7 shadow-xs">
        <form method="POST" action="{{ route('admin.tugas.store') }}" class="space-y-4">
            @csrf

            {{-- Divisi --}}
            <div>
                <label for="divisi_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Divisi <span class="text-red-500">*</span></label>
                <select id="divisi_id" name="divisi_id" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors">
                    <option value="">Pilih Divisi</option>
                    @foreach($divisiList as $d)
                        <option value="{{ $d->id }}" {{ old('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                    @endforeach
                </select>
                @error('divisi_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- PIC --}}
            <div>
                <label for="ditugaskan_ke" class="block text-xs font-semibold text-slate-700 mb-1.5">Ditugaskan Ke (PIC)</label>
                <select id="ditugaskan_ke" name="ditugaskan_ke"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors">
                    <option value="">— Belum Ditentukan —</option>
                    @foreach($panitiaList as $p)
                        <option value="{{ $p->id }}" {{ old('ditugaskan_ke') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_lengkap }} — {{ $p->divisi?->nama_divisi ?? 'Tanpa Divisi' }}
                        </option>
                    @endforeach
                </select>
                @error('ditugaskan_ke')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Judul --}}
            <div>
                <label for="judul_tugas" class="block text-xs font-semibold text-slate-700 mb-1.5">Judul Tugas <span class="text-red-500">*</span></label>
                <input id="judul_tugas" name="judul_tugas" type="text" value="{{ old('judul_tugas') }}" required maxlength="200"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors"
                    placeholder="Contoh: Verifikasi Berkas Pendaftar Gel. 1">
                @error('judul_tugas')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1.5">Instruksi / Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors resize-none"
                    placeholder="Jelaskan apa yang harus dilakukan panitia...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipe Target & Prioritas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tipe_target" class="block text-xs font-semibold text-slate-700 mb-1.5">Tipe Target <span class="text-red-500">*</span></label>
                    <select id="tipe_target" name="tipe_target" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors">
                        <option value="numerik" {{ old('tipe_target') === 'numerik' ? 'selected' : '' }}>Numerik (Kuantitas)</option>
                        <option value="dokumen" {{ old('tipe_target') === 'dokumen' ? 'selected' : '' }}>Dokumen (File/Link)</option>
                        <option value="checklist" {{ old('tipe_target') === 'checklist' ? 'selected' : '' }}>Checklist</option>
                    </select>
                </div>
                <div>
                    <label for="prioritas" class="block text-xs font-semibold text-slate-700 mb-1.5">Prioritas <span class="text-red-500">*</span></label>
                    <select id="prioritas" name="prioritas" required
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors">
                        <option value="rendah" {{ old('prioritas') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                        <option value="sedang" {{ old('prioritas', 'sedang') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="tinggi" {{ old('prioritas') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                        <option value="mendesak" {{ old('prioritas') === 'mendesak' ? 'selected' : '' }}>Mendesak</option>
                    </select>
                </div>
            </div>

            {{-- Target Jumlah & Tenggat --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div id="target-jumlah-wrapper">
                    <label for="target_jumlah" class="block text-xs font-semibold text-slate-700 mb-1.5">Kuota Target</label>
                    <input id="target_jumlah" name="target_jumlah" type="number" value="{{ old('target_jumlah') }}" min="1"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors"
                        placeholder="Contoh: 150">
                    <p class="text-xs text-slate-500 font-medium mt-1">Wajib diisi jika tipe target Numerik.</p>
                </div>
                <div>
                    <label for="tenggat_waktu" class="block text-xs font-semibold text-slate-700 mb-1.5">Tenggat Waktu</label>
                    <input id="tenggat_waktu" name="tenggat_waktu" type="date" value="{{ old('tenggat_waktu') }}"
                        class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors">
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-2.5 pt-4 border-t border-slate-200/80">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm shadow-blue-600/20 transition-colors">
                    Buat Tugas
                </button>
                <a href="{{ route('admin.monitoring') }}"
                    class="px-3.5 py-2 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Show/hide target_jumlah field based on tipe_target
    const tipeTargetSelect = document.getElementById('tipe_target');
    const targetJumlahWrapper = document.getElementById('target-jumlah-wrapper');

    function toggleTargetJumlah() {
        if (tipeTargetSelect.value === 'numerik') {
            targetJumlahWrapper.style.display = 'block';
        } else {
            targetJumlahWrapper.style.display = 'none';
        }
    }

    tipeTargetSelect.addEventListener('change', toggleTargetJumlah);
    toggleTargetJumlah();
</script>
@endpush
