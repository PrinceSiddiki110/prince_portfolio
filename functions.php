<?php
function isAdminLoggedIn() {
  return !empty($_SESSION['admin_id']);
}

function require_admin() {
  if (!isAdminLoggedIn()) {
    header('Location: adminlogin.php');
    exit;
  }
}

function logout() {
  session_start();
  session_unset();
  session_destroy();
  header('Location: adminlogin.php');
  exit;
}
