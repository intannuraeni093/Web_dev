<!-- ============================================= -->
<!-- MODAL - MODAL APLIKASI (Fitur Modal Terpisah) -->
<!-- ============================================= -->

<!-- 1. MODAL TAMBAH & EDIT PROYEK (Include Fitur Multiple File Upload) -->
<div id="addEditModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl shadow-2xl border border-slate-100 flex flex-col max-h-[90vh] transform scale-95 transition-transform duration-300" id="addEditModalContent">
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50 rounded-t-2xl">
            <h3 class="text-lg font-bold text-slate-900" id="modalTitle">Tambah Proyek Baru</h3>
            <button onclick="closeModal('addEditModal')" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-lg transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- Modal Body (Scrollable) -->
        <div class="p-6 overflow-y-auto space-y-4 flex-grow">
            <form id="projectForm" onsubmit="handleProjectSubmit(event)">
                <input type="hidden" id="projectId">
                
                <!-- Judul Proyek -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Judul Proyek Web</label>
                    <input type="text" id="projectTitle" name="projectTitle" required
                        class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                        placeholder="Contoh: E-Commerce Toko Baju Lokal">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Kategori Aplikasi</label>
                        <select id="projectCategory" name="projectCategory" required
                            class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                            <option value="E-Commerce">E-Commerce</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                            <option value="Media Sosial">Media Sosial</option>
                            <option value="Game Web">Game Web</option>
                            <option value="E-Learning">E-Learning</option>
                        </select>
                    </div>
                    <!-- Anggota Kelompok -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Kelompok (Mhs)</label>
                        <input type="text" id="projectTeam" name="projectTeam" required
                            class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                            placeholder="Contoh: Budi, Andi, Susi">
                    </div>
                </div>

                <!-- Deskripsi Proyek -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Deskripsi Singkat Fitur</label>
                    <textarea id="projectDesc" name="projectDesc" rows="3" required
                        class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                        placeholder="Jelaskan fitur-fitur penting yang ada pada proyek web yang Anda ajukan..."></textarea>
                </div>

                <!-- MULTIPLE FILE UPLOAD (Fitur 1 - Multiple File) -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Upload Berkas Pendukung (Multiple)</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center bg-slate-50 hover:bg-slate-100/50 transition-colors cursor-pointer relative" id="dropzone" onclick="document.getElementById('projectFiles').click()">
                        <input type="file" id="projectFiles" name="projectFiles[]" multiple onchange="handleMultipleFiles(event)" class="hidden">
                        <i class="fas fa-cloud-upload-alt text-3xl text-indigo-500 mb-2"></i>
                        <p class="text-xs font-semibold text-slate-700">Pilih berkas Anda atau seret ke sini</p>
                        <p class="text-[10px] text-slate-400 mt-1">Bisa upload dokumen PDF, gambar mock-up, dll. (Max: 5 berkas)</p>
                    </div>
                    <!-- Preview Berkas yang dipilih -->
                    <div id="filePreviews" class="grid grid-cols-1 gap-2 mt-3">
                        <!-- List preview file akan tampil di sini -->
                    </div>
                </div>

                <!-- Tanda Tangan Simpan status -->
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between text-xs text-slate-600">
                    <span id="signatureStatusText"><i class="fas fa-file-signature text-amber-500 mr-1.5"></i> Belum ada TTD yang disematkan.</span>
                    <button type="button" onclick="openSignatureModal()" class="text-indigo-600 font-bold hover:underline">Sematkan TTD Digital</button>
                </div>

                <!-- Tombol Form Action -->
                <div class="mt-6 flex justify-end gap-3 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeModal('addEditModal')" class="px-4 py-2 border border-slate-200 text-slate-600 font-semibold rounded-xl text-xs hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs shadow transition-colors">Simpan Data Proyek</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. MODAL CANVAS TANDA TANGAN DIGITAL (Fitur Canvas TTD) -->
<div id="signatureModal" class="fixed inset-0 z-[60] hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100 flex flex-col" id="signatureModalContent">
        <!-- Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50 rounded-t-2xl">
            <h3 class="text-md font-bold text-slate-900"><i class="fas fa-pen-fancy text-indigo-600 mr-2"></i>Gambar Tanda Tangan</h3>
            <button onclick="closeModal('signatureModal')" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-lg transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- Body Canvas -->
        <div class="p-6 space-y-4">
            <p class="text-xs text-slate-500">Gunakan mouse atau layar sentuh untuk menggambar tanda tangan Anda pada bidang canvas di bawah ini sebagai lembar verifikasi proyek.</p>
            
            <div class="border border-slate-300 rounded-xl overflow-hidden bg-slate-50 shadow-inner relative">
                <canvas id="signatureCanvas" class="w-full h-44 cursor-crosshair block touch-none" width="380" height="176"></canvas>
            </div>

            <div class="flex justify-between items-center text-xs text-slate-500">
                <span>Goresan warna: <strong class="text-indigo-600">Biru Gelap</strong></span>
                <button type="button" onclick="clearCanvas()" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded font-semibold transition-colors">
                    <i class="fas fa-eraser mr-1"></i> Bersihkan Canvas
                </button>
            </div>
        </div>
        <!-- Footer -->
        <div class="p-5 border-t border-slate-100 flex justify-end gap-3 bg-slate-50 rounded-b-2xl">
            <button type="button" onclick="closeModal('signatureModal')" class="px-4 py-2 border border-slate-200 text-slate-600 font-semibold rounded-xl text-xs hover:bg-slate-50 transition-colors">Kembali</button>
            <button type="button" onclick="saveSignature()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs shadow transition-colors">Terapkan Tanda Tangan</button>
        </div>
    </div>
</div>

<!-- 3. MODAL CONFIRMATION HAPUS -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl border border-slate-100 p-6 text-center">
        <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center text-rose-500 text-2xl mx-auto mb-4">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus Proyek Ini?</h3>
        <p class="text-xs text-slate-500 mb-6">Apakah Anda yakin ingin menghapus data proyek ini secara permanen dari server dan database? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-center gap-3">
            <button onclick="closeModal('deleteModal')" class="px-4 py-2 border border-slate-200 text-slate-600 font-semibold rounded-xl text-xs hover:bg-slate-50 transition-colors">Batal</button>
            <button id="confirmDeleteBtn" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-xs shadow transition-colors">Ya, Hapus Data</button>
        </div>
    </div>
</div>

<!-- 4. MODAL PREVIEW IMAGE/TTD DETAIL -->
<div id="previewModal" class="fixed inset-0 z-[70] hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden relative">
        <button onclick="closeModal('previewModal')" class="absolute top-3 right-3 p-1.5 bg-slate-900/20 hover:bg-slate-900/40 text-white rounded-lg transition-colors z-10">
            <i class="fas fa-times"></i>
        </button>
        <div class="p-6 text-center">
            <h4 class="text-sm font-bold text-slate-800 mb-3" id="previewTitle">Preview Elemen</h4>
            <div class="border border-slate-100 rounded-xl bg-slate-50 p-4 flex items-center justify-center min-h-[200px]">
                <img id="previewImage" src="" alt="Pratinjau" class="max-h-[300px] object-contain rounded-lg">
            </div>
        </div>
    </div>
</div>