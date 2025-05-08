<?php
include 'conexao.php';
session_start();
if (!isset($_SESSION['id'])) header("Location: login.php");
$res = $conn->query("SELECT l.*, u.nome FROM log_acesso l JOIN usuarios u ON l.id_usuario = u.id ORDER BY l.data_hora DESC");
echo "<h2>Logs de Acesso</h2><table border='1'><tr><th>Usuário</th><th>Página</th><th>Operação</th><th>Data/Hora</th></tr>";
while($log = $res->fetch_assoc()) {
    echo "<tr><td>{$log['nome']}</td><td>{$log['pagina_acessada']}</td><td>{$log['operacao']}</td><td>{$log['data_hora']}</td></tr>";
}
echo "</table>";
?>