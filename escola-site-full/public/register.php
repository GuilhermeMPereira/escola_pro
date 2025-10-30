<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name  = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  $role  = $_POST['role'] ?? 'funcionario';

  if (!$name || !$email || !$pass) {
    $err = "Preencha todos os campos.";
  } elseif (!in_array($role, ['admin','funcionario'])) {
    $err = "Perfil inválido.";
  } else {
    try {
      $pdo = get_pdo();
      $st = $pdo->prepare("INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,?)");
      $st->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), $role]);
      header('Location: index.php?msg=Cadastro+concluído.+Faça+login');
      exit;
    } catch (PDOException $e) {
      $err = (str_contains($e->getMessage(), 'Duplicate'))
        ? 'E-mail já cadastrado.' : 'Erro ao cadastrar.';
    }
  }
}
?>
<div class="card">
  <h2>Criar conta</h2>
  <?php if (!empty($err)): ?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <form method="post">
    <label class="label">Nome</label>
    <input class="input" name="name" required>

    <label class="label">E-mail</label>
    <input class="input" type="email" name="email" required>

    <label class="label">Senha</label>
    <input class="input" type="password" name="password" required>

    <label class="label">Perfil</label>
    <select class="input" name="role">
      <option value="funcionario">Funcionário</option>
      <option value="admin">Administrador</option>
    </select>

    <div class="actions">
      <button class="btn" type="submit">Cadastrar</button>
      <a class="btn-ghost" href="index.php">Cancelar</a>
    </div>
  </form>
</div>
</main></body></html>
