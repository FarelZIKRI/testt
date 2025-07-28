<?php
require_once '../includes/admin_functions.php';
requireAdminLogin();

$message = '';
$success = false;

// Proses tambah beasiswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_beasiswa'])) {
    $nama = trim($_POST['nama_beasiswa']);
    $syarat_ipk = floatval($_POST['syarat_ipk']);
    $deskripsi = trim($_POST['deskripsi']);
    
    if (empty($nama) || $syarat_ipk <= 0) {
        $message = 'Nama beasiswa dan syarat IPK harus diisi dengan benar';
    } else {
        if (addJenisBeasiswa($nama, $syarat_ipk, $deskripsi)) {
            $message = 'Jenis beasiswa berhasil ditambahkan';
            $success = true;
        } else {
            $message = 'Gagal menambahkan jenis beasiswa';
        }
    }
}

// Proses update beasiswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_beasiswa'])) {
    $id = $_POST['id'];
    $nama = trim($_POST['nama_beasiswa']);
    $syarat_ipk = floatval($_POST['syarat_ipk']);
    $deskripsi = trim($_POST['deskripsi']);
    
    if (empty($nama) || $syarat_ipk <= 0) {
        $message = 'Nama beasiswa dan syarat IPK harus diisi dengan benar';
    } else {
        if (updateJenisBeasiswa($id, $nama, $syarat_ipk, $deskripsi)) {
            $message = 'Jenis beasiswa berhasil diupdate';
            $success = true;
        } else {
            $message = 'Gagal mengupdate jenis beasiswa';
        }
    }
}

// Proses hapus beasiswa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_beasiswa'])) {
    $id = $_POST['id'];
    
    if (deleteJenisBeasiswa($id)) {
        $message = 'Jenis beasiswa berhasil dihapus';
        $success = true;
    } else {
        $message = 'Gagal menghapus jenis beasiswa. Pastikan tidak ada pendaftaran yang menggunakan jenis beasiswa ini.';
    }
}

$beasiswa_list = getAllBeasiswa();
$edit_data = null;

// Ambil data untuk edit
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_data = getBeasiswaById($edit_id);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Beasiswa - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1>🎯 Kelola Jenis Beasiswa</h1>
            <p>Tambah, edit, dan hapus jenis beasiswa</p>
        </div>
    </header>

    <nav class="admin-nav">
        <div class="container">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="pendaftaran.php">Kelola Pendaftaran</a></li>
                <li><a href="beasiswa.php" class="active">Kelola Beasiswa</a></li>
                <li><a href="laporan.php">Laporan</a></li>
                <li><a href="../index.php" target="_blank">Lihat Website</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <!-- Form Tambah/Edit Beasiswa -->
            <div class="card">
                <h3><?= $edit_data ? 'Edit Jenis Beasiswa' : 'Tambah Jenis Beasiswa' ?></h3>
                
                <form method="POST">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="nama_beasiswa">Nama Beasiswa *</label>
                        <input type="text" id="nama_beasiswa" name="nama_beasiswa" required
                               value="<?= $edit_data ? htmlspecialchars($edit_data['nama_beasiswa']) : '' ?>"
                               placeholder="Contoh: Beasiswa Akademik">
                    </div>
                    
                    <div class="form-group">
                        <label for="syarat_ipk">Syarat IPK Minimal *</label>
                        <input type="number" id="syarat_ipk" name="syarat_ipk" 
                               min="0" max="4" step="0.01" required
                               value="<?= $edit_data ? $edit_data['syarat_ipk'] : '' ?>"
                               placeholder="Contoh: 3.50">
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4"
                                  placeholder="Deskripsi jenis beasiswa..."><?= $edit_data ? htmlspecialchars($edit_data['deskripsi']) : '' ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <?php if ($edit_data): ?>
                            <button type="submit" name="update_beasiswa" class="btn btn-success">
                                Update Beasiswa
                            </button>
                            <a href="beasiswa.php" class="btn" style="margin-left: 0.5rem;">
                                Batal
                            </a>
                        <?php else: ?>
                            <button type="submit" name="add_beasiswa" class="btn btn-success">
                                Tambah Beasiswa
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Daftar Beasiswa -->
            <div class="card">
                <h3>Daftar Jenis Beasiswa (<?= count($beasiswa_list) ?> jenis)</h3>
                
                <?php if (empty($beasiswa_list)): ?>
                    <div class="alert alert-info">
                        Belum ada jenis beasiswa. Tambahkan jenis beasiswa pertama Anda.
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Beasiswa</th>
                                <th>IPK Min</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($beasiswa_list as $index => $beasiswa): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($beasiswa['nama_beasiswa']) ?></td>
                                    <td><?= number_format($beasiswa['syarat_ipk'], 2) ?></td>
                                    <td>
                                        <?php 
                                        $desc = htmlspecialchars($beasiswa['deskripsi']);
                                        echo strlen($desc) > 50 ? substr($desc, 0, 50) . '...' : $desc;
                                        ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            <a href="beasiswa.php?edit=<?= $beasiswa['id'] ?>" 
                                               class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                                ✏️ Edit
                                            </a>
                                            
                                            <form method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Yakin ingin menghapus jenis beasiswa ini? Pastikan tidak ada pendaftaran yang menggunakan jenis beasiswa ini.')">
                                                <input type="hidden" name="id" value="<?= $beasiswa['id'] ?>">
                                                <input type="hidden" name="delete_beasiswa" value="1">
                                                <button type="submit" class="btn btn-danger" 
                                                        style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="card" style="background-color: #f8f9fa;">
            <h3>ℹ️ Informasi Penting</h3>
            <ul>
                <li><strong>IPK Minimal:</strong> Masukkan nilai IPK minimal yang diperlukan untuk jenis beasiswa ini (0.00 - 4.00)</li>
                <li><strong>Deskripsi:</strong> Berikan penjelasan singkat tentang jenis beasiswa ini</li>
                <li><strong>Menghapus Beasiswa:</strong> Jenis beasiswa tidak dapat dihapus jika masih ada pendaftaran yang menggunakannya</li>
                <li><strong>Edit Beasiswa:</strong> Perubahan akan mempengaruhi tampilan di halaman pendaftaran</li>
            </ul>
        </div>
    </div>
</body>
</html>