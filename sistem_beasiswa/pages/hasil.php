<?php
$pendaftaran_list = getAllPendaftaran();
?>

<h2>Hasil Pendaftaran Beasiswa</h2>

<?php if (empty($pendaftaran_list)): ?>
    <div class="alert alert-info">
        <h4>Belum Ada Pendaftaran</h4>
        <p>Belum ada data pendaftaran beasiswa yang tercatat dalam sistem.</p>
        <div style="margin-top: 1rem;">
            <a href="?page=daftar" class="btn btn-success">Daftar Beasiswa Sekarang</a>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <strong>Informasi:</strong> Berikut adalah daftar semua pendaftaran beasiswa yang telah disubmit. 
        Status "belum di verifikasi" menunjukkan bahwa pendaftaran masih dalam proses review.
    </div>

    <!-- Tampilan Desktop/Tablet -->
    <div class="table-responsive" style="display: block;">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Semester</th>
                    <th>IPK</th>
                    <th>Jenis Beasiswa</th>
                    <th>Berkas</th>
                    <th>Status Ajuan</th>
                    <th>Tanggal Daftar</th>
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
                                <a href="uploads/<?= htmlspecialchars($pendaftaran['berkas_syarat']) ?>" 
                                   target="_blank" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                    Lihat Berkas
                                </a>
                            <?php else: ?>
                                <span style="color: #666;">Tidak ada</span>
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
                        <td><?= formatTanggal($pendaftaran['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Tampilan Mobile -->
    <div class="mobile-cards" style="display: none;">
        <?php foreach ($pendaftaran_list as $index => $pendaftaran): ?>
            <div class="card">
                <h4>Pendaftaran #<?= $index + 1 ?></h4>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 0.5rem; margin-bottom: 1rem;">
                    <strong>Nama:</strong>
                    <span><?= htmlspecialchars($pendaftaran['nama']) ?></span>
                    
                    <strong>Email:</strong>
                    <span><?= htmlspecialchars($pendaftaran['email']) ?></span>
                    
                    <strong>No. HP:</strong>
                    <span><?= htmlspecialchars($pendaftaran['no_hp']) ?></span>
                    
                    <strong>Semester:</strong>
                    <span><?= $pendaftaran['semester'] ?></span>
                    
                    <strong>IPK:</strong>
                    <span><?= number_format($pendaftaran['ipk'], 2) ?></span>
                    
                    <strong>Beasiswa:</strong>
                    <span><?= htmlspecialchars($pendaftaran['nama_beasiswa']) ?></span>
                    
                    <strong>Berkas:</strong>
                    <span>
                        <?php if ($pendaftaran['berkas_syarat']): ?>
                            <a href="uploads/<?= htmlspecialchars($pendaftaran['berkas_syarat']) ?>" 
                               target="_blank" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                Lihat Berkas
                            </a>
                        <?php else: ?>
                            Tidak ada
                        <?php endif; ?>
                    </span>
                    
                    <strong>Status:</strong>
                    <span>
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
                    </span>
                    
                    <strong>Tanggal:</strong>
                    <span><?= formatTanggal($pendaftaran['created_at']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 2rem;">
        <p><strong>Total Pendaftaran:</strong> <?= count($pendaftaran_list) ?> pendaftar</p>
        <a href="?page=daftar" class="btn btn-success">Daftar Beasiswa Baru</a>
    </div>
<?php endif; ?>

<div class="card" style="margin-top: 2rem;">
    <h3>Keterangan Status Ajuan</h3>
    <div style="display: grid; grid-template-columns: auto 1fr; gap: 1rem; align-items: center;">
        <span class="status-badge status-pending">Belum di verifikasi</span>
        <span>Pendaftaran telah diterima dan sedang dalam proses review oleh tim beasiswa</span>
        
        <span class="status-badge status-verified">Diverifikasi</span>
        <span>Pendaftaran telah diverifikasi dan disetujui. Selamat!</span>
        
        <span class="status-badge status-rejected">Ditolak</span>
        <span>Pendaftaran tidak memenuhi syarat atau dokumen tidak lengkap</span>
    </div>
</div>

<div class="card" style="background-color: #f8f9fa;">
    <h3>Informasi Penting</h3>
    <ul>
        <li>Proses verifikasi membutuhkan waktu 3-7 hari kerja</li>
        <li>Pastikan nomor HP dan email yang didaftarkan aktif untuk dihubungi</li>
        <li>Jika status masih "belum di verifikasi" setelah 7 hari, silakan hubungi admin</li>
        <li>Pengumuman hasil akhir akan dikirim melalui email dan SMS</li>
    </ul>
</div>

<style>
@media (max-width: 768px) {
    .table-responsive {
        display: none !important;
    }
    .mobile-cards {
        display: block !important;
    }
}
</style>