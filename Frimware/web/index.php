<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "ziqrimukti"
);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Data absensi
$tanggal = "";

if(isset($_GET['tanggal']))
{
    $tanggal = $_GET['tanggal'];

    $data = $conn->query("
        SELECT
            absen.id,
            absen.uid,
            absen.masuk,
            absen.pulang,
            kartu.nama,
            kartu.nim,
            kartu.kelas
        FROM absen
        LEFT JOIN kartu
        ON absen.uid = kartu.uid
        WHERE DATE(absen.masuk)='$tanggal'
        ORDER BY absen.id DESC
    ");
}
else
{
    $data = $conn->query("
        SELECT
            absen.id,
            absen.uid,
            absen.masuk,
            absen.pulang,
            kartu.nama,
            kartu.nim,
            kartu.kelas
        FROM absen
        LEFT JOIN kartu
        ON absen.uid = kartu.uid
        WHERE DATE(absen.masuk)=CURDATE()
        ORDER BY absen.id DESC
    ");
}

// Jumlah mahasiswa terdaftar
$totalMahasiswa = $conn->query("
    SELECT COUNT(*) AS total
    FROM kartu
")->fetch_assoc()['total'];

// Jumlah mahasiswa hadir hari ini
$totalHadir = $conn->query("
    SELECT COUNT(DISTINCT uid) AS total
    FROM absen
    WHERE DATE(masuk)=CURDATE()
")->fetch_assoc()['total'];

// Daftar mahasiswa hadir hari ini
$hadirHariIni = $conn->query("
    SELECT DISTINCT kartu.nama
    FROM absen
    LEFT JOIN kartu
    ON absen.uid = kartu.uid
    WHERE DATE(absen.masuk)=CURDATE()
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Absensi RFID</title>

    <style>

        body{
    font-family:'Segoe UI',sans-serif;
    background:#f4f7fc;
    margin:0;
    padding:20px;
}

h2{
    text-align:center;
    color:#1e3a8a;
    margin-bottom:30px;
}

.btn{
    background:#2563eb;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    font-size:15px;
    cursor:pointer;
    box-shadow:0 4px 10px rgba(0,0,0,0.15);
}

.btn:hover{
    background:#1d4ed8;
}

.info-container{
    display:flex;
    gap:20px;
    margin-bottom:20px;
}

.info-box{
    flex:1;
    color:white;
    padding:25px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
}

.terdaftar{
    background:linear-gradient(135deg,#16a34a,#22c55e);
}

.hadir{
    background:linear-gradient(135deg,#2563eb,#3b82f6);
}

.daftar-hadir{
    background:white;
    border-radius:15px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.daftar-hadir h3{
    margin-top:0;
    color:#1e3a8a;
}

.daftar-hadir ul{
    padding-left:20px;
}

table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
    background:white;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

th, td{
    padding:12px;
    text-align:center;
    vertical-align:middle;
    word-wrap:break-word;
    border-bottom:1px solid #e5e7eb;
}

tr:hover{
    background:#f1f5f9;
}

.jam-container{
    position:absolute;
    top:20px;
    right:20px;
    text-align:right;
    background:white;
    padding:12px 20px;
    border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
    font-weight:bold;
}

form{
    margin-bottom:20px;
}

input[type=date]{
    padding:8px;
    border:1px solid #ccc;
    border-radius:8px;
}

button[type=submit]{
    background:#2563eb;
    color:white;
    border:none;
    padding:8px 15px;
    border-radius:8px;
    cursor:pointer;
}

/* ===== DARK MODE ===== */

.dark-mode{
    background:#111827;
    color:white;
}

.dark-mode h2{
    color:white;
}

.dark-mode .daftar-hadir{
    background:#1f2937;
    color:white;
}

.dark-mode .daftar-hadir h3{
    color:white;
}

.dark-mode table{
    background:#1f2937;
    color:white;
}

.dark-mode th{
    background:#1e40af;
}

.dark-mode td{
    border-bottom:1px solid #374151;
}

.dark-mode tr:hover{
    background:#374151;
}

.dark-mode .jam-container{
    background:#1f2937;
    color:white;
}

.dark-mode input[type=date]{
    background:#374151;
    color:white;
    border:1px solid #666;
}

</style>

</head>
<body>

<h2>RFID BINTANG ZIQRI GIPAR RIPAN</h2>

<a href="registrasi.php">
    <button class="btn">
        Registrasi Kartu Baru
    </button>
</a>

<button
    class="btn"
    onclick="document.getElementById('filterTanggal').style.display='block';">
    📅 Pilih Tanggal
</button>

<button
    id="modeBtn"
    class="btn"
    onclick="toggleMode()">
    🌙 Dark Mode
</button>

<div id="filterTanggal" style="display:none; margin:20px 0;">

<form method="GET">

    <input
        type="date"
        name="tanggal"
        required
    >

    <button
        class="btn"
        type="submit">
        Tampilkan
    </button>

</form>

</div>

<div class="info-container">
    <div class="info-box terdaftar">
        <h3>Mahasiswa Terdaftar</h3>
        <h1><?php echo $totalMahasiswa; ?></h1>
    </div>

    <div class="info-box hadir">
        <h3>Sudah Absen Hari Ini</h3>
        <h1><?php echo $totalHadir; ?></h1>
    </div>

</div>

<div class="daftar-hadir">

    <h3>Mahasiswa Hadir Hari Ini</h3>

    <ul>

    <?php

    if($hadirHariIni->num_rows > 0)
    {
        while($hadir = $hadirHariIni->fetch_assoc())
        {
            echo "<li>".$hadir['nama']."</li>";
        }
    }
    else
    {
        echo "<li>Belum ada mahasiswa yang hadir hari ini</li>";
    }

    ?>

    </ul>

</div>

<table>

<tr>
    <th style="width:13%;">Nama</th>
    <th style="width:12%;">NIM</th>
    <th style="width:8%;">Kelas</th>
    <th style="width:12%;">UID</th>
    <th style="width:22%;">Masuk</th>
    <th style="width:22%;">Pulang</th>
    <th style="width:11%;">Durasi</th>
</tr>

<?php while($row = $data->fetch_assoc()) { ?>

<tr>

<td><?php echo $row['nama']; ?></td>

<td><?php echo $row['nim']; ?></td>

<td><?php echo $row['kelas']; ?></td>

<td><?php echo $row['uid']; ?></td>

<td><?php echo $row['masuk']; ?></td>

<td>
<?php
echo ($row['pulang'])
     ? $row['pulang']
     : '-';
?>
</td>

<td>

<?php

if($row['pulang'])
{
    $masuk  = strtotime($row['masuk']);
    $pulang = strtotime($row['pulang']);

    $durasi = $pulang - $masuk;

    echo gmdate("H:i:s", $durasi);
}
else
{
    echo "-";
}

?>

</td>

</tr>

<?php } ?>

</table>

<script>

function updateJam()
{
    const sekarang = new Date();

    const tanggal = sekarang.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    const jam = sekarang.toLocaleTimeString('id-ID');

    document.getElementById("tanggal").innerHTML = tanggal;
    document.getElementById("jam").innerHTML = jam;
}

function toggleMode()
{
    document.body.classList.toggle("dark-mode");

    let btn = document.getElementById("modeBtn");

    if(document.body.classList.contains("dark-mode"))
    {
        btn.innerHTML = "☀️ Light Mode";
        localStorage.setItem("mode","dark");
    }
    else
    {
        btn.innerHTML = "🌙 Dark Mode";
        localStorage.setItem("mode","light");
    }
}

window.onload = function()
{
    updateJam();
    setInterval(updateJam,1000);

    if(localStorage.getItem("mode")=="dark")
    {
        document.body.classList.add("dark-mode");
        document.getElementById("modeBtn").innerHTML="☀️ Light Mode";
    }
}

</script>


</body>
<div class="jam-container">
    <div id="tanggal"></div>
    <div id="jam"></div>
</div>

</body>
</html>
