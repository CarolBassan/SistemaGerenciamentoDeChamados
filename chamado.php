<?php
include 'conexao.php';
session_start();
if (!isset($_SESSION['id'])) header("Location: login.php");
echo "<h2>Chamados</h2><form method='POST'><input name='titulo' placeholder='Título'><input name='descricao' placeholder='Descrição'><input name='id_equipamento' placeholder='ID Equipamento'><input name='status' placeholder='Status'><button name='add_chamado'>Cadastrar</button></form>";
if (isset($_POST['add_chamado'])) {
    $stmt = $conn->prepare("INSERT INTO chamados (titulo, descricao, id_equipamento, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $_POST['titulo'], $_POST['descricao'], $_POST['id_equipamento'], $_POST['status']);
    $stmt->execute();
}
$res = $conn->query("SELECT * FROM chamados");
echo "<table border='1'><tr><th>Título</th><th>Descrição</th><th>ID Equipamento</th><th>Status</th></tr>";
while($c = $res->fetch_assoc()) {
    echo "<tr><td>{$c['titulo']}</td><td>{$c['descricao']}</td><td>{$c['id_equipamento']}</td><td>{$c['status']}</td></tr>";
}
echo "</table>";
?>