<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';

  if ($email && $pass) {
    $pdo = get_pdo();
    $st = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ?");
    $st->execute([$email]);
    $u = $st->fetch();
    if ($u && password_verify($pass, $u['password_hash'])) {
      $_SESSION['user'] = [
        'id' => $u['id'],
        'name' => $u['name'],
        'email' => $u['email'],
        'role' => $u['role']
      ];
      header('Location: dashboard.php');
      exit;
    } else {
      $err = "Credenciais inválidas.";
    }
  } else {
    $err = "Informe e-mail e senha.";
  }
}
?>
 
<div class="card">
  <h2>Entrar</h2>
  <?php if (!empty($_GET['msg'])): ?><div class="notice"><?=htmlspecialchars($_GET['msg'])?></div><?php endif; ?>
  <?php if (!empty($err)): ?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <form method="post">
    <label class="label">E-mail</label>
    <input class="input" type="email" name="email" required>
    <label class="label">Senha</label>
    <input class="input" type="password" name="password" required>
    <div class="actions">
      <button class="btn" type="submit">Entrar</button>
      <a class="btn-ghost" href="register.php">Criar conta</a>
    </div>
  </form>
</div>
</main>
</body>
</html>
