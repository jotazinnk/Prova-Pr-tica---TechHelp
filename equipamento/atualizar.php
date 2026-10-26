<?php
require_once __DIR__ . '/../conexao.php'; exigir_login();
$id = (int)($_GET['id'] ?? 0);
$editar = true && $id > 0;
$data = [];

$opts_cliente_id = $conn->query("SELECT id, nome FROM clientes ORDER BY nome")->fetch_all(MYSQLI_ASSOC);

if ($editar) {
    $stmt = $conn->prepare("SELECT * FROM equipamentos WHERE id = ?");
    $stmt->bind_param('i', $id); $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    if (!$data) { http_response_code(404); exit('Registro não encontrado.'); }
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    $data['cliente_id'] = trim((string)($_POST['cliente_id'] ?? '')); $data['tipo'] = trim((string)($_POST['tipo'] ?? '')); $data['marca'] = trim((string)($_POST['marca'] ?? '')); $data['modelo'] = trim((string)($_POST['modelo'] ?? '')); $data['numero_serie'] = trim((string)($_POST['numero_serie'] ?? '')); $data['descricao'] = trim((string)($_POST['descricao'] ?? '')); 
    if ($data['cliente_id'] === '' || $data['tipo'] === '' ) $erro = 'Preencha os campos obrigatórios.';
    else {
        try {
            if ($editar) {
                $stmt = $conn->prepare("UPDATE equipamentos SET cliente_id = ?, tipo = ?, marca = ?, modelo = ?, numero_serie = ?, descricao = ? WHERE id = ?");
                $types = 'isssssi'; $vals = [$data['cliente_id'], $data['tipo'], $data['marca'], $data['modelo'], $data['numero_serie'], $data['descricao'], $id];
            } else {
                $stmt = $conn->prepare("INSERT INTO equipamentos (cliente_id, tipo, marca, modelo, numero_serie, descricao) VALUES (?, ?, ?, ?, ?, ?)");
                $types = 'isssss'; $vals = [$data['cliente_id'], $data['tipo'], $data['marca'], $data['modelo'], $data['numero_serie'], $data['descricao']];
            }
            $stmt->bind_param($types, ...$vals); $stmt->execute();
            header('Location: listar.php'); exit;
        } catch (mysqli_sql_exception $e) { $erro = 'Não foi possível salvar. Confira os dados relacionados e tente novamente.'; }
    }
}
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $editar ? 'Editar' : 'Novo' ?> equipamento | TechHelp</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="topbar"><a class="brand" href="../index.php">TechHelp</a><nav><a href="../index.php">Painel</a><a href="../cliente/listar.php">Clientes</a><a href="../equipamento/listar.php">Equipamentos</a><a href="../ordem/listar.php">Ordens</a><a href="../login/logout.php">Sair</a></nav></header>
<main class="container"><div class="page-head"><h1><?= $editar ? 'Editar' : 'Novo' ?> equipamento</h1><a class="button secondary" href="listar.php">Voltar</a></div><section class="form-card"><?php if($erro): ?><div class="notice"><?= h($erro) ?></div><?php endif; ?><form method="post"><div class="form-grid"><div class="field"><label for="cliente_id">Cliente</label><select name="cliente_id" id="cliente_id" required><option value="">Selecione...</option><?php foreach($opts_cliente_id as $op): ?><option value="<?= (int)$op['id'] ?>" <?= (string)($data['cliente_id'] ?? '') === (string)$op['id'] ? 'selected' : '' ?>><?= h($op['nome'] ?? $op['rotulo'] ?? $op['tipo'] ?? '') ?></option><?php endforeach; ?></select></div><div class="field"><label for="tipo">Tipo de equipamento</label><input type="text" name="tipo" id="tipo" value="<?= h($data['tipo'] ?? '') ?>" required></div><div class="field"><label for="marca">Marca</label><input type="text" name="marca" id="marca" value="<?= h($data['marca'] ?? '') ?>"></div><div class="field"><label for="modelo">Modelo</label><input type="text" name="modelo" id="modelo" value="<?= h($data['modelo'] ?? '') ?>"></div><div class="field"><label for="numero_serie">Número de série</label><input type="text" name="numero_serie" id="numero_serie" value="<?= h($data['numero_serie'] ?? '') ?>"></div><div class="field"><label for="descricao">Descrição</label><textarea name="descricao" id="descricao" rows="3"><?= h($data['descricao'] ?? '') ?></textarea></div></div><div class="form-actions"><button type="submit">Salvar</button><a class="button secondary" href="listar.php">Cancelar</a></div></form></section></main></body></html>
