<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "ziqrimukti"
);

$scan = $conn->query(
    "SELECT uid FROM scan_rfid WHERE id=1"
);

$uid = "";

if($scan && $scan->num_rows > 0)
{
    $data = $scan->fetch_assoc();
    $uid = $data['uid'];
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Kartu RFID</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            margin:30px;
        }

        h2{
            text-align:center;
        }

        form{
            width:400px;
            margin:auto;
        }

        input{
            width:100%;
            padding:10px;
            margin-top:5px;
            margin-bottom:15px;
        }

        button{
            padding:10px 20px;
            cursor:pointer;
        }

        .scan{
            background:green;
            color:white;
            border:none;
        }

        .simpan{
            background:blue;
            color:white;
            border:none;
        }

        .kembali{
            background:gray;
            color:white;
            border:none;
        }

        .button-group{
            display:flex;
            gap:10px;
            margin-top:10px;
        }

        #status{
            color:red;
            font-weight:bold;
        }

    </style>

</head>

<body>

<h2>REGISTRASI KARTU RFID</h2>

<form action="simpan_kartu.php" method="POST">

    UID RFID

    <input
        type="text"
        id="uid"
        name="uid"
        value=""
        readonly
    >

    <button
        type="button"
        class="scan"
        onclick="mulaiScan()"
    >
        Scan Kartu
    </button>

    <br><br>

    <span id="status"></span>

    <br><br>

    Nama

    <input
        type="text"
        name="nama"
        required
    >

    NIM

    <input
        type="text"
        name="nim"
        required
    >

    Kelas

    <input
        type="text"
        name="kelas"
        required
    >

    <div class="button-group">

        <button
            type="submit"
            class="simpan"
        >
            Simpan
        </button>

        <button
            type="button"
            class="kembali"
            onclick="window.location.href='index.php'"
        >
            Kembali
        </button>

    </div>

</form>

<script>

function mulaiScan()
{
    document.getElementById("uid").value = "";

    document.getElementById("status").innerHTML =
    "Tempelkan kartu RFID...";

    let interval = setInterval(function()
    {
        fetch("ambil_uid.php")
        .then(response => response.text())
        .then(uidBaru =>
        {
            uidBaru = uidBaru.trim();

            if(uidBaru != "")
            {
                document.getElementById("uid").value =
                uidBaru;

                document.getElementById("status").innerHTML =
                "Kartu berhasil dibaca";

                clearInterval(interval);
            }
        });

    }, 1000);
}

</script>

</body>
</html>