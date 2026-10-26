<?php
require_once __DIR__ . '/../conexao.php'; exigir_login();
$id=(int)($_GET['id'] ?? 0);
if ($id > 0) { $stmt=$conn->prepare('DELETE FROM clientes WHERE id=?'); $stmt->bind_param('i',$id); try {$stmt->execute();} catch (mysqli_sql_exception $e) { $_SESSION['flash']='Não é possível apagar um cliente que tenha equipamentos ou ordens vinculados.'; } }
header('Location: listar.php'); exit;
