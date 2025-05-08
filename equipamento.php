<?php
include 'conexao.php';
session_start();
if (!isset($_SESSION['id'])) header("Location: login.php");
echo "<h2>Equipamentos</h2><form method='POST'><input name='descricao' placeholder='Descrição'><input name='marca' placeholder='Marca'><input name='id_laboratorio' placeholder='ID Laboratório'><button name='add_eqp'>Cadastrar</button></form>";
if (isset($_POST['add_eqp'])) {
    $stmt = $conn->prepare("INSERT INTO equipamentos (descricao, marca, id_laboratorio) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $_POST['descricao'], $_POST['marca'], $_POST['id_laboratorio']);
    $stmt->execute();
}
$res = $conn->query("SELECT * FROM equipamentos");
echo "<table border='1'><tr><th>Descrição</th><th>Marca</th><th>ID Laboratório</th></tr>";
while($e = $res->fetch_assoc()) {
    echo "<tr><td>{$e['descricao']}</td><td>{$e['marca']}</td><td>{$e['id_laboratorio']}</td></tr>";
}
echo "</table>";
?>