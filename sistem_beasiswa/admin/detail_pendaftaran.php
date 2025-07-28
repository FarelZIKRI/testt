<?php
require_once '../includes/admin_functions.php';
requireAdminLogin();

$message = '';
$success = false;

// Ambil ID pendaftaran
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header('Location: pendaftaran.php');
    exit;
}

// Proses update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    
    if (updateStatusPendaftaran($id, $status)) {
        $message = 'Status pendaftaran berhasil diupdate';
        $success = true;
    } else {
        $message = 'Gagal mengupdate status pendaftaran';
    }
}

// Ambil detail pendaftaran
$pendaftaran = getPendaftaranById($id);
if (!$pendaftaran) {
    header('Location: pendaftaran.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pendaftaran - Admin</title>
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
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .detail-item {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #2c3e50;
        }
        .status-actions {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
            .detail-item {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <h1>👁️ Detail Pendaftaran</h1>
            <p>Informasi lengkap pendaftaran beasiswa</p>
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

        <!-- Navigasi Kembali -->
        <div style="margin-bottom: 2rem;">
            <a href="pendaftaran.php" class="btn">← Kembali ke Daftar Pendaftaran</a>
        </div>

        <div class="detail-grid">
            <!-- Informasi Pendaftar -->
            <div class="card">
                <h3>👤 Informasi Pendaftar</h3>
                
                <div class="detail-item">
                    <div class="detail-label">Nama Lengkap:</div>
                    <div><?= htmlspecialchars($pendaftaran['nama']) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Email:</div>
                    <div>
                        <a href="mailto:<?= htmlspecialchars($pendaftaran['email']) ?>">
                            <?= htmlspecialchars($pendaftaran['email']) ?>
                        </a>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Nomor HP:</div>
                    <div>
                        <a href="tel:<?= htmlspecialchars($pendaftaran['no_hp']) ?>">
                            <?= htmlspecialchars($pendaftaran['no_hp']) ?>
                        </a>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Semester:</div>
                    <div>Semester <?= $pendaftaran['semester'] ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">IPK:</div>
                    <div>
                        <strong style="color: <?= $pendaftaran['ipk'] >= 3.0 ? '#27ae60' : '#e74c3c' ?>;">
                            <?= number_format($pendaftaran['ipk'], 2) ?>
                        </strong>
                        <?= $pendaftaran['ipk'] >= 3.0 ? '✅ Memenuhi syarat' : '❌ Tidak memenuhi syarat' ?>
                    </div>
                </div>
            </div>

            <!-- Informasi Beasiswa -->
            <div class="card">
                <h3>🎯 Informasi Beasiswa</h3>
                
                <div class="detail-item">
                    <div class="detail-label">Jenis Beasiswa:</div>
                    <div><?= htmlspecialchars($pendaftaran['nama_beasiswa']) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Syarat IPK Minimal:</div>
                    <div><?= number_format($pendaftaran['syarat_ipk'], 2) ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Status IPK:</div>
                    <div>
                        <?php if ($pendaftaran['ipk'] >= $pendaftaran['syarat_ipk']): ?>
                            <span style="color: #27ae60;">✅ Memenuhi syarat beasiswa ini</span>
                        <?php else: ?>
                            <span style="color: #e74c3c;">❌ Tidak memenuhi syarat beasiswa ini</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Berkas Syarat:</div>
                    <div>
                        <?php if ($pendaftaran['berkas_syarat']): ?>
                            <a href="../uploads/<?= htmlspecialchars($pendaftaran['berkas_syarat']) ?>" 
                               target="_blank" class="btn" style="padding: 0.5rem 1rem;">
                                📄 Lihat Berkas
                            </a>
                        <?php else: ?>
                            <span style="color: #666;">Tidak ada berkas</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Tanggal Daftar:</div>
                    <div><?= formatTanggal($pendaftaran['created_at']) ?></div>
                </div>
            </div>
        </div>

        <!-- Status dan Aksi -->
        <div class="status-actions">
            <h3>⚙️ Status & Aksi</h3>
            
            <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 2rem; align-items: center; margin-bottom: 2rem;">
                <div>
                    <strong>Status Saat Ini:</strong>
                </div>
                <div>
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
                    <span class="status-badge <?= $status_class ?>" style="font-size: 1.1rem; padding: 0.5rem 1rem;">
                        <?= ucfirst($pendaftaran['status_ajuan']) ?>
                    </span>
                </div>
                <div>
                    <small style="color: #666;">
                        ID: #<?= $pendaftaran['id'] ?>
                    </small>
                </div>
            </div>

            <!-- Form Update Status -->
            <form method="POST" style="display: grid; grid-template-columns: auto 1fr auto; gap: 1rem; align-items: center;">
                <label for="status"><strong>Ubah Status:</strong></label>
                <select id="status" name="status" required>
                    <option value="">-- Pilih Status Baru --</option>
                    <option value="belum di verifikasi" <?= $pendaftaran['status_ajuan'] === 'belum di verifikasi' ? 'disabled' : '' ?>>
                        Belum Diverifikasi
                    </option>
                    <option value="diverifikasi" <?= $pendaftaran['status_ajuan'] === 'diverifikasi' ? 'disabled' : '' ?>>
                        Verifikasi (Setuju)
                    </option>
                    <option value="ditolak" <?= $pendaftaran['status_ajuan'] === 'ditolak' ? 'disabled' : '' ?>>
                        Tolak
                    </option>
                </select>
                <button type="submit" name="update_status" class="btn btn-success">
                    Update Status
                </button>
            </form>
        </div>

        <!-- Rekomendasi -->
        <div class="card" style="background-color: #f8f9fa;">
            <h3>💡 Rekomendasi Verifikasi</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div>
                    <h4>✅ Poin Positif:</h4>
                    <ul>
                        <?php if ($pendaftaran['ipk'] >= 3.0): ?>
                            <li>IPK memenuhi syarat minimum (≥ 3.0)</li>
                        <?php endif; ?>
                        
                        <?php if ($pendaftaran['ipk'] >= $pendaftaran['syarat_ipk']): ?>
                            <li>IPK memenuhi syarat beasiswa yang dipilih</li>
                        <?php endif; ?>
                        
                        <?php if ($pendaftaran['berkas_syarat']): ?>
                            <li>Berkas syarat telah diupload</li>
                        <?php endif; ?>
                        
                        <?php if (filter_var($pendaftaran['email'], FILTER_VALIDATE_EMAIL)): ?>
                            <li>Format email valid</li>
                        <?php endif; ?>
                        
                        <?php if (preg_match('/^[0-9]+$/', $pendaftaran['no_hp'])): ?>
                            <li>Format nomor HP valid</li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div>
                    <h4>⚠️ Perhatian:</h4>
                    <ul>
                        <?php if ($pendaftaran['ipk'] < 3.0): ?>
                            <li style="color: #e74c3c;">IPK di bawah syarat minimum (< 3.0)</li>
                        <?php endif; ?>
                        
                        <?php if ($pendaftaran['ipk'] < $pendaftaran['syarat_ipk']): ?>
                            <li style="color: #e74c3c;">IPK tidak memenuhi syarat beasiswa yang dipilih</li>
                        <?php endif; ?>
                        
                        <?php if (!$pendaftaran['berkas_syarat']): ?>
                            <li style="color: #e74c3c;">Berkas syarat belum diupload</li>
                        <?php endif; ?>
                        
                        <?php if ($pendaftaran['semester'] > 8): ?>
                            <li style="color: #f39c12;">Semester melebihi batas normal S1</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Aksi Tambahan -->
        <div style="text-align: center; margin: 2rem 0;">
            <a href="pendaftaran.php" class="btn">← Kembali ke Daftar</a>
            <a href="mailto:<?= htmlspecialchars($pendaftaran['email']) ?>" class="btn" style="background: #3498db;">
                📧 Kirim Email
            </a>
            <a href="tel:<?= htmlspecialchars($pendaftaran['no_hp']) ?>" class="btn" style="background: #27ae60;">
                📞 Hubungi
            </a>
        </div>
    </div>

    <script>
        // Konfirmasi sebelum update status
        document.querySelector('form').addEventListener('submit', function(e) {
            const status = document.getElementById('status').value;
            if (status) {
                const confirmMsg = `Yakin ingin mengubah status menjadi "${status}"?`;
                if (!confirm(confirmMsg)) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>