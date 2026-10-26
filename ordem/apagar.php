<?php
require_once __DIR__ . '/../conexao.php'; exigir_login();
$id=(int)($_GET['id'] ?? 0);
if ($id > 0) { $stmt=$conn->prepare('DELETE FROM ordens_servico WHERE id=?'); $stmt->bind_param('i',$id); $stmt->execute(); }
header('Location: listar.php'); exit;
