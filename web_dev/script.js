// DATABASE DAN STATE UTAMA DI FRONTEND (Sinkronisasi dengan API backend)
let projects = [];
let temporaryFiles = []; // Menyimpan file mentah dari browser (File Objects)
let currentSignatureData = ''; // Menyimpan data tanda tangan Base64 sementara
let isEditing = false;
let deleteId = null;

// Jalankan ketika seluruh halaman telah termuat
window.onload = function () {
    // Memuat data dari database via REST API PHP saat aplikasi terbuka jika pengguna sudah login
    const dashboardPage = document.getElementById('dashboardPage');
    if (dashboardPage) {
        fetchProjects();
        initCanvas();
    }
};

// ==========================================
// 1. INTEGRASI DATABASE REST API (AJAX FETCH)
// ==========================================

// Membaca Data (READ) dari MySQL PHP
function fetchProjects() {
    fetch('api.php')
        .then(response => {
            if (!response.ok) throw new Error('Respon server gagal');
            return response.json();
        })
        .then(res => {
            if (res.status === 'success') {
                projects = res.data;
                renderProjects();
            } else {
                showNotification('Gagal memuat data: ' + res.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showNotification('Tidak dapat terhubung ke server/database local.', 'error');
        });
}

// Menangani Submit Form (CREATE dan UPDATE)
function handleProjectSubmit(event) {
    event.preventDefault();
    
    const title = document.getElementById('projectTitle').value.trim();
    const category = document.getElementById('projectCategory').value;
    const team = document.getElementById('projectTeam').value.trim();
    const desc = document.getElementById('projectDesc').value.trim();
    const projId = document.getElementById('projectId').value;

    // Gunakan FormData untuk mengirim data teks, berkas multiple, dan data tanda tangan
    const formData = new FormData();
    formData.append('projectTitle', title);
    formData.append('projectCategory', category);
    formData.append('projectTeam', team);
    formData.append('projectDesc', desc);
    formData.append('projectSignature', currentSignatureData);

    if (isEditing) {
        formData.append('projectId', projId);
    }

    // Lampirkan file yang ada di temporaryFiles ke FormData
    temporaryFiles.forEach(fileObj => {
        // Hanya tambahkan file yang bertipe File mentah dari sistem
        if (fileObj.rawFile) {
            formData.append('projectFiles[]', fileObj.rawFile);
        }
    });

    fetch('api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            showNotification(res.message, 'success');
            closeModal('addEditModal');
            fetchProjects(); // Reload data dari DB
        } else {
            showNotification('Gagal menyimpan data: ' + res.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification('Kesalahan sistem saat menyimpan data ke DB.', 'error');
    });
}

// Menghapus Data (DELETE)
function confirmDelete(id) {
    deleteId = id;
    openModal('deleteModal');
    
    document.getElementById('confirmDeleteBtn').onclick = function() {
        fetch(`api.php?id=${deleteId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                showNotification(res.message, 'success');
                closeModal('deleteModal');
                fetchProjects(); // Muat ulang tabel
            } else {
                showNotification('Gagal menghapus: ' + res.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showNotification('Kesalahan saat mencoba menghapus data.', 'error');
        });
    };
}


// ==========================================
// 2. FRONTEND RENDER & DATATABLE
// ==========================================

function renderProjects(filteredData = null) {
    const dataToRender = filteredData || projects;
    const tableBody = document.getElementById('projectTableBody');
    if (!tableBody) return;
    tableBody.innerHTML = '';

    dataToRender.forEach((proj) => {
        // Generate file badges/chips
        let fileBadges = '';
        if (proj.files && proj.files.length > 0) {
            proj.files.forEach((file, index) => {
                const icon = file.isDoc ? 'fa-file-pdf text-rose-500' : 'fa-file-image text-blue-500';
                fileBadges += `
                    <div class="inline-flex items-center gap-1 bg-slate-100 border border-slate-200 text-slate-700 text-xs px-2 py-0.5 rounded-md mr-1.5 mt-1 hover:bg-indigo-50 hover:border-indigo-200 transition-colors cursor-pointer" 
                         onclick="previewFile('${proj.id}', ${index})">
                        <i class="fas ${icon}"></i> 
                        <span class="max-w-[100px] truncate">${file.name}</span>
                    </div>
                `;
            });
        } else {
            fileBadges = '<span class="text-xs text-slate-400 italic">Belum ada file</span>';
        }

        // Status Tanda Tangan
        const signatureCell = proj.signature 
            ? `<div class="flex justify-center">
                 <img src="${proj.signature}" alt="TTD" class="h-10 w-24 object-contain bg-white border border-slate-100 rounded p-1 shadow-sm cursor-pointer hover:border-indigo-400" onclick="showSignaturePreview('${proj.signature}')" title="Klik untuk lihat detail">
               </div>`
            : `<div class="text-center">
                 <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold text-rose-600 bg-rose-50 rounded-full border border-rose-200">Pending TTD</span>
               </div>`;

        const row = document.createElement('tr');
        row.className = 'hover:bg-slate-50/50 transition-colors';
        row.innerHTML = `
            <td class="py-4 px-4">
                <p class="font-bold text-slate-900 leading-snug">${proj.title}</p>
                <p class="text-xs text-slate-500 line-clamp-1 mt-1">${proj.description}</p>
            </td>
            <td class="py-4 px-4 whitespace-nowrap">
                <span class="px-2.5 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 rounded-lg">${proj.category}</span>
            </td>
            <td class="py-4 px-4 text-xs font-medium text-slate-700">${proj.team}</td>
            <td class="py-4 px-4">${fileBadges}</td>
            <td class="py-4 px-4 align-middle">${signatureCell}</td>
            <td class="py-4 px-4 text-right whitespace-nowrap">
                <div class="inline-flex items-center gap-1.5">
                    <button onclick="editProject('${proj.id}')" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Ubah Proyek">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="confirmDelete('${proj.id}')" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Hapus Proyek">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });

    document.getElementById('displayedCount').innerText = dataToRender.length;
    document.getElementById('totalCount').innerText = projects.length;
    updateStats();
}

function updateStats() {
    const total = projects.length;
    const verified = projects.filter(p => p.signature).length;
    const pending = total - verified;

    const totalEl = document.getElementById('statTotal');
    const verifiedEl = document.getElementById('statVerified');
    const pendingEl = document.getElementById('statPending');

    if (totalEl) totalEl.innerText = total;
    if (verifiedEl) verifiedEl.innerText = verified;
    if (pendingEl) pendingEl.innerText = pending;
}


// ==========================================
// 3. SEACH & FILTER LOGIC
// ==========================================

function handleSearch() {
    const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
    const catFilter = document.getElementById('filterCategory').value;

    const filtered = projects.filter(proj => {
        const matchesKeyword = proj.title.toLowerCase().includes(keyword) || 
                               proj.description.toLowerCase().includes(keyword) || 
                               proj.team.toLowerCase().includes(keyword);
        const matchesCategory = catFilter === "" || proj.category === catFilter;

        return matchesKeyword && matchesCategory;
    });

    renderProjects(filtered);
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterCategory').value = '';
    renderProjects();
    showNotification('Penyaringan proyek di-reset', 'info');
}


// ==========================================
// 4. LOGIN & AUTHENTICATION (Opsi Tampilan Mata)
// ==========================================

function togglePasswordVisibility() {
    const passInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (passInput.type === 'password') {
        passInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}


// ==========================================
// 5. AUDIO & VIDEO CONTROLLER
// ==========================================

const bgAudio = document.getElementById('bgAudio');
function controlAudio(action) {
    if (action === 'play') {
        bgAudio.play().then(() => {
            showNotification('Audio lofi diputar', 'success');
        }).catch(err => {
            showNotification('Aktifkan izin audio di browser!', 'warning');
        });
    } else if (action === 'pause') {
        bgAudio.pause();
        showNotification('Audio di-pause', 'info');
    }
}

function setAudioVolume(val) {
    bgAudio.volume = val;
}

function playCustomIntro() {
    showNotification('Memutar video pengantar proyek...', 'info');
    const video = document.getElementById('tutorialVideo');
    video.currentTime = 0;
    video.play();
}


// ==========================================
// 6. MULTIPLE FILES & PREVIEW
// ==========================================

function handleMultipleFiles(event) {
    const files = event.target.files;
    if (files.length === 0) return;

    if (temporaryFiles.length + files.length > 5) {
        showNotification('Maksimal hanya boleh mengunggah 5 berkas!', 'warning');
        return;
    }

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const isDoc = file.type === 'application/pdf' || file.name.endsWith('.pdf');
        
        const reader = new FileReader();
        reader.onload = function(e) {
            temporaryFiles.push({
                name: file.name,
                size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                data: isDoc ? null : e.target.result,
                isDoc: isDoc,
                rawFile: file // Menyimpan file asli untuk diupload ke PHP
            });
            renderFilePreviews();
        };
        reader.readAsDataURL(file);
    }
}

function removeTemporaryFile(index) {
    temporaryFiles.splice(index, 1);
    renderFilePreviews();
}

function renderFilePreviews() {
    const previewsContainer = document.getElementById('filePreviews');
    if (!previewsContainer) return;
    previewsContainer.innerHTML = '';

    temporaryFiles.forEach((file, index) => {
        const mediaIcon = file.isDoc ? 'fa-file-pdf text-rose-500' : 'fa-file-image text-blue-500';
        
        const previewItem = document.createElement('div');
        previewItem.className = 'flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs';
        previewItem.innerHTML = `
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded bg-white flex items-center justify-center border border-slate-100 shadow-sm text-lg">
                    <i class="fas ${mediaIcon}"></i>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 truncate max-w-[180px]" title="${file.name}">${file.name}</p>
                    <p class="text-[10px] text-slate-400">${file.size}</p>
                </div>
            </div>
            <button type="button" onclick="removeTemporaryFile(${index})" class="text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition-colors">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;
        previewsContainer.appendChild(previewItem);
    });
}

function previewFile(projId, fileIndex) {
    const proj = projects.find(p => p.id == projId);
    if (!proj) return;
    const file = proj.files[fileIndex];

    if (file.isDoc) {
        document.getElementById('previewTitle').innerText = `PDF: ${file.name}`;
        // Di server nyata, kita arahkan ke file path aslinya di folder uploads/
        document.getElementById('previewImage').src = 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=500&auto=format&fit=crop&q=60';
        openModal('previewModal');
    } else {
        document.getElementById('previewTitle').innerText = file.name;
        document.getElementById('previewImage').src = file.data; // URL path file di folder uploads
        openModal('previewModal');
    }
}


// ==========================================
// 7. EDIT PROJECT FILL FORM
// ==========================================

function editProject(id) {
    isEditing = true;
    const proj = projects.find(p => p.id == id);
    if (!proj) return;

    document.getElementById('modalTitle').innerText = "Edit Rincian Proyek";
    document.getElementById('projectId').value = proj.id;
    document.getElementById('projectTitle').value = proj.title;
    document.getElementById('projectCategory').value = proj.category;
    document.getElementById('projectTeam').value = proj.team;
    document.getElementById('projectDesc').value = proj.description;

    // Duplikasi file list untuk dimodifikasi
    temporaryFiles = proj.files.map(f => ({
        name: f.name,
        size: f.size,
        data: f.data,
        isDoc: f.isDoc,
        rawFile: null // File lama sudah ada di server, tidak diupload lagi kecuali diganti baru
    }));
    renderFilePreviews();

    currentSignatureData = proj.signature || '';
    const sigText = document.getElementById('signatureStatusText');
    if (currentSignatureData) {
        sigText.innerHTML = '<span class="text-emerald-600 font-semibold"><i class="fas fa-check-double mr-1.5"></i>Tanda tangan telah terpasang.</span>';
    } else {
        sigText.innerHTML = '<span><i class="fas fa-file-signature text-amber-500 mr-1.5"></i>Belum ada TTD yang disematkan.</span>';
    }

    openModal('addEditModal');
}


// ==========================================
// 8. CANVAS TANDA TANGAN DIGITAL (TTD)
// ==========================================

let canvas, ctx;
let drawing = false;

function initCanvas() {
    canvas = document.getElementById('signatureCanvas');
    if (!canvas) return;
    ctx = canvas.getContext('2d');

    // Mengatur gaya goresan tanda tangan agar tebal dan presisi
    ctx.strokeStyle = '#312e81'; // Warna Navy Gelap
    ctx.lineWidth = 3.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    // Mencegah browser melakukan scroll/gesture otomatis saat menggambar di layar sentuh
    canvas.style.touchAction = 'none';

    // Menggunakan Pointer Events untuk menyatukan deteksi Mouse, Touchscreen, dan Pen/Stylus
    canvas.addEventListener('pointerdown', (e) => {
        drawing = true;
        // Mengunci pointer agar koordinat menggambar tetap terekam meski pointer keluar dari area canvas
        canvas.setPointerCapture(e.pointerId);
        const pos = getCoordinates(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    });

    canvas.addEventListener('pointermove', (e) => {
        if (!drawing) return;
        const pos = getCoordinates(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    });

    canvas.addEventListener('pointerup', (e) => {
        if (drawing) {
            canvas.releasePointerCapture(e.pointerId);
            drawing = false;
        }
    });

    canvas.addEventListener('pointercancel', (e) => {
        if (drawing) {
            canvas.releasePointerCapture(e.pointerId);
            drawing = false;
        }
    });
}

function getCoordinates(event) {
    const rect = canvas.getBoundingClientRect();
    return {
        x: (event.clientX - rect.left) * (canvas.width / rect.width),
        y: (event.clientY - rect.top) * (canvas.height / rect.height)
    };
}

function clearCanvas() {
    if (ctx) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        // Memastikan gaya goresan dikonfigurasi ulang setelah canvas dibersihkan
        ctx.strokeStyle = '#312e81';
        ctx.lineWidth = 3.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
    }
}

function openSignatureModal() {
    openModal('signatureModal');
    setTimeout(() => {
        clearCanvas();
    }, 100);
}

function saveSignature() {
    const blankCanvas = document.createElement('canvas');
    blankCanvas.width = canvas.width;
    blankCanvas.height = canvas.height;
    
    if (canvas.toDataURL() === blankCanvas.toDataURL()) {
        showNotification('Canvas kosong! Silakan berikan tanda tangan dahulu.', 'warning');
        return;
    }

    currentSignatureData = canvas.toDataURL('image/png');
    document.getElementById('signatureStatusText').innerHTML = '<span class="text-emerald-600 font-semibold"><i class="fas fa-check-double mr-1.5"></i>Tanda tangan baru berhasil digambar.</span>';
    
    closeModal('signatureModal');
    showNotification('Tanda tangan digital berhasil direkam!', 'success');
}

function showSignaturePreview(sigSrc) {
    document.getElementById('previewTitle').innerText = "Tanda Tangan Terverifikasi";
    document.getElementById('previewImage').src = sigSrc;
    openModal('previewModal');
}


// ==========================================
// 9. CONVERT & EXPORT ACTIONS (Word, Excel, dll)
// ==========================================

function exportData(format) {
    if (projects.length === 0) {
        showNotification('Tidak ada data proyek untuk dikonversi!', 'warning');
        return;
    }

    const sanitizedData = projects.map(proj => ({
        ID: proj.id,
        Judul_Proyek: proj.title,
        Kategori: proj.category,
        Anggota_Kelompok: proj.team,
        Deskripsi: proj.description,
        Total_Files: proj.files.length,
        Status_TTD: proj.signature ? "Telah Ditandatangani" : "Belum Ditandatangani"
    }));

    if (format === 'json') {
        const jsonString = JSON.stringify(sanitizedData, null, 2);
        downloadFile(jsonString, 'webdev_projects_export.json', 'application/json');
        showNotification('Berhasil mengekspor data ke format JSON!', 'success');
    } 
    else if (format === 'csv') {
        const headers = Object.keys(sanitizedData[0]).join(',');
        const rows = sanitizedData.map(row => 
            Object.values(row).map(value => `"${value.toString().replace(/"/g, '""')}"`).join(',')
        );
        const csvContent = [headers, ...rows].join('\n');
        downloadFile(csvContent, 'webdev_projects_export.csv', 'text/csv');
        showNotification('Berhasil mengekspor data ke format CSV!', 'success');
    } 
    else if (format === 'excel') {
        let excelContent = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">`;
        excelContent += `<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Portofolio Proyek</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><style>table {border-collapse:collapse;} th,td {border:1px solid #cbd5e1; padding:8px; font-family:sans-serif; font-size:12px;} th {background-color:#e2e8f0; font-weight:bold; color: #0f172a;}</style></head><body>`;
        excelContent += `<h2>LAPORAN EVALUASI PROYEK AKHIR MAHASISWA - WEBDEV HUB</h2>`;
        excelContent += `<p>Tanggal Ekspor: ${new Date().toLocaleDateString('id-ID')}</p>`;
        excelContent += `<table><thead><tr><th>ID Proyek</th><th>Judul Proyek Web</th><th>Kategori</th><th>Anggota Kelompok</th><th>Deskripsi Singkat</th><th>Jumlah Berkas</th><th>Status Tanda Tangan</th></tr></thead><tbody>`;
        
        projects.forEach(p => {
            excelContent += `<tr><td>${p.id}</td><td><b>${p.title}</b></td><td>${p.category}</td><td>${p.team}</td><td>${p.description}</td><td style="text-align:center;">${p.files.length}</td><td>${p.signature ? 'TERVERIFIKASI TTD' : 'PENDING TTD'}</td></tr>`;
        });
        
        excelContent += `</tbody></table></body></html>`;
        downloadFile(excelContent, 'webdev_projects_export.xls', 'application/vnd.ms-excel');
        showNotification('Berhasil mengonversi dan mengunduh format Excel!', 'success');
    } 
    else if (format === 'word') {
        let wordContent = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">`;
        wordContent += `<head><meta charset="utf-8"><style>body {font-family: 'Arial', sans-serif;} h2 {color: #1e1b4b; border-bottom: 2px solid #4f46e5; padding-bottom: 5px; font-size: 20px;} table {border-collapse:collapse; width:100%; margin-top:20px;} th,td {border:1px solid #cbd5e1; padding:10px; font-size:12px;} th {background-color:#f8fafc; font-weight:bold; color:#1e293b;}</style></head><body>`;
        wordContent += `<h2>BERKAS LAPORAN RESMI PORTOFOLIO WEBDEV HUB</h2>`;
        wordContent += `<p>Diproduksi Secara Otomatis pada: <b>${new Date().toLocaleDateString('id-ID')}</b></p>`;
        wordContent += `<table><thead><tr><th>ID</th><th>Judul Proyek</th><th>Kategori</th><th>Anggota Tim</th><th>Spesifikasi Proyek</th><th>Status Verifikasi TTD</th></tr></thead><tbody>`;
        
        projects.forEach(p => {
            wordContent += `<tr><td>${p.id}</td><td><b>${p.title}</b></td><td>${p.category}</td><td>${p.team}</td><td>${p.description}</td><td><b>${p.signature ? 'SUDAH TTD' : 'BELUM TTD'}</b></td></tr>`;
        });
        
        wordContent += `</tbody></table></body></html>`;
        downloadFile(wordContent, 'webdev_projects_export.doc', 'application/msword');
        showNotification('Berhasil mengonversi dan mengunduh format Word!', 'success');
    } 
    else if (format === 'pdf') {
        const printWindow = window.open('', '_blank');
        let pdfHTML = `
        <html>
        <head>
            <title>Cetak Laporan - WebDev Hub</title>
            <script src="https://cdn.tailwindcss.com"><\/script>
            <style>
                @media print { .no-print { display: none; } }
            </style>
        </head>
        <body class="bg-slate-50 p-8 text-slate-800">
            <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                <div class="flex justify-between items-center border-b-2 border-indigo-600 pb-6 mb-6">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight">WEBDEV HUB PORTAL</h1>
                        <p class="text-indigo-600 font-semibold text-sm">Laporan Hasil Portofolio Proyek Akhir Pemrograman Web</p>
                    </div>
                </div>
                <table class="w-full text-left border-collapse border border-slate-200 rounded-lg overflow-hidden mb-8">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-[10px] font-bold uppercase border-b border-slate-200">
                            <th class="p-3">ID</th>
                            <th class="p-3">Judul Proyek</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Kelompok</th>
                            <th class="p-3 text-center">Tanda Tangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100">
        `;
        
        projects.forEach(p => {
            const sigImg = p.signature ? `<img src="${p.signature}" class="h-10 w-24 object-contain mx-auto" />` : '<span class="text-rose-500 font-bold">PENDING</span>';
            pdfHTML += `
                <tr>
                    <td class="p-3">${p.id}</td>
                    <td class="p-3"><b>${p.title}</b><p class="text-slate-500 mt-1">${p.description}</p></td>
                    <td class="p-3">${p.category}</td>
                    <td class="p-3">${p.team}</td>
                    <td class="p-3 text-center">${sigImg}</td>
                </tr>
            `;
        });
        
        pdfHTML += `
                    </tbody>
                </table>
                <div class="flex justify-between items-center text-[10px] text-slate-400 border-t border-slate-100 pt-6">
                    <p>WebDev Hub Portal Akademik © 2026.</p>
                    <button onclick="window.print()" class="no-print px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg shadow-md">Cetak / Simpan PDF</button>
                </div>
            </div>
        </body>
        </html>
        `;
        
        printWindow.document.write(pdfHTML);
        printWindow.document.close();
    }
}

