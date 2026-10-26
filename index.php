<?php
require_once __DIR__ . '/conexao.php';
if (empty($_SESSION['usuario_id'])) {
    header('Location: login/login.php');
    exit;
}
$clientes = (int)$conn->query("SELECT COUNT(*) total FROM clientes")->fetch_assoc()['total'];
$equipamentos = (int)$conn->query("SELECT COUNT(*) total FROM equipamentos")->fetch_assoc()['total'];
$ordens = (int)$conn->query("SELECT COUNT(*) total FROM ordens_servico")->fetch_assoc()['total'];
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>TechHelp</title><link rel="stylesheet" href="css/style.css"></head><body>
<header class="topbar"><a class="brand" href="index.php">TechHelp</a><nav><a href="cliente/listar.php">Clientes</a><a href="equipamento/listar.php">Equipamentos</a><a href="ordem/listar.php">Ordens de serviço</a><a href="login/logout.php">Sair</a></nav></header>
<main class="container"><section class="hero"><p class="eyebrow">PAINEL DE CONTROLE</p><h1>Olá, <?= h($_SESSION['nome'] ?? 'usuário') ?>!</h1><p>Gerencie clientes, equipamentos e ordens de serviço em um só lugar.</p></section>
<div class="stats"><article class="stat"><span>Clientes</span><strong><?= $clientes ?></strong><a href="cliente/listar.php">Gerenciar →</a></article><article class="stat"><span>Equipamentos</span><strong><?= $equipamentos ?></strong><a href="equipamento/listar.php">Gerenciar →</a></article><article class="stat"><span>Ordens de serviço</span><strong><?= $ordens ?></strong><a href="ordem/listar.php">Gerenciar →</a></article></div>
<section class="panel"><h2>Acesso rápido</h2><div class="actions"><a class="button" href="cliente/inserir.php">+ Novo cliente</a><a class="button" href="equipamento/inserir.php">+ Novo equipamento</a><a class="button" href="ordem/inserir.php">+ Nova ordem</a></div></section></main></body></html>
