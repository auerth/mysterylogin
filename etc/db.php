<?php
// Create connection remove "_" from name

$db = new mysqli("localhost", "sae", "sae2021!","sae");
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
?>