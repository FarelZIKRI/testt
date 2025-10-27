<?php
$message = '';
$success = false;

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $semester = $_POST['semester'];
    $ipk = floatval($_POST['ipk']);
    $jenis_beasiswa_id = $_POST['jenis_beasiswa'];
    
    $errors = [];
    
    // Validasi server-side
    if (empty($nama)) {
        $errors[] = 'Nama harus diisi';
    }
    
    if (empty($email)) {
        $errors[] = 'Email harus diisi';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Format email tidak valid';
    }
    
    if (empty($no_hp)) {
        $errors[] = 'Nomor HP harus diisi';
    } elseif (!validatePhone($no_hp)) {
        $errors[] = 'Nomor HP hanya boleh berisi angka';
    }
    
    if (empty($semester)) {
        $errors[] = 'Semester harus dipilih';
    }
    
    if ($ipk < 3.0) {
        $errors[] = 'IPK tidak memenuhi syarat minimum (3.0)';
    }
    
    if (empty($jenis_beasiswa_id)) {
        $errors[] = 'Jenis beasiswa harus dipilih';
    }
    
    // Validasi file upload
    $berkas_filename = '';
    if (!isset($_FILES['berkas_syarat']) || $_FILES['berkas_syarat']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Berkas syarat harus diupload';
    } else {
        // Cek error upload
        if ($_FILES['berkas_syarat']['error'] !== UPLOAD_ERR_OK) {
            switch ($_FILES['berkas_syarat']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errors[] = 'Ukuran file terlalu besar (maksimal 5MB)';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errors[] = 'File hanya terupload sebagian, silakan coba lagi';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errors[] = 'Folder temporary tidak ditemukan';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errors[] = 'Gagal menulis file ke disk';
                    break;
                default:
                    $errors[] = 'Terjadi kesalahan saat upload file';
            }
        } else {
            $berkas_filename = uploadFile($_FILES['berkas_syarat']);
            if (!$berkas_filename) {
                $errors[] = 'Gagal mengupload berkas. Pastikan format file benar (PDF, JPG, PNG, ZIP), ukuran maksimal 5MB, dan folder uploads dapat ditulis';
            }
        }
    }
    
    // Jika tidak ada error, simpan data
    if (empty($errors)) {
        $data = [
            'nama' => $nama,
            'email' => $email,
            'no_hp' => $no_hp,
            'semester' => $semester,
            'ipk' => $ipk,
            'jenis_beasiswa_id' => $jenis_beasiswa_id,
            'berkas_syarat' => $berkas_filename
        ];
        
        if (savePendaftaran($data)) {
            $success = true;
            $message = 'Pendaftaran beasiswa berhasil disimpan! Status ajuan: "Belum di verifikasi"';
        } else {
            $message = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
        }
    } else {
        $message = 'Terdapat kesalahan dalam pengisian form:<br>• ' . implode('<br>• ', $errors);
    }
}

// Ambil data beasiswa
$beasiswa_list = getAllBeasiswa();
?>

<h2>Form Pendaftaran Beasiswa</h2>

<?php if ($message): ?>
    <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?>">
        <?= $message ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="text-align: center; margin: 2rem 0;">
        <a href="?page=hasil" class="btn btn-success">Lihat Hasil Pendaftaran</a>
        <a href="?page=daftar" class="btn">Daftar Lagi</a>
    </div>
<?php else: ?>

<form id="beasiswa-form" method="POST" enctype="multipart/form-data" onsubmit="return confirmSubmit()">
    <div id="form-errors"></div>
    
    <div class="form-group">
        <label for="nama">Nama Lengkap *</label>
        <input type="text" id="nama" name="nama" required 
               value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">
    </div>
    
    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required 
               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        <div id="email-feedback"></div>
    </div>
    
    <div class="form-group">
        <label for="no_hp">Nomor HP *</label>
        <input type="tel" id="no_hp" name="no_hp" required placeholder="Contoh: 081234567890"
               value="<?= isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : '' ?>">
        <div id="phone-feedback"></div>
    </div>
    
    <div class="form-group">
        <label for="semester">Semester Saat Ini *</label>
        <select id="semester" name="semester" required>
            <option value="">Pilih Semester</option>
            <?php for ($i = 1; $i <= 8; $i++): ?>
                <option value="<?= $i ?>" <?= (isset($_POST['semester']) && $_POST['semester'] == $i) ? 'selected' : '' ?>>
                    Semester <?= $i ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label>IPK Saat Ini</label>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="padding: 0.75rem; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; font-weight: bold; min-width: 80px;">
                <span id="ipk-display">-</span>
            </div>
            <button type="button" id="generate-ipk" class="btn" style="padding: 0.5rem 1rem;">
                Generate IPK Otomatis
            </button>
        </div>
        <small style="color: #666; margin-top: 0.25rem; display: block;">
            Sistem akan generate IPK random antara 2.60 - 4.00
        </small>
        <input type="hidden" id="ipk" name="ipk" value="">
        <div id="ipk-message" style="margin-top: 0.5rem;"></div>
    </div>
    
    <div class="form-group">
        <label for="jenis_beasiswa">Pilihan Beasiswa *</label>
        <select id="jenis_beasiswa" name="jenis_beasiswa" required disabled>
            <option value="">Pilih Jenis Beasiswa</option>
            <?php foreach ($beasiswa_list as $beasiswa): ?>
                <option value="<?= $beasiswa['id'] ?>" <?= (isset($_POST['jenis_beasiswa']) && $_POST['jenis_beasiswa'] == $beasiswa['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($beasiswa['nama_beasiswa']) ?> (IPK Min: <?= $beasiswa['syarat_ipk'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="berkas_syarat">Upload Berkas Syarat *</label>
        <input type="file" id="berkas_syarat" name="berkas_syarat" 
               accept=".pdf,.jpg,.jpeg,.png,.zip" required disabled>
        <small style="color: #666;">Format yang diizinkan: PDF, JPG, PNG, ZIP (Maksimal 5MB)</small>
        <div id="file-preview" style="margin-top: 0.5rem;"></div>
    </div>
    
    <div class="form-group" style="text-align: center; margin-top: 2rem;">
        <button type="submit" id="submit-btn" class="btn btn-success" disabled>
            Daftar Beasiswa
        </button>
        <a href="?page=home" class="btn" style="margin-left: 1rem;">Kembali</a>
    </div>
</form>

<?php endif; ?>

<div class="card" style="margin-top: 2rem;">
    <h3>Petunjuk Pengisian Form</h3>
    <ol>
        <li><strong>Nama Lengkap:</strong> Isi dengan nama lengkap sesuai KTP/KTM</li>
        <li><strong>Email:</strong> Gunakan email aktif yang dapat dihubungi</li>
        <li><strong>Nomor HP:</strong> Masukkan nomor HP aktif (hanya angka)</li>
        <li><strong>Semester:</strong> Pilih semester saat ini (1-8)</li>
        <li><strong>IPK:</strong> Klik tombol untuk generate IPK otomatis (2.60 - 4.00) dari sistem</li>
        <li><strong>Jenis Beasiswa:</strong> Pilih sesuai minat dan syarat IPK</li>
        <li><strong>Berkas Syarat:</strong> Upload dokumen pendukung</li>
    </ol>
    
    <div class="alert alert-warning" style="margin-top: 1rem;">
        <strong>Perhatian:</strong> Pastikan semua data yang diisi sudah benar karena data tidak dapat diubah setelah disimpan.
    </div>
</div>