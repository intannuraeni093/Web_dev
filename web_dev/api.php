<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'config.php';

// Buat direktori unggah berkas jika belum ada
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$method = $_SERVER['REQUEST_METHOD'];

// Handle GET Request (Membaca Data / READ)
if ($method === 'GET') {
    try {
        // Ambil semua proyek
        $stmt = $pdo->query("SELECT * FROM projects ORDER BY id DESC");
        $projects = $stmt->fetchAll();
        
        // Ambil file pendukung untuk masing-masing proyek
        foreach ($projects as &$proj) {
            $file_stmt = $pdo->prepare("SELECT file_name AS name, file_size AS size, file_path AS data, is_doc AS isDoc FROM project_files WHERE project_id = ?");
            $file_stmt->execute([$proj['id']]);
            $proj['files'] = $file_stmt->fetchAll();
            
            // Konversi isDoc dari database (0/1) ke boolean JS
            foreach ($proj['files'] as &$file) {
                $file['isDoc'] = (bool)$file['isDoc'];
            }
        }
        
        echo json_encode(['status' => 'success', 'data' => $projects]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// Handle POST Request (Tambah Data Baru / CREATE atau UPDATE)
elseif ($method === 'POST') {
    // Mengecek apakah ini request untuk UPDATE data
    $is_update = isset($_POST['projectId']) && !empty($_POST['projectId']);
    
    try {
        $title = $_POST['projectTitle'] ?? '';
        $category = $_POST['projectCategory'] ?? '';
        $team = $_POST['projectTeam'] ?? '';
        $desc = $_POST['projectDesc'] ?? '';
        $signature = $_POST['projectSignature'] ?? ''; // Mengambil data tanda tangan base64
        
        if (empty($title) || empty($category) || empty($team)) {
            throw new Exception("Kolom wajib (Judul, Kategori, Kelompok) tidak boleh kosong.");
        }
        
        $pdo->beginTransaction();
        
        if ($is_update) {
            // PROSES UPDATE
            $id = $_POST['projectId'];
            
            // Update data proyek dasar
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, category = ?, team = ?, description = ?, signature = ? WHERE id = ?");
            $stmt->execute([$title, $category, $team, $desc, $signature, $id]);
            
            // Hapus file lama jika ada penggantian berkas baru
            if (isset($_FILES['projectFiles']) && !empty($_FILES['projectFiles']['name'][0])) {
                // Ambil info file lama dari DB untuk dihapus dari server fisik
                $old_files_stmt = $pdo->prepare("SELECT file_path FROM project_files WHERE project_id = ?");
                $old_files_stmt->execute([$id]);
                $old_files = $old_files_stmt->fetchAll();
                foreach($old_files as $of) {
                    if (file_exists($of['file_path'])) {
                        unlink($of['file_path']);
                    }
                }
                
                // Bersihkan relasi file lama di DB
                $delete_files_stmt = $pdo->prepare("DELETE FROM project_files WHERE project_id = ?");
                $delete_files_stmt->execute([$id]);
            }
            
            $project_id = $id;
        } else {
            // PROSES INSERT BARU
            $stmt = $pdo->prepare("INSERT INTO projects (title, category, team, description, signature) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $category, $team, $desc, $signature]);
            $project_id = $pdo->lastInsertId();
        }
        
        // Proses Upload Multiple Files jika ada file yang dikirim
        if (isset($_FILES['projectFiles'])) {
            $files = $_FILES['projectFiles'];
            $file_count = count($files['name']);
            
            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $tmp_name = $files['tmp_name'][$i];
                    $original_name = basename($files['name'][$i]);
                    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
                    
                    // Amankan nama file untuk menghindari duplikasi
                    $secure_name = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $original_name);
                    $dest_path = $upload_dir . $secure_name;
                    
                    if (move_uploaded_file($tmp_name, $dest_path)) {
                        $size_mb = number_format(($files['size'][$i] / 1024 / 1024), 2) . ' MB';
                        $is_doc = (strtolower($extension) === 'pdf') ? 1 : 0;
                        
                        $file_stmt = $pdo->prepare("INSERT INTO project_files (project_id, file_name, file_size, file_path, is_doc) VALUES (?, ?, ?, ?, ?)");
                        $file_stmt->execute([$project_id, $original_name, $size_mb, $dest_path, $is_doc]);
                    }
                }
            }
        }
        
        $pdo->commit();
        echo json_encode([
            'status' => 'success', 
            'message' => $is_update ? 'Proyek berhasil diperbarui.' : 'Proyek berhasil ditambahkan.',
            'project_id' => $project_id
        ]);
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// Handle DELETE Request (Hapus Data / DELETE)
elseif ($method === 'DELETE' || (isset($_GET['action']) && $_GET['action'] === 'delete')) {
    try {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            // Cek jika ID dikirim lewat body raw JSON
            $data = json_decode(file_get_contents("php://input"));
            $id = $data->id ?? null;
        }
        
        if (!$id) {
            throw new Exception("ID Proyek tidak valid untuk dihapus.");
        }
        
        // Ambil info berkas fisik untuk dihapus sebelum menghapus relasi DB
        $file_stmt = $pdo->prepare("SELECT file_path FROM project_files WHERE project_id = ?");
        $file_stmt->execute([$id]);
        $files = $file_stmt->fetchAll();
        foreach($files as $f) {
            if (file_exists($f['file_path'])) {
                unlink($f['file_path']);
            }
        }
        
        // Menghapus data proyek (Secara otomatis menghapus baris berkas di DB karena ON DELETE CASCADE)
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['status' => 'success', 'message' => 'Proyek beserta file pendukungnya berhasil dihapus.']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>