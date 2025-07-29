<?php
$beasiswa_list = getAllBeasiswa();
?>

<h2>Selamat Datang di Portal Beasiswa</h2>

<div class="alert alert-info">
    <h4>Informasi Penting:</h4>
    <ul>
        <li>Sistem akan generate IPK random (2.60-4.00), minimal 3.0 untuk dapat mendaftar beasiswa</li>
        <li>Siapkan berkas syarat dalam format PDF, JPG, PNG, atau ZIP (maksimal 5MB)</li>
        <li>Isi data dengan lengkap dan benar</li>
        <li>Setelah mendaftar, status ajuan akan menjadi "belum di verifikasi"</li>
    </ul>
</div>

<h3>Jenis Beasiswa Yang Tersedia</h3>

<?php if (empty($beasiswa_list)): ?>
    <div class="alert alert-warning">
        Belum ada jenis beasiswa yang tersedia saat ini.
    </div>
<?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
        <?php foreach ($beasiswa_list as $beasiswa): ?>
            <div class="card">
                <h3><?= htmlspecialchars($beasiswa['nama_beasiswa']) ?></h3>
                <p><strong>Syarat IPK Minimal:</strong> <?= number_format($beasiswa['syarat_ipk'], 2) ?></p>
                <p><?= htmlspecialchars($beasiswa['deskripsi']) ?></p>
                
                <div style="margin-top: 1rem;">
                    <h4>Syarat Umum:</h4>
                    <ul>
                        <li>Mahasiswa aktif semester 1-8</li>
                        <li>IPK minimal <?= number_format($beasiswa['syarat_ipk'], 2) ?> (sistem generate 2.60-4.00)</li>
                        <li>Melengkapi berkas persyaratan</li>
                        <li>Mengisi formulir pendaftaran dengan benar</li>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div style="text-align: center; margin-top: 2rem;">
    <a href="?page=daftar" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">
        Daftar Beasiswa Sekarang
    </a>
</div>

<div class="card" style="margin-top: 2rem;">
    <h3>Cara Mendaftar Beasiswa</h3>
    <ol>
        <li><strong>Klik "Daftar Beasiswa"</strong> - Buka halaman formulir pendaftaran</li>
        <li><strong>Isi Data Pribadi</strong> - Masukkan nama, email, dan nomor HP</li>
        <li><strong>Pilih Semester</strong> - Pilih semester saat ini (1-8)</li>
        <li><strong>Cek IPK Otomatis</strong> - Sistem akan generate IPK random (2.60 - 4.00) secara otomatis</li>
        <li><strong>Pilih Jenis Beasiswa</strong> - Pilih beasiswa yang sesuai (jika IPK memenuhi syarat)</li>
        <li><strong>Upload Berkas</strong> - Upload berkas syarat dalam format yang diizinkan</li>
        <li><strong>Submit Pendaftaran</strong> - Klik tombol "Daftar Beasiswa"</li>
        <li><strong>Cek Status</strong> - Lihat hasil pendaftaran di menu "Hasil Pendaftaran"</li>
    </ol>
</div>

<div class="card" style="background-color: #f8f9fa;">
    <h3>Kontak & Bantuan</h3>
    <p>Jika Anda mengalami kesulitan dalam proses pendaftaran, silakan hubungi:</p>
    <ul>
        <li><strong>Email:</strong> beasiswa@universitascontoh.ac.id</li>
        <li><strong>Telepon:</strong> (021) 1234-5678</li>
        <li><strong>WhatsApp:</strong> 0812-3456-7890</li>
        <li><strong>Jam Layanan:</strong> Senin-Jumat, 08:00-16:00 WIB</li>
    </ul>
</div>