<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$host = 'localhost:3307';
$usuario = 'root';
$senha = '';
$banco = 'techhelp';

try {
    $conn = new mysqli($host, $usuario, $senha, $banco);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Não foi possível conectar ao banco. Importe banco.sql e confira conexao.php.');
}
if (session_status() === PHP_SESSION_NONE) session_start();

function exigir_login(): void {
    if (empty($_SESSION['usuario_id'])) {
        header('Location: /TechHelp/login/login.php');
        exit;
    }
}
function h($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
