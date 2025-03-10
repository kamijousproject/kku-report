<?php
session_start();
if (isset($_SESSION["user_id"])) {
  header("Location: template-vertical-nav/index.php");
  exit();
} else {
  header("Location: login.php");
  exit();
}
?>
