<?php
include 'conexao.php';
session_start();
if (!isset($_SESSION['id'])) header("Location: login.php");
echo "<h2>Laboratórios</h2><form method='POST'><input name='nome' placeholder='Nome'><input name='localizacao' placeholder='Local'><input name='responsavel' placeholder='Responsável'><button name='add_lab'>Cadastrar</button></form>";
if (isset($_POST['add_lab'])) {
    $stmt = $conn->prepare("INSERT INTO laboratorios (nome, localizacao, responsavel) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $_POST['nome'], $_POST['localizacao'], $_POST['responsavel']);
    $stmt->execute();
}
$res = $conn->query("SELECT * FROM laboratorios");
echo "<table border='1'><tr><th>Nome</th><th>Local</th><th>Responsável</th></tr>";
while($l = $res->fetch_assoc()) {
    echo "<tr><td>{$l['nome']}</td><td>{$l['localizacao']}</td><td>{$l['responsavel']}</td></tr>";
}
echo "</table>";
?>