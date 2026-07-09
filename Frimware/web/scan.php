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

    $sql = "
        UPDATE scan_rfid
        SET uid='$uid'
        WHERE id=1
    ";

    if($conn->query($sql))
    {
        echo "OK";
    }
    else
    {
        echo "ERROR";
    }
}
else
{
    echo "NO UID";
}

?>