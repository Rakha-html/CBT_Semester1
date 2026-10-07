<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Login - SIM Monitoring Kinerja Panitia SPMB">
    <title>Login - SIM Monitoring SPMB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="relative w-full max-w-md">
        {{-- Logo & Header --}}
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-blue-600 text-white shadow-sm shadow-blue-600/20 mb-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-slate-900 tracking-tight">SIM Monitoring SPMB</h1>
            <p class="text-xs text-slate-500 mt-1">Sistem Pemantauan Kinerja Panitia</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
            <h2 class="text-base font-semibold text-slate-900 mb-1">Masuk ke Akun Anda</h2>
            <p class="text-xs text-slate-500 mb-5">Gunakan email dan password yang telah terdaftar.</p>

            @if($errors->any())
                <div class="mb-4 px-3.5 py-2.5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-9 pr-3.5 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors"
                            placeholder="nama@sekolah.sch.id">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required
                            class="w-full pl-9 pr-10 py-2 rounded-lg border border-slate-200 bg-slate-50/50 text-sm text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-colors"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePasswordVisibility()" id="toggle-password-btn" title="Tampilkan / Sembunyikan Password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-700 focus:outline-none">
                            <svg id="eye-icon" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-slash-icon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center gap-2">
                    <input id="remember" name="remember" type="checkbox"
                        class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 cursor-pointer">
                    <label for="remember" class="text-xs text-slate-600 cursor-pointer select-none">Ingat saya</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm shadow-blue-600/20 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    Masuk
                </button>
            </form>

            {{-- Demo Accounts Quick-Fill Widget --}}
            <div class="mt-6 pt-5 border-t border-slate-100" x-data="{ open: false }">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Akun Demo (Pengujian)</span>
                    <button type="button" onclick="document.getElementById('demo-credentials').classList.toggle('hidden')" 
                        class="text-xs text-blue-600 hover:text-blue-700 font-semibold transition-colors">
                        Lihat Kredensial &darr;
                    </button>
                </div>

                <div id="demo-credentials" class="hidden mt-3 space-y-2.5 text-xs">
                    {{-- Admin --}}
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">Ketua SPMB / Admin</p>
                            <p class="text-slate-600 font-mono text-xs font-medium">adminspmb@smkwikrama.sch.id</p>
                            <p class="text-slate-500 text-xs font-medium">Pass: adminspmb2026</p>
                        </div>
                        <button type="button" onclick="fillLogin('adminspmb@smkwikrama.sch.id', 'adminspmb2026')"
                            class="px-2.5 py-1 bg-blue-50 text-blue-700 font-medium rounded-md text-xs border border-blue-100 hover:bg-blue-100 transition-colors">
                            Pilih
                        </button>
                    </div>

                    {{-- Panitia Header --}}
                    <p class="text-xs font-medium text-slate-500 pt-1">Panitia Tiap Divisi (Password: <code class="font-mono text-slate-700">password123</code>):</p>

                    <div class="bg-white rounded-lg border border-slate-200/80 divide-y divide-slate-100 overflow-hidden">
                        <button type="button" onclick="fillLogin('panitia.publikasi@smkwikrama.sch.id', 'password123')"
                            class="w-full text-left p-2.5 hover:bg-slate-50 flex items-center justify-between group transition-colors">
                            <div>
                                <span class="font-medium text-slate-700 block">1. Sosialisasi &amp; Publikasi</span>
                                <span class="text-xs text-slate-500 font-mono font-medium">panitia.publikasi@smkwikrama.sch.id</span>
                            </div>
                            <span class="text-xs text-blue-600 font-medium group-hover:underline">Gunakan</span>
                        </button>

                        <button type="button" onclick="fillLogin('panitia.pendaftaran@smkwikrama.sch.id', 'password123')"
                            class="w-full text-left p-2.5 hover:bg-slate-50 flex items-center justify-between group transition-colors">
                            <div>
                                <span class="font-medium text-slate-700 block">2. Pendaftaran &amp; Berkas</span>
                                <span class="text-xs text-slate-500 font-mono font-medium">panitia.pendaftaran@smkwikrama.sch.id</span>
                            </div>
                            <span class="text-xs text-blue-600 font-medium group-hover:underline">Gunakan</span>
                        </button>

                        <button type="button" onclick="fillLogin('panitia.seleksi@smkwikrama.sch.id', 'password123')"
                            class="w-full text-left p-2.5 hover:bg-slate-50 flex items-center justify-between group transition-colors">
                            <div>
                                <span class="font-medium text-slate-700 block">3. Seleksi &amp; Ujian</span>
                                <span class="text-xs text-slate-500 font-mono font-medium">panitia.seleksi@smkwikrama.sch.id</span>
                            </div>
                            <span class="text-xs text-blue-600 font-medium group-hover:underline">Gunakan</span>
                        </button>

                        <button type="button" onclick="fillLogin('panitia.keuangan@smkwikrama.sch.id', 'password123')"
                            class="w-full text-left p-2.5 hover:bg-slate-50 flex items-center justify-between group transition-colors">
                            <div>
                                <span class="font-medium text-slate-700 block">4. Keuangan &amp; Administrasi</span>
                                <span class="text-xs text-slate-500 font-mono font-medium">panitia.keuangan@smkwikrama.sch.id</span>
                            </div>
                            <span class="text-xs text-blue-600 font-medium group-hover:underline">Gunakan</span>
                        </button>

                        <button type="button" onclick="fillLogin('panitia.logistik@smkwikrama.sch.id', 'password123')"
                            class="w-full text-left p-2.5 hover:bg-slate-50 flex items-center justify-between group transition-colors">
                            <div>
                                <span class="font-medium text-slate-700 block">5. Logistik &amp; Sarpras</span>
                                <span class="text-xs text-slate-500 font-mono font-medium">panitia.logistik@smkwikrama.sch.id</span>
                            </div>
                            <span class="text-xs text-blue-600 font-medium group-hover:underline">Gunakan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-slate-500 font-medium mt-6">
            &copy; {{ date('Y') }} SIM Monitoring SPMB &middot; Panitia Penerimaan Peserta Didik Baru
        </p>
    </div>

    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                if (eyeIcon) eyeIcon.classList.remove('hidden');
                if (eyeSlashIcon) eyeSlashIcon.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                if (eyeIcon) eyeIcon.classList.add('hidden');
                if (eyeSlashIcon) eyeSlashIcon.classList.remove('hidden');
            }
        }
    </script>

</body>
</html>
