<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "ziqrimukti"
);

$data = $conn->query(
    "SELECT uid
     FROM scan_rfid
     WHERE id=1"
);

if($data && $data->num_rows > 0)
{
    $row = $data->fetch_assoc();
    echo $row['uid'];
}
else
{
    echo "";
}

?>
