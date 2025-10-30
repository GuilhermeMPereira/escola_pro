<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Escola - Painel</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="topbar">
  <div class="container">
    <div class="brand">Escola Pro</div>

    <button id="menu-toggle" class="menu-toggle-btn" aria-label="Abrir menu">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>

    <nav id="main-nav">
      <?php if ($user): ?>
        <a href="dashboard.php">Dashboard</a>
        <?php if ($user['role'] === 'admin'): ?>
          <a href="dashboard.php#importar">Importar CSV</a>
        <?php endif; ?>
        <a href="logout.php" class="btn-ghost">Sair</a>
      <?php else: ?>
        <a href="index.php">Login</a>
        <a href="register.php" class="btn-ghost">Cadastrar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container page">

<script>
  // Espera o documento carregar
  document.addEventListener("DOMContentLoaded", function() {
    
    // Seleciona os elementos
    const menuToggle = document.getElementById('menu-toggle');
    const mainNav = document.getElementById('main-nav');

    if (menuToggle && mainNav) {
      // Adiciona o evento de clique no botão
      menuToggle.addEventListener('click', function() {
        // Adiciona/remove a classe 'nav-open' no menu
        mainNav.classList.toggle('nav-open');
      });
    }
  });
</script>