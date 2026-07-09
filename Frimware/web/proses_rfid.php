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

    // cek kartu terdaftar
    $cekKartu = $conn->query(
        "SELECT * FROM kartu
         WHERE uid='$uid'"
    );

    // BELUM TERDAFTAR
    if($cekKartu->num_rows == 0)
    {
        $conn->query(
            "UPDATE scan_rfid
             SET uid='$uid'
             WHERE id=1"
        );

        echo "BELUM_TERDAFTAR";
        exit();
    }

    // cek absen hari ini
$cekAbsen = $conn->query(
    "SELECT *
     FROM absen
     WHERE uid='$uid'
     AND DATE(masuk)=CURDATE()
     ORDER BY id DESC
     LIMIT 1"
);

    if($cekAbsen->num_rows > 0)
{
    $row = $cekAbsen->fetch_assoc();

    // sudah masuk tapi belum pulang
    if($row['pulang'] == NULL)
    {
        $conn->query(
            "UPDATE absen
             SET pulang = NOW()
             WHERE id=".$row['id']
        );

        echo "PULANG";
    }
    else
    {
        echo "SUDAH_ABSEN";
    }
}
    else
   {
    $conn->query(
        "INSERT INTO absen(uid, masuk)
         VALUES('$uid', NOW())"
    );

    echo "MASUK";
   }
}

?>