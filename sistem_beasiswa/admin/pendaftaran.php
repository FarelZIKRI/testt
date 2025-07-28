<?php
require_once '../includes/admin_functions.php';
requireAdminLogin();

$message = '';
$success = false;

// Proses update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    if (updateStatusPendaftaran($id, $status)) {
        $message = 'Status pendaftaran berhasil diupdate';
        $success = true;
    } else {
        $message = 'Gagal mengupdate status pendaftaran';
    }
}

// Proses hapus pendaftaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_pendaftaran'])) {
    $id = $_POST['id'];
    
    if (deletePendaftaran($id)) {
        $message = 'Pendaftaran berhasil dihapus';
        $success = true;
    } else {
        $message = 'Gagal menghapus pendaftaran';
    }
}

// Filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : null;
$beasiswa_filter = isset($_GET['beasiswa']) ? $_GET['beasiswa'] : null;

$pendaftaran_list = getPendaftaranWithFilter($status_filter, $beasiswa_filter);
$beasiswa_list = getAllBeasiswa();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pendaftaran - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .admin-nav {
            background-color: #34495e;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .admin-nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 0;
            padding: 0;
        }
        .admin-nav ul li {
            margin: 0 1rem;
        }
        .admin-nav ul li a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .admin-nav ul li a:hover,
        .admin-nav ul li a.active {
            background-color: #2c3e50;
        }
        .filter-form {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .filter-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto auto;
            gap: 1rem;
            align-items: end;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        @media (max-width: 768px) {
            .filter-row {
                grid-template-columns: 1fr;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1>📋 Kelola Pendaftaran</h1>
            <p>Kelola dan verifikasi pendaftaran beasiswa</p>
        </div>
    </header>

    <nav class="admin-nav">
        <div class="container">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="pendaftaran.php" class="active">Kelola Pendaftaran</a></li>
                <li><a href="beasiswa.php">Kelola Beasiswa</a></li>
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

        <!-- Filter Form -->
        <div class="filter-form">
            <h3>Filter Pendaftaran</h3>
            <form method="GET" class="filter-row">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="belum di verifikasi" <?= $status_filter === 'belum di verifikasi' ? 'selected' : '' ?>>Belum Diverifikasi</option>
                        <option value="diverifikasi" <?= $status_filter === 'diverifikasi' ? 'selected' : '' ?>>Diverifikasi</option>
                        <option value="ditolak" <?= $status_filter === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="beasiswa">Jenis Beasiswa</label>
                    <select id="beasiswa" name="beasiswa">
                        <option value="">Semua Beasiswa</option>
                        <?php foreach ($beasiswa_list as $beasiswa): ?>
                            <option value="<?= $beasiswa['id'] ?>" <?= $beasiswa_filter == $beasiswa['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($beasiswa['nama_beasiswa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Filter</button>
                <a href="pendaftaran.php" class="btn" style="background: #95a5a6;">Reset</a>
            </form>
        </div>

        <!-- Pendaftaran List -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Daftar Pendaftaran (<?= count($pendaftaran_list) ?> data)</h3>
                <a href="laporan.php?export=csv" class="btn" style="background: #27ae60;">
                    📊 Export CSV
                </a>
            </div>

            <?php if (empty($pendaftaran_list)): ?>
                <div class="alert alert-info">
                    Tidak ada data pendaftaran yang sesuai dengan filter.
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Semester</th>
                                <th>IPK</th>
                                <th>Jenis Beasiswa</th>
                                <th>Berkas</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendaftaran_list as $index => $pendaftaran): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($pendaftaran['nama']) ?></td>
                                    <td><?= htmlspecialchars($pendaftaran['email']) ?></td>
                                    <td><?= htmlspecialchars($pendaftaran['no_hp']) ?></td>
                                    <td><?= $pendaftaran['semester'] ?></td>
                                    <td><?= number_format($pendaftaran['ipk'], 2) ?></td>
                                    <td><?= htmlspecialchars($pendaftaran['nama_beasiswa']) ?></td>
                                    <td>
                                        <?php if ($pendaftaran['berkas_syarat']): ?>
                                            <a href="../uploads/<?= htmlspecialchars($pendaftaran['berkas_syarat']) ?>" 
                                               target="_blank" class="btn btn-small">
                                                📄 Lihat
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #666;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        switch($pendaftaran['status_ajuan']) {
                                            case 'belum di verifikasi':
                                                $status_class = 'status-pending';
                                                break;
                                            case 'diverifikasi':
                                                $status_class = 'status-verified';
                                                break;
                                            case 'ditolak':
                                                $status_class = 'status-rejected';
                                                break;
                                        }
                                        ?>
                                        <span class="status-badge <?= $status_class ?>">
                                            <?= ucfirst($pendaftaran['status_ajuan']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($pendaftaran['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="detail_pendaftaran.php?id=<?= $pendaftaran['id'] ?>" 
                                               class="btn btn-small">
                                                👁️ Detail
                                            </a>
                                            
                                            <!-- Form Update Status -->
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= $pendaftaran['id'] ?>">
                                                <select name="status" onchange="this.form.submit()" class="btn-small" style="font-size: 0.75rem;">
                                                    <option value="">Ubah Status</option>
                                                    <option value="belum di verifikasi" <?= $pendaftaran['status_ajuan'] === 'belum di verifikasi' ? 'disabled' : '' ?>>
                                                        Belum Verifikasi
                                                    </option>
                                                    <option value="diverifikasi" <?= $pendaftaran['status_ajuan'] === 'diverifikasi' ? 'disabled' : '' ?>>
                                                        Verifikasi
                                                    </option>
                                                    <option value="ditolak" <?= $pendaftaran['status_ajuan'] === 'ditolak' ? 'disabled' : '' ?>>
                                                        Tolak
                                                    </option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                            
                                            <!-- Form Delete -->
                                            <form method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Yakin ingin menghapus pendaftaran ini?')">
                                                <input type="hidden" name="id" value="<?= $pendaftaran['id'] ?>">
                                                <input type="hidden" name="delete_pendaftaran" value="1">
                                                <button type="submit" class="btn btn-danger btn-small">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto submit form saat status berubah
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelects = document.querySelectorAll('select[name="status"]');
            statusSelects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.value && confirm('Yakin ingin mengubah status?')) {
                        this.form.submit();
                    } else {
                        this.value = '';
                    }
                });
            });
        });
    </script>
</body>
</html>