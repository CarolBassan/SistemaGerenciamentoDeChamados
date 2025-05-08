<?php
include 'conexao.php';
session_start();
if (!isset($_SESSION['id'])) header("Location: login.php");
echo "<h2>Consultas</h2><form method='GET'><input type='hidden' name='page' value='consulta'><input name='termo' placeholder='Buscar'><button>Buscar</button></form>";
if (isset($_GET['termo'])) {
    $termo = '%' . $conn->real_escape_string($_GET['termo']) . '%';
    $stmt = $conn->prepare("SELECT * FROM chamados WHERE titulo LIKE ? OR descricao LIKE ?");
    $stmt->bind_param("ss", $termo, $termo);
    $stmt->execute();
    $res = $stmt->get_result();
    echo "<table border='1'><tr><th>Título</th><th>Descrição</th><th>Status</th></tr>";
    while($row = $res->fetch_assoc()) {
        echo "<tr><td>{$row['titulo']}</td><td>{$row['descricao']}</td><td>{$row['status']}</td></tr>";
    }
    echo "</table>";
}
?>