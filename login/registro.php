<?php
require_once __DIR__ . '/../conexao.php';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? ''); $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? ''; $confirmacao = $_POST['confirmacao'] ?? '';
    if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erro = 'Informe nome e e-mail válidos.';
    elseif (strlen($senha) < 8) $erro = 'A senha precisa ter pelo menos 8 caracteres.';
    elseif ($senha !== $confirmacao) $erro = 'As senhas não conferem.';
    else {
        $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email); $stmt->execute();
        if ($stmt->get_result()->num_rows) $erro = 'Este e-mail já está cadastrado.';
        else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO usuarios (nome,email,senha) VALUES (?,?,?)');
            $stmt->bind_param('sss', $nome, $email, $hash); $stmt->execute();
            $_SESSION['usuario_id'] = $conn->insert_id; $_SESSION['nome'] = $nome;
            session_regenerate_id(true); header('Location: ../index.php'); exit;
        }
    }
}
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Criar conta | TechHelp</title><link rel="stylesheet" href="../css/style.css"></head><body><main class="auth-wrap"><section class="auth-card"><a class="brand" href="../index.php">TechHelp</a><h1>Criar conta</h1><p>Cadastre-se para usar o TechHelp.</p><?php if($erro): ?><div class="notice"><?= h($erro) ?></div><?php endif; ?><form method="post"><div class="field"><label for="nome">Nome</label><input id="nome" name="nome" required value="<?= h($_POST['nome'] ?? '') ?>"></div><div class="field"><label for="email">E-mail</label><input id="email" name="email" type="email" required value="<?= h($_POST['email'] ?? '') ?>"></div><div class="field"><label for="senha">Senha (mínimo 8 caracteres)</label><input id="senha" name="senha" type="password" minlength="8" required></div><div class="field"><label for="confirmacao">Confirmar senha</label><input id="confirmacao" name="confirmacao" type="password" minlength="8" required></div><button type="submit" class="button">Criar conta</button></form><p style="margin-top:20px">Já tem conta? <a href="login.php">Entrar</a></p></section></main></body></html>
