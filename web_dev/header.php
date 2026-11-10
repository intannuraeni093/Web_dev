<!-- HEADER UTAMA -->
<header class="bg-white border-b border-slate-100 sticky top-0 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Brand -->
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-600 text-white text-xl shadow-md shadow-indigo-600/20">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 leading-tight">WebDev Hub</h1>
                    <p class="text-[10px] text-slate-500">Proyek Pemrograman Web 2026</p>
                </div>
            </div>

            <!-- Audio Player & Musik Control (Fitur Audio) -->
            <div class="hidden md:flex items-center gap-3 px-4 py-1.5 bg-slate-100 rounded-full text-slate-700">
                <span class="text-xs font-medium"><i class="fas fa-music text-indigo-500 animate-bounce"></i> Musik Belajar</span>
                <div class="flex items-center gap-2">
                    <button onclick="controlAudio('play')" class="p-1.5 bg-white rounded-full text-indigo-600 hover:text-indigo-800 shadow-sm text-xs transition-colors">
                        <i class="fas fa-play"></i>
                    </button>
                    <button onclick="controlAudio('pause')" class="p-1.5 bg-white rounded-full text-indigo-600 hover:text-indigo-800 shadow-sm text-xs transition-colors">
                        <i class="fas fa-pause"></i>
                    </button>
                    <input type="range" id="audioVolume" min="0" max="1" step="0.1" value="0.5" oninput="setAudioVolume(this.value)" class="w-16 h-1 accent-indigo-600 bg-slate-300 rounded-lg appearance-none cursor-pointer">
                </div>
            </div>

            <!-- User Profil & Keluar -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2.5 text-right">
                    <div class="hidden sm:block">
                        <p class="text-xs font-semibold text-slate-800" id="userDisplayName"><?php echo htmlspecialchars($_SESSION['user']); ?></p>
                        <p class="text-[10px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full font-medium inline-block">Online (PHP Session)</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow">
                        <?php echo strtoupper(substr($_SESSION['user'], 0, 2)); ?>
                    </div>
                </div>
                <!-- Tombol Keluar Langsung Mengarah ke Logout PHP -->
                <a href="index.php?action=logout" class="p-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-colors" title="Keluar">
                    <i class="fas fa-power-off"></i>
                </a>
            </div>
        </div>
    </div>
</header>