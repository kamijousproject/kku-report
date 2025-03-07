<?php
session_start();
if (isset($_SESSION["user_id"])) {
  header("Location: template-vertical-nav/index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Meta -->
  <meta name="description" content="Premium Quality and Responsive UI for Dashboard.">
  <meta name="author" content="NineKit">

  <title>Ameen - Bootstrap Admin Dashboard HTML Template</title>

</head>

<body>
  <script>
    // similar behavior as an HTTP redirect
    window.location.replace("template-vertical-nav/index.php");
  </script>
</body>

</html>