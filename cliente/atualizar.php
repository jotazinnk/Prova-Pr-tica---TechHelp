<?php
require_once __DIR__ . '/../conexao.php'; exigir_login();
$id = (int)($_GET['id'] ?? 0);
$editar = true && $id > 0;
$data = [];


if ($editar) {
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->bind_param('i', $id); $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    if (!$data) { http_response_code(404); exit('Registro não encontrado.'); }
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    $data['nome'] = trim((string)($_POST['nome'] ?? '')); $data['email'] = trim((string)($_POST['email'] ?? '')); $data['telefone'] = trim((string)($_POST['telefone'] ?? '')); $data['cpf'] = trim((string)($_POST['cpf'] ?? '')); $data['endereco'] = trim((string)($_POST['endereco'] ?? '')); 
    if ($data['nome'] === '' ) $erro = 'Preencha os campos obrigatórios.';
    else {
        try {
            if ($editar) {
                $stmt = $conn->prepare("UPDATE clientes SET nome = ?, email = ?, telefone = ?, cpf = ?, endereco = ? WHERE id = ?");
                $types = 'sssssi'; $vals = [$data['nome'], $data['email'], $data['telefone'], $data['cpf'], $data['endereco'], $id];
            } else {
                $stmt = $conn->prepare("INSERT INTO clientes (nome, email, telefone, cpf, endereco) VALUES (?, ?, ?, ?, ?)");
                $types = 'sssss'; $vals = [$data['nome'], $data['email'], $data['telefone'], $data['cpf'], $data['endereco']];
            }
            $stmt->bind_param($types, ...$vals); $stmt->execute();
            header('Location: listar.php'); exit;
        } catch (mysqli_sql_exception $e) { $erro = 'Não foi possível salvar. Confira os dados relacionados e tente novamente.'; }
    }
}
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $editar ? 'Editar' : 'Novo' ?> cliente | TechHelp</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="topbar"><a class="brand" href="../index.php">TechHelp</a><nav><a href="../index.php">Painel</a><a href="../cliente/listar.php">Clientes</a><a href="../equipamento/listar.php">Equipamentos</a><a href="../ordem/listar.php">Ordens</a><a href="../login/logout.php">Sair</a></nav></header>
<main class="container"><div class="page-head"><h1><?= $editar ? 'Editar' : 'Novo' ?> cliente</h1><a class="button secondary" href="listar.php">Voltar</a></div><section class="form-card"><?php if($erro): ?><div class="notice"><?= h($erro) ?></div><?php endif; ?><form method="post"><div class="form-grid"><div class="field"><label for="nome">Nome</label><input type="text" name="nome" id="nome" value="<?= h($data['nome'] ?? '') ?>" required></div><div class="field"><label for="email">E-mail</label><input type="email" name="email" id="email" value="<?= h($data['email'] ?? '') ?>"></div><div class="field"><label for="telefone">Telefone</label><input type="tel" name="telefone" id="telefone" value="<?= h($data['telefone'] ?? '') ?>"></div><div class="field"><label for="cpf">CPF</label><input type="text" name="cpf" id="cpf" value="<?= h($data['cpf'] ?? '') ?>"></div><div class="field"><label for="endereco">Endereço</label><input type="text" name="endereco" id="endereco" value="<?= h($data['endereco'] ?? '') ?>"></div></div><div class="form-actions"><button type="submit">Salvar</button><a class="button secondary" href="listar.php">Cancelar</a></div></form></section></main></body></html>