function downloadFile(content, fileName, contentType) {
    const blob = new Blob([content], { type: contentType });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = fileName;
    a.click();
    URL.revokeObjectURL(url);
}


// ==========================================
// 10. MODAL MANAGER & SYSTEM TOAST
// ==========================================

function showNotification(message, type = 'info') {
    const toast = document.getElementById('toast');
    const toastIcon = document.getElementById('toastIcon');
    const toastMessage = document.getElementById('toastMessage');

    if (!toast) return;
    toastMessage.innerText = message;
    
    if (type === 'success') {
        toastIcon.className = "inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-emerald-500 bg-emerald-100 rounded-lg";
        toastIcon.innerHTML = `<i class="fas fa-check-circle"></i>`;
    } else if (type === 'error') {
        toastIcon.className = "inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-rose-500 bg-rose-100 rounded-lg";
        toastIcon.innerHTML = `<i class="fas fa-times-circle"></i>`;
    } else if (type === 'warning') {
        toastIcon.className = "inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-amber-500 bg-amber-100 rounded-lg";
        toastIcon.innerHTML = `<i class="fas fa-exclamation-circle"></i>`;
    } else {
        toastIcon.className = "inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-blue-500 bg-blue-100 rounded-lg";
        toastIcon.innerHTML = `<i class="fas fa-info-circle"></i>`;
    }

    toast.classList.remove('translate-x-full');
    toast.classList.add('translate-x-0');

    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
    }, 3500);
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    
    if (modalId === 'addEditModal' && !isEditing) {
        document.getElementById('modalTitle').innerText = "Tambah Proyek Baru";
        document.getElementById('projectForm').reset();
        document.getElementById('projectId').value = '';
        temporaryFiles = [];
        currentSignatureData = '';
        renderFilePreviews();
        document.getElementById('signatureStatusText').innerHTML = '<span><i class="fas fa-file-signature text-amber-500 mr-1.5"></i>Belum ada TTD yang disematkan.</span>';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('hidden');
    if (modalId === 'addEditModal') {
        isEditing = false;
    }
}