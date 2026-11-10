<!-- FOOTER SELESAI -->
    <footer class="bg-white border-t border-slate-100 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
            <p>&copy; 2026 WebDev Hub - Proyek Akhir Mata Kuliah Pemrograman Web.</p>
            <p class="mt-1">Intan Nuraeni</p>
        </div>
    </footer>

  <!-- MODAL POP-UP KONFIRMASI LOGOUT -->
    <div id="logoutConfirmModal" class="fixed inset-0 z-[100] hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl border border-slate-100 p-6 text-center transform scale-95 transition-transform duration-300">
            <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center text-amber-500 text-2xl mx-auto mb-4">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Konfirmasi Keluar</h3>
            <p class="text-xs text-slate-500 mb-6">Apakah Anda yakin ingin keluar dari sistem WebDev Hub? Sesi pengerjaan Anda akan diakhiri aman.</p>
            <div class="flex justify-center gap-3">
                <button onclick="closeLogoutModal()" class="px-4 py-2 border border-slate-200 text-slate-600 font-semibold rounded-xl text-xs hover:bg-slate-50 transition-colors">Batal</button>
                <!-- Menambahkan ID "confirmLogoutBtn" pada tombol Ya, Keluar -->
                <a href="index.php?action=logout" id="confirmLogoutBtn" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-xs shadow-lg shadow-rose-600/20 transition-colors inline-block align-middle">Ya, Keluar</a>
            </div>
        </div>
    </div>

    <!-- Hubungkan ke JS -->
    <script src="script.js"></script>

<!-- Script Interseptor Aksi Logout -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mencari semua tautan logout KECUALI tombol konfirmasi di dalam modal (:not(#confirmLogoutBtn))
            const logoutLinks = document.querySelectorAll('a[href="index.php?action=logout"]:not(#confirmLogoutBtn)');
            logoutLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault(); // Mencegah redirect langsung ke PHP logout
                    openLogoutModal(); // Tampilkan pop-up kustom
                });
            });
        });

        function openLogoutModal() {
            const modal = document.getElementById('logoutConfirmModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutConfirmModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    </script>