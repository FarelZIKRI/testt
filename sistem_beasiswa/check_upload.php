<?php
// File untuk mengecek konfigurasi upload dan troubleshooting
echo "<h2>Troubleshooting Upload File</h2>";

echo "<h3>1. Konfigurasi PHP Upload</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . " detik<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";

echo "<h3>2. Status Folder Uploads</h3>";
$upload_dir = "uploads/";

if (!is_dir($upload_dir)) {
    echo "❌ Folder uploads tidak ada<br>";
    if (mkdir($upload_dir, 0755, true)) {
        echo "✅ Folder uploads berhasil dibuat<br>";
    } else {
        echo "❌ Gagal membuat folder uploads<br>";
    }
} else {
    echo "✅ Folder uploads sudah ada<br>";
}

if (is_writable($upload_dir)) {
    echo "✅ Folder uploads dapat ditulis<br>";
} else {
    echo "❌ Folder uploads tidak dapat ditulis<br>";
    echo "Solusi: Ubah permission folder uploads menjadi 755 atau 777<br>";
}

echo "<h3>3. Test Pembuatan File</h3>";
$test_file = $upload_dir . "test_" . time() . ".txt";
if (file_put_contents($test_file, "test")) {
    echo "✅ Berhasil membuat file test<br>";
    if (unlink($test_file)) {
        echo "✅ Berhasil menghapus file test<br>";
    }
} else {
    echo "❌ Gagal membuat file test<br>";
}

echo "<h3>4. Path Information</h3>";
echo "Current Directory: " . getcwd() . "<br>";
echo "Upload Directory: " . realpath($upload_dir) . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

echo "<h3>5. Solusi Jika Masih Error:</h3>";
echo "<ol>";
echo "<li>Pastikan folder 'uploads' ada di direktori yang sama dengan file PHP</li>";
echo "<li>Ubah permission folder uploads: <code>chmod 755 uploads</code> atau <code>chmod 777 uploads</code></li>";
echo "<li>Pastikan PHP memiliki akses write ke direktori</li>";
echo "<li>Cek konfigurasi php.ini untuk upload_max_filesize dan post_max_size</li>";
echo "<li>Restart web server setelah mengubah konfigurasi</li>";
echo "</ol>";

echo "<p><a href='index.php'>Kembali ke Aplikasi</a></p>";
?>