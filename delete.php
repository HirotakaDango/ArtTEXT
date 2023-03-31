<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
    header('Location: session.php');
}
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $query = "DELETE FROM posts WHERE id='$post_id'";
    $db->exec($query);
}
header('Location: profile.php');
?>
