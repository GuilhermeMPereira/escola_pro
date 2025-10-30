<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function is_logged_in(): bool { return isset($_SESSION['user']); }
function current_user() { return $_SESSION['user'] ?? null; }
function require_login() {
  if (!is_logged_in()) { header('Location: index.php?msg=Faça+login'); exit; }
}
function require_admin() {
  require_login();
  if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: dashboard.php?err=Acesso+restrito+ao+administrador'); exit;
  }
}
