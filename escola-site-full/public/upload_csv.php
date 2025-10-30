<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
  header('Location: dashboard.php?err=Falha+no+upload+do+CSV');
  exit;
}

$fname = $_FILES['csv']['name'];
$ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
if ($ext !== 'csv') {
  header('Location: dashboard.php?err=Arquivo+precisa+ser+.csv');
  exit;
}

$pdo = get_pdo();

// ==== Seleção de funcionários ====
// Recebe múltiplos IDs do POST (assigned_ids[])
$selectedIds = array_filter(array_map('intval', $_POST['assigned_ids'] ?? []));

// Filtra por funcionários válidos existentes
$selected = [];
if (!empty($selectedIds)) {
  $in  = implode(',', array_fill(0, count($selectedIds), '?'));
  $sql = "SELECT id FROM users WHERE role='funcionario' AND id IN ($in) ORDER BY id ASC";
  $st  = $pdo->prepare($sql);
  $st->execute($selectedIds);
  $selected = $st->fetchAll(PDO::FETCH_COLUMN);
}

// Modo de atribuição:
// - Se $selected tiver 1 ID → atribui TUDO para esse funcionário (fixo)
// - Se $selected tiver 2+ IDs → round-robin entre os selecionados
// - Se $selected estiver vazio → round-robin entre TODOS os funcionários
$assignFixed = null;
$funcQueue   = [];

if (count($selected) === 1) {
  $assignFixed = (int)$selected[0];
} elseif (count($selected) > 1) {
  $funcQueue = array_map('intval', $selected);
} else {
  // Fallback: todos os funcionários
  $funcQueue = $pdo->query("SELECT id FROM users WHERE role='funcionario' ORDER BY id ASC")->fetchAll(PDO::FETCH_COLUMN);
  $funcQueue = array_map('intval', $funcQueue);
}

$funcCount = count($funcQueue);
$cursor = 0;

// ===== Helpers para CSV =====
function normalize_str($s) {
  $s = mb_strtolower(trim((string)$s), 'UTF-8');
  $map = ['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a',
          'é'=>'e','ê'=>'e','è'=>'e','ë'=>'e',
          'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
          'ó'=>'o','ô'=>'o','õ'=>'o','ò'=>'o','ö'=>'o',
          'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
          'ç'=>'c'];
  $s = strtr($s, $map);
  $s = preg_replace('/\s+/', ' ', $s);
  return $s;
}

$pdo->beginTransaction();
try {
  $tmp = $_FILES['csv']['tmp_name'];
  $h = fopen($tmp, 'r');
  if ($h === false) throw new RuntimeException('Não foi possível abrir o arquivo.');

  // Detecta delimitador (vírgula ou ponto-e-vírgula) pela 1ª linha
  $peek = fgets($h);
  if ($peek === false) throw new RuntimeException('CSV vazio.');
  $comma = substr_count($peek, ',');
  $semi  = substr_count($peek, ';');
  $delim = ($semi > $comma) ? ';' : ',';
  rewind($h);

  // Lê cabeçalho
  $header = fgetcsv($h, 0, $delim);
  if (!$header) throw new RuntimeException('Cabeçalho ausente.');
  $norm = array_map('normalize_str', $header);

  // Esperado (flexível): nome | telefone | escola | serie
  $want = ['nome'=>-1,'telefone'=>-1,'escola'=>-1,'serie'=>-1];
  foreach ($norm as $i => $col) {
    if ($col === 'nome') { $want['nome'] = $i; continue; }
    if ($col === 'telefone' or $col === 'celular' or $col === 'telefone celular' or $col == 'telefone1') { $want['telefone'] = $i; continue; }
    if ($col === 'escola' or $col === 'nome da escola' or $col === 'colegio' or $col === 'colegio nome' or $col === 'colegio escola' or $col === 'colegio-escola') { $want['escola'] = $i; continue; }
    if ($col === 'serie' or $col === 'serie do aluno' or $col === 'serie que o aluno esta' or $col === 'serie/ano' or $col === 'ano/serie' or $col === 'ano serie' or $col === 'ano') { $want['serie'] = $i; continue; }
  }
  if (in_array(-1, $want, true)) {
    throw new RuntimeException('Cabeçalho esperado: nome, telefone, escola, serie (variações são aceitas).');
  }

  $ins = $pdo->prepare("INSERT INTO leads (nome,telefone,escola,serie,assigned_to) VALUES (?,?,?,?,?)");
  $count = 0;

  while (($row = fgetcsv($h, 0, $delim)) !== false) {
    if (!$row) continue;

    $nome    = isset($row[$want['nome']]) ? trim($row[$want['nome']]) : '';
    $tel     = isset($row[$want['telefone']]) ? trim($row[$want['telefone']]) : '';
    $escola  = isset($row[$want['escola']]) ? trim($row[$want['escola']]) : '';
    $serie   = isset($row[$want['serie']]) ? trim($row[$want['serie']]) : '';

    // Ignora linhas completamente vazias
    if ($nome === '' && $tel === '' && $escola === '' && $serie === '') continue;

    // Define responsável
    if ($assignFixed !== null) {
      $assignedTo = $assignFixed;
    } else {
      if ($funcCount > 0) {
        $assignedTo = (int)$funcQueue[$cursor];
        $cursor = ($cursor + 1) % $funcCount;
      } else {
        $assignedTo = null;
      }
    }

    $ins->execute([$nome, $tel, $escola, $serie, $assignedTo]);
    $count++;
  }
  fclose($h);
  $pdo->commit();

  // Mensagem descritiva
  if ($assignFixed !== null) {
    $msg = 'Importado: '.$count.' registro(s) (atribuído fixo ao funcionário ID '.$assignFixed.')';
  } elseif ($funcCount > 0) {
    $msg = 'Importado: '.$count.' registro(s) (round-robin entre '.count($funcQueue).' funcionário(s) selecionado(s))';
  } else {
    $msg = 'Importado: '.$count.' registro(s) (sem funcionários — leads não atribuídos)';
  }

  header('Location: dashboard.php?msg='.urlencode($msg));
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  header('Location: dashboard.php?err='.urlencode('Erro ao importar: '.$e->getMessage()));
}
