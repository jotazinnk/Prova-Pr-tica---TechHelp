<?php
require_once __DIR__ . '/../conexao.php'; exigir_login();
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);
$rows = $conn->query("SELECT id AS `id`, nome AS `Nome`, email AS `E-mail`, telefone AS `Telefone` FROM clientes  ORDER BY id DESC");
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Clientes | TechHelp</title><link rel="stylesheet" href="../css/style.css"></head><body>
<header class="topbar"><a class="brand" href="../index.php">TechHelp</a><nav><a href="../index.php">Painel</a><a href="../cliente/listar.php">Clientes</a><a href="../equipamento/listar.php">Equipamentos</a><a href="../ordem/listar.php">Ordens</a><a href="../login/logout.php">Sair</a></nav></header>
<main class="container"><?php if($flash): ?><div class="notice"><?= h($flash) ?></div><?php endif; ?><div class="page-head"><h1>Clientes</h1><a class="button" href="inserir.php">+ Novo</a></div>
<div class="table-wrap"><table><thead><tr><th>id</th><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Ações</th></tr></thead><tbody><?php if($rows->num_rows === 0): ?><tr><td class="empty" colspan="5">Nenhum registro cadastrado.</td></tr><?php else: while($r = $rows->fetch_assoc()): ?><tr><td><?= h($r['id']) ?></td><td><?= h($r['Nome']) ?></td><td><?= h($r['E-mail']) ?></td><td><?= h($r['Telefone']) ?></td><td><div class="table-actions"><a href="atualizar.php?id=<?= (int)$r['id'] ?>">Editar</a><a href="apagar.php?id=<?= (int)$r['id'] ?>" onclick="return confirm('Confirma a exclusão deste registro?')">Apagar</a></div></td></tr><?php endwhile; endif; ?></tbody></table></div></main></body></html>
