<?php
include '../../config/conexao.php';

$sql = "SELECT codlive FROM linklive LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['codlive'];
} else {
    echo "";
}

$conn->close();
?>
