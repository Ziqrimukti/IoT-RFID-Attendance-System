<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "ziqrimukti"
);

if(isset($_POST['uid']))
{
    $uid   = trim($_POST['uid']);
    $nama  = trim($_POST['nama']);
    $nim   = trim($_POST['nim']);
    $kelas = trim($_POST['kelas']);

    // cek UID sudah terdaftar atau belum
    $cek = $conn->query("
        SELECT *
        FROM kartu
        WHERE uid='$uid'
    ");

    if($cek->num_rows > 0)
    {
        echo "
        <script>
        alert('Kartu sudah terdaftar!');
        window.location='registrasi.php';
        </script>
        ";
        exit();
    }

    $simpan = $conn->query("
        INSERT INTO kartu(uid,nama,nim,kelas)
        VALUES('$uid','$nama','$nim','$kelas')
    ");

    if($simpan)
    {
        // kosongkan scan_rfid setelah berhasil daftar
        $conn->query("
            UPDATE scan_rfid
            SET uid=''
            WHERE id=1
        ");

        echo "
        <script>
        alert('Kartu berhasil didaftarkan!');
        window.location='registrasi.php';
        </script>
        ";
    }
    else
    {
        echo "Gagal: ".$conn->error;
    }
}

?>
