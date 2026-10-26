<?php
require_once __DIR__ . '/../conexao.php';
if (!empty($_SESSION['usuario_id'])) { header('Location: ../index.php'); exit; }
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $stmt = $conn->prepare('SELECT id, nome, senha FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email); $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int)$usuario['id']; $_SESSION['nome'] = $usuario['nome'];
        header('Location: ../index.php'); exit;
    }
    $erro = 'E-mail ou senha inválidos.';
}
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Entrar | TechHelp</title><link rel="stylesheet" href="../css/style.css"></head><body><main class="auth-wrap"><section class="auth-card"><a class="brand" href="../index.php">TechHelp</a><h1>Bem-vindo de volta</h1><p>Entre para acessar o painel de assistência técnica.</p><?php if($erro): ?><div class="notice"><?= h($erro) ?></div><?php endif; ?><form method="post"><div class="field"><label for="email">E-mail</label><input id="email" name="email" type="email" required autocomplete="email"></div><div class="field"><label for="senha">Senha</label><input id="senha" name="senha" type="password" required autocomplete="current-password"></div><button type="submit" class="button">Entrar</button></form><p style="margin-top:20px">Ainda não tem conta? <a href="registro.php">Criar conta</a></p></section></main></body></html>
