<!-- VIDEO & PROMOSI PROYEK (Fitur Video / Animasi) -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Video Section -->
    <div class="lg:col-span-2 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-video text-rose-500"></i> Video Panduan Proyek Web
                </h3>
                <span class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-md">MP4 Video</span>
            </div>
            <div class="relative aspect-video rounded-xl overflow-hidden bg-slate-900 border border-slate-200 shadow-inner group">
                <video id="tutorialVideo" class="w-full h-full object-cover" controls preload="metadata">
                    <source src="assets/video/Smart City Digital City.mp4" type="video/mp4">
                    Browser Anda tidak mendukung pemutaran video.
                </video>
            </div>
        </div>
        <div class="mt-3 text-xs text-slate-500 flex justify-between items-center">
            <span><i class="fas fa-info-circle mr-1"></i> Tonton video panduan ini untuk memahami kriteria proyek web-dev.</span>
            <button onclick="playCustomIntro()" class="text-indigo-600 font-semibold hover:underline">Putar Intro</button>
        </div>
    </div>

    <!-- Banner Informasi & Stats -->
    <div class="bg-gradient-to-br from-indigo-900 to-slate-900 text-white p-6 rounded-2xl shadow-xl flex flex-col justify-between relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-white/5 rounded-full blur-2xl"></div>
        <div>
            <span class="px-2.5 py-1 bg-white/10 rounded-full text-[10px] font-semibold uppercase tracking-wider">Tentang Platform</span>
            <h3 class="text-2xl font-black mt-3 leading-tight">WebDev Hub Portal</h3>
            <p class="text-xs text-indigo-200 mt-2 leading-relaxed">
                WebDev Hub merupakan pusat pengelolaan, pengunggahan dokumen, pencatatan pencapaian, serta verifikasi legalitas lembar proyek pemrograman web mahasiswa melalui integrasi Tanda Tangan Digital (TTD) berbasis HTML5 Canvas secara real-time.
            </p>
        </div>

        <!-- Stat Count -->
        <div class="grid grid-cols-3 gap-2 bg-white/5 border border-white/10 p-3 rounded-xl backdrop-blur-sm mt-6">
            <div class="text-center">
                <span class="block text-lg font-bold" id="statTotal">0</span>
                <span class="text-[9px] uppercase tracking-wider text-indigo-200">Total</span>
            </div>
            <div class="text-center border-x border-white/10">
                <span class="block text-lg font-bold text-emerald-400" id="statVerified">0</span>
                <span class="text-[9px] uppercase tracking-wider text-indigo-200">Terverifikasi</span>
            </div>
            <div class="text-center">
                <span class="block text-lg font-bold text-amber-400" id="statPending">0</span>
                <span class="text-[9px] uppercase tracking-wider text-indigo-200">Pending</span>
            </div>
        </div>
    </div>
</section>

<!-- KONTROL PENCARIAN, FILTER, DAN CRUD -->
<section class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Manajemen Proyek Web</h2>
            <p class="text-sm text-slate-500">Kelola berkas proyek, deskripsi, dan ttd digital proyek akhir mata kuliah secara dinamis.</p>
        </div>
        <!-- Tombol Tambah Proyek -->
        <button onclick="openModal('addEditModal')" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-xl font-semibold text-sm transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2 self-start sm:self-auto">
            <i class="fas fa-plus"></i> Tambah Proyek Baru
        </button>
    </div>

    <!-- Bar Pencarian & Penyaringan -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
        <!-- Search Input -->
        <div class="md:col-span-2 relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" id="searchInput" oninput="handleSearch()"
                class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-sm" 
                placeholder="Cari berdasarkan judul, kategori, atau deskripsi...">
        </div>
        <!-- Filter Kategori -->
        <div>
            <select id="filterCategory" onchange="handleSearch()"
                class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-sm text-slate-600">
                <option value="">Semua Kategori</option>
                <option value="E-Commerce">E-Commerce</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
                <option value="Media Sosial">Media Sosial</option>
                <option value="Game Web">Game Web</option>
                <option value="E-Learning">E-Learning</option>
            </select>
        </div>
        <!-- Reset Filter -->
        <div>
            <button onclick="resetFilters()" class="w-full px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-undo"></i> Reset Filter
            </button>
        </div>
    </div>

    <!-- TABEL DATA (Fitur Datatable & Konversi) -->
    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="w-full text-left border-collapse" id="projectTable">
            <thead>
                <tr class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-wider border-b border-slate-200">
                    <th class="py-3.5 px-4">Info Proyek</th>
                    <th class="py-3.5 px-4">Kategori</th>
                    <th class="py-3.5 px-4">Anggota Kelompok</th>
                    <th class="py-3.5 px-4">File Terunggah</th>
                    <th class="py-3.5 px-4 text-center">Tanda Tangan</th>
                    <th class="py-3.5 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm" id="projectTableBody">
                <!-- Baris tabel akan di-generate otomatis oleh AJAX Javascript -->
            </tbody>
        </table>
    </div>

    <!-- Bagian Konversi Data (Fitur Konversi Data) -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
        <div class="text-xs text-slate-500">
            Menampilkan <span id="displayedCount">0</span> dari <span id="totalCount">0</span> proyek web.
        </div>
        <!-- Export Actions (Word, Excel, PDF, JSON, CSV) -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-slate-500"><i class="fas fa-file-export"></i> Konversi & Ekspor:</span>
            <button onclick="exportData('json')" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-bold transition-colors border border-amber-200/50 flex items-center gap-1.5">
                <i class="fas fa-code"></i> JSON
            </button>
            <button onclick="exportData('csv')" class="px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 rounded-lg text-xs font-bold transition-colors border border-teal-200/50 flex items-center gap-1.5">
                <i class="fas fa-file-csv"></i> CSV
            </button>
            <button onclick="exportData('excel')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold transition-colors border border-emerald-200/50 flex items-center gap-1.5">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button onclick="exportData('word')" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold transition-colors border border-blue-200/50 flex items-center gap-1.5">
                <i class="fas fa-file-word"></i> Word
            </button>
            <button onclick="exportData('pdf')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-xs font-bold transition-colors border border-rose-200/50 flex items-center gap-1.5">
                <i class="fas fa-file-pdf"></i> PDF / Cetak
            </button>
        </div>
    </div>
</section>