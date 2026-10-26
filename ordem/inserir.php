<?php
require_once __DIR__ . '/../conexao.php'; exigir_login();
$id = (int)($_GET['id'] ?? 0);
$editar = false && $id > 0;
$data = [];

$opts_cliente_id = $conn->query("SELECT id,nome FROM clientes ORDER BY nome")->fetch_all(MYSQLI_ASSOC);
$opts_equipamento_id = $conn->query("SELECT id, tipo AS nome FROM equipamentos ORDER BY tipo")->fetch_all(MYSQLI_ASSOC);

if ($editar) {
    $stmt = $conn->prepare("SELECT * FROM ordens_servico WHERE id = ?");
    $stmt->bind_param('i', $id); $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    if (!$data) { http_response_code(404); exit('Registro não encontrado.'); }
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    $data['cliente_id'] = trim((string)($_POST['cliente_id'] ?? '')); $data['equipamento_id'] = trim((string)($_POST['equipamento_id'] ?? '')); $data['defeito'] = trim((string)($_POST['defeito'] ?? '')); $data['diagnostico'] = trim((string)($_POST['diagnostico'] ?? '')); $data['servico_realizado'] = trim((string)($_POST['servico_realizado'] ?? '')); $data['status'] = trim((string)($_POST['status'] ?? '')); $data['valor'] = trim((string)($_POST['valor'] ?? '')); $data['data_entrada'] = trim((string)($_POST['data_entrada'] ?? '')); $data['data_saida'] = trim((string)($_POST['data_saida'] ?? '')); if ($data['data_saida'] === '') $data['data_saida'] = null; 
    if ($data['cliente_id'] === '' || $data['equipamento_id'] === '' || $data['defeito'] === '' || $data['status'] === '' || $data['valor'] === '' || $data['data_entrada'] === '' ) $erro = 'Preencha os campos obrigatórios.';
    else {
        try {
            if ($editar) {
                $stmt = $conn->prepare("UPDATE ordens_servico SET cliente_id = ?, equipamento_id = ?, defeito = ?, diagnostico = ?, servico_realizado = ?, status = ?, valor = ?, data_entrada = ?, data_saida = ? WHERE id = ?");
                $types = 'iissssdssi'; $vals = [$data['cliente_id'], $data['equipamento_id'], $data['defeito'], $data['diagnostico'], $data['servico_realizado'], $data['status'], $data['valor'], $data['data_entrada'], $data['data_saida'], $id];
            } else {
                $stmt = $conn->prepare("INSERT INTO ordens_servico (cliente_id, equipamento_id, defeito, diagnostico, servico_realizado, status, valor, data_entrada, data_saida) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $types = 'iissssdss'; $vals = [$data['cliente_id'], $data['equipamento_id'], $data['defeito'], $data['diagnostico'], $data['servico_realizado'], $data['status'], $data['valor'], $data['data_entrada'], $data['data_saida']];
            }
            $stmt->bind_param($types, ...$vals); $stmt->execute();
            header('Location: listar.php'); exit;
        } catch (mysqli_sql_exception $e) { $erro = 'Não foi possível salvar. Confira os dados relacionados e tente novamente.'; }
    }
}
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $editar ? 'Editar' : 'Novo' ?> Ordens de serviço | TechHelp</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="topbar"><a class="brand" href="../index.php">TechHelp</a><nav><a href="../index.php">Painel</a><a href="../cliente/listar.php">Clientes</a><a href="../equipamento/listar.php">Equipamentos</a><a href="../ordem/listar.php">Ordens</a><a href="../login/logout.php">Sair</a></nav></header>
<main class="container"><div class="page-head"><h1><?= $editar ? 'Editar' : 'Novo' ?> ordem de serviço</h1><a class="button secondary" href="listar.php">Voltar</a></div><section class="form-card"><?php if($erro): ?><div class="notice"><?= h($erro) ?></div><?php endif; ?><form method="post"><div class="form-grid"><div class="field"><label for="cliente_id">Cliente</label><select name="cliente_id" id="cliente_id" required><option value="">Selecione...</option><?php foreach($opts_cliente_id as $op): ?><option value="<?= (int)$op['id'] ?>" <?= (string)($data['cliente_id'] ?? '') === (string)$op['id'] ? 'selected' : '' ?>><?= h($op['nome'] ?? $op['rotulo'] ?? $op['tipo'] ?? '') ?></option><?php endforeach; ?></select></div><div class="field"><label for="equipamento_id">Equipamento</label><select name="equipamento_id" id="equipamento_id" required><option value="">Selecione...</option><?php foreach($opts_equipamento_id as $op): ?><option value="<?= (int)$op['id'] ?>" <?= (string)($data['equipamento_id'] ?? '') === (string)$op['id'] ? 'selected' : '' ?>><?= h($op['nome'] ?? $op['rotulo'] ?? $op['tipo'] ?? '') ?></option><?php endforeach; ?></select></div><div class="field"><label for="defeito">Defeito relatado</label><textarea name="defeito" id="defeito" rows="3" required><?= h($data['defeito'] ?? '') ?></textarea></div><div class="field"><label for="diagnostico">Diagnóstico</label><textarea name="diagnostico" id="diagnostico" rows="3"><?= h($data['diagnostico'] ?? '') ?></textarea></div><div class="field"><label for="servico_realizado">Serviço realizado</label><textarea name="servico_realizado" id="servico_realizado" rows="3"><?= h($data['servico_realizado'] ?? '') ?></textarea></div><div class="field"><label for="status">Status</label><select name="status" id="status" required><?php foreach (['Aberta', 'Em andamento', 'Aguardando peça', 'Concluída', 'Cancelada'] as $v): ?><option value="<?= h($v) ?>" <?= ($data['status'] ?? 'Aberta') === $v ? 'selected' : '' ?>><?= h($v) ?></option><?php endforeach; ?></select></div><div class="field"><label for="valor">Valor (R$)</label><input type="number" name="valor" id="valor" value="<?= h($data['valor'] ?? '') ?>" step="0.01" min="0" required></div><div class="field"><label for="data_entrada">Data de entrada</label><input type="date" name="data_entrada" id="data_entrada" value="<?= h($data['data_entrada'] ?? '') ?>" required></div><div class="field"><label for="data_saida">Data de saída</label><input type="date" name="data_saida" id="data_saida" value="<?= h($data['data_saida'] ?? '') ?>"></div></div><div class="form-actions"><button type="submit">Salvar</button><a class="button secondary" href="listar.php">Cancelar</a></div></form></section></main></body></html>
