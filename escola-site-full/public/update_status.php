<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: dashboard.php?err=Requisição inválida');
  exit;
}

$leadId = isset($_POST['lead_id']) ? (int)$_POST['lead_id'] : 0;
$status = $_POST['status'] ?? '';

$allowed = ['aceito','interessado','nao_quero','pendente'];
if ($leadId <= 0 || !in_array($status, $allowed, true)) {
  header('Location: dashboard.php?err=Dados inválidos');
  exit;
}

$pdo = get_pdo();
$user = current_user();

try {
  if ($user['role'] === 'funcionario') {
    $st = $pdo->prepare("UPDATE leads SET status=? WHERE id=? AND assigned_to=?");
    $st->execute([$status, $leadId, $user['id']]);
    if ($st->rowCount() === 0) {
      header('Location: dashboard.php?err=Sem permissão para atualizar este lead');
      exit;
    }
  } else {
    $st = $pdo->prepare("UPDATE leads SET status=? WHERE id=?");
    $st->execute([$status, $leadId]);
  }
  header('Location: dashboard.php?msg=Status+atualizado');
} catch (Throwable $e) {
  header('Location: dashboard.php?err='.urlencode('Erro: '.$e->getMessage()));
}
