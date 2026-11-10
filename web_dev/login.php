<!-- PAGE 1: FORM LOGIN (Hanya dirender jika tidak ada session aktif) -->
<div id="loginPage" class="min-h-screen flex items-center justify-center bg-gradient-to-tr from-slate-900 via-indigo-950 to-slate-900 p-4">
    <!-- Dekorasi Latar Belakang -->
    <div class="absolute top-10 left-10 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-pulse-slow"></div>
    <div class="absolute bottom-10 right-10 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1.5s"></div>

    <div class="bg-white/95 backdrop-blur-md p-8 rounded-2xl shadow-2xl w-full max-w-md border border-white/20 transition-all transform hover:scale-[1.01] z-10">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 text-white text-3xl mb-4 shadow-lg shadow-indigo-600/30">
                <i class="fas fa-laptop-code"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">WebDev Hub</h2>
            <p class="text-sm text-slate-500 mt-1">Sistem Manajemen Portofolio & Proyek Web</p>
        </div>

        <!-- Notifikasi Error PHP jika login gagal -->
        <?php if (!empty($login_error)): ?>
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-600 text-xs rounded-xl flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-sm"></i>
                <span><?php echo $login_error; ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Login yang mengarah ke index.php sendiri -->
        <form id="loginForm" method="POST" action="index.php">
            <input type="hidden" name="login" value="1">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Nama Pengguna (Username)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="username" id="username" required value="mahasiswa123"
                            class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50/50" 
                            placeholder="Masukkan username anda...">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Kata Sandi (Password)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required value="rahasia"
                            class="w-full pl-10 pr-10 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all bg-slate-50/50" 
                            placeholder="Masukkan password anda...">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4 mb-6">
                <div class="flex items-center">
                    <input id="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                    <label for="remember" class="ml-2 block text-xs text-slate-600">Ingat Saya</label>
                </div>
                <a href="#" onclick="showNotification('Fitur reset password belum diimplementasikan!', 'warning')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Lupa Sandi?</a>
            </div>

            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-xl font-semibold text-sm transition-all shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2">
                Masuk Aplikasi <i class="fas fa-sign-in-alt"></i>
            </button>
        </form>

        <div class="mt-6 text-center border-t border-slate-100 pt-4">
            <p class="text-xs text-slate-500">
                Gunakan kredensial default:<br>
                <span class="font-mono font-semibold text-indigo-600">mahasiswa123</span> / <span class="font-mono font-semibold text-indigo-600">rahasia</span>
            </p>
        </div>
    </div>
</div>