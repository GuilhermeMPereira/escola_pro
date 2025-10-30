<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();
require_once __DIR__ . '/../includes/header.php';

$pdo = get_pdo();
$user = current_user();

$totalLeads = (int)$pdo->query("SELECT COUNT(*) c FROM leads")->fetch()['c'];
$totalUsers = (int)$pdo->query("SELECT COUNT(*) c FROM users")->fetch()['c'];

function status_badge($s) {
  $label = [
    'aceito'       => 'Aceito',
    'interessado'  => 'Interessado',
    'nao_quero'    => 'Não quer',
    'pendente'     => 'Pendente',
    ''             => 'Pendente',
    null           => 'Pendente'
  ][$s] ?? 'Pendente';
  $cls = [
    'aceito'       => 'status-green',
    'interessado'  => 'status-yellow',
    'nao_quero'    => 'status-red',
    'pendente'     => 'status-pendente',
    ''             => 'status-pendente',
    null           => 'status-pendente'
  ][$s] ?? 'status-pendente';
  return '<span class="badge-status '.$cls.'">'.$label.'</span>';
}

$myLeads = [];
if ($user['role'] === 'funcionario') {
  $st = $pdo->prepare("SELECT * FROM leads WHERE assigned_to = ? ORDER BY id DESC LIMIT 200");
  $st->execute([$user['id']]);
  $myLeads = $st->fetchAll();
}

$allLeads = [];
if ($user['role'] === 'admin') {
  $allLeads = $pdo->query("SELECT l.*, u.name AS responsavel
                           FROM leads l LEFT JOIN users u ON u.id=l.assigned_to
                           ORDER BY l.id DESC LIMIT 300")->fetchAll();
  $funcs = $pdo->query("SELECT id, name FROM users WHERE role='funcionario' ORDER BY name")->fetchAll();
}
?>
<div class="card">
  <h2>Olá, <?=htmlspecialchars($user['name'])?> 👋</h2>
  <div class="form-row">
    <div class="badge">Usuários: <?=$totalUsers?></div>
    <div class="badge">Leads: <?=$totalLeads?></div>
    <div class="badge">Perfil: <?=htmlspecialchars($user['role'])?></div>
  </div>
</div>

<?php if ($user['role'] === 'admin'): ?>
  <div id="importar" class="card">
    <h3>Importar CSV</h3>
    <p>Cabeçalho esperado (ordem livre): <strong>nome, telefone, escola, serie</strong>. Delimitador pode ser vírgula ou ponto-e-vírgula.</p>
    <form method="post" action="upload_csv.php" enctype="multipart/form-data">
      <label class="label">Arquivo CSV</label>
      <input class="input" type="file" name="csv" accept=".csv" required>

      <label class="label">Atribuir automaticamente (opcional): selecione 1 ou mais funcionários</label>
      <select class="input" name="assigned_ids[]" multiple size="5" title="Segure Ctrl (Windows) ou Cmd (Mac) para múltiplos">
        <?php foreach ($funcs as $f): ?>
          <option value="<?=$f['id']?>"><?=htmlspecialchars($f['name'])?> (ID <?=$f['id']?>)</option>
        <?php endforeach; ?>
      </select>
      <small class="muted">Se não selecionar ninguém: round-robin entre <em>todos</em> os funcionários. Se selecionar 1: todos os registros vão para ele. Se selecionar 2+: distribuição igual entre os selecionados.</small>

      <div class="actions">
        <button class="btn" type="submit">Enviar & Importar</button>
      </div>
    </form>
  </div>

  <div class="card">
    <h3>Leads (recentes)</h3>
    <table class="table">
      <thead><tr>
        <th>ID</th><th>Nome</th><th>Telefone</th><th>Escola</th><th>Série</th><th>Status</th><th>Ações</th><th>Responsável</th><th>Criado</th>
      </tr></thead>
      <tbody>
      <?php foreach ($allLeads as $l): ?>
        <tr>
          <td><?=$l['id']?></td>
          <td><?=htmlspecialchars($l['nome'])?></td>
          <td><?=htmlspecialchars($l['telefone'])?></td>
          <td><?=htmlspecialchars($l['escola'] ?? '')?></td>
          <td><?=htmlspecialchars($l['serie'] ?? '')?></td>
          <td><?=status_badge($l['status'] ?? null)?></td>
          <td>
            <form method="post" action="update_status.php" class="inline chips">
              <input type="hidden" name="lead_id" value="<?=$l['id']?>">
              <button class="chip chip-green"   name="status" value="aceito"      title="Aluno aceitou">Aceito</button>
              <button class="chip chip-yellow"  name="status" value="interessado" title="Interessado (por enquanto não)">Interesse</button>
              <button class="chip chip-red"     name="status" value="nao_quero"   title="Aluno não quer">Não quer</button>
            </form>
          </td>
          <td><?=htmlspecialchars($l['responsavel'] ?? '—')?></td>
          <td><?=htmlspecialchars($l['created_at'])?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="card">
    <h3>Meus Leads</h3>
    <?php if (!$myLeads): ?>
      <p class="muted">Nenhum lead atribuído ainda.</p>
    <?php else: ?>
      <table class="table">
        <thead><tr>
          <th>ID</th><th>Nome</th><th>Telefone</th><th>Escola</th><th>Série</th><th>Status</th><th>Ações</th><th>Criado</th>
        </tr></thead>
        <tbody>
        <?php foreach ($myLeads as $l): ?>
          <tr>
            <td><?=$l['id']?></td>
            <td><?=htmlspecialchars($l['nome'])?></td>
            <td><?=htmlspecialchars($l['telefone'])?></td>
            <td><?=htmlspecialchars($l['escola'] ?? '')?></td>
            <td><?=htmlspecialchars($l['serie'] ?? '')?></td>
            <td><?=status_badge($l['status'] ?? null)?></td>
            <td>
              <form method="post" action="update_status.php" class="inline chips">
                <input type="hidden" name="lead_id" value="<?=$l['id']?>">
                <button class="chip chip-green"   name="status" value="aceito"      title="Aluno aceitou">Aceito</button>
                <button class="chip chip-yellow"  name="status" value="interessado" title="Interessado (por enquanto não)">Interesse</button>
                <button class="chip chip-red"     name="status" value="nao_quero"   title="Aluno não quer">Não quer</button>
              </form>
            </td>
            <td><?=htmlspecialchars($l['created_at'])?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
<?php endif; ?>

</main></body></html>
