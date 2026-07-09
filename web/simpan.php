<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "ziqrimukti"
);

if(isset($_POST['uid']))
{
    $uid = strtoupper($_POST['uid']);

    // CEK APAKAH KARTU TERDAFTAR
    $cekKartu = $conn->query("
        SELECT * FROM kartu
        WHERE uid='$uid'
    ");

    if($cekKartu->num_rows == 0)
    {
        echo "TIDAK_TERDAFTAR";
        exit();
    }

    // CEK ABSENSI TERAKHIR
    $cek = $conn->query("
        SELECT *
        FROM absen
        WHERE uid='$uid'
        AND pulang IS NULL
        ORDER BY id DESC
        LIMIT 1
    ");

    if($cek->num_rows > 0)
    {
        // PULANG
        $row = $cek->fetch_assoc();

        $conn->query("
            UPDATE absen
            SET pulang = NOW()
            WHERE id=".$row['id']."
        ");

        echo "PULANG";
    }
    else
    {
        // MASUK
        $conn->query("
            INSERT INTO absen(uid, masuk)
            VALUES('$uid', NOW())
        ");

        echo "MASUK";
    }
}
else
{
    echo "NO UID";
}

?>
