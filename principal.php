<?php
include 'conexao.php';
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
echo "<h1>Bem-vindo, {$_SESSION['nome']}</h1>";
echo "<a href='laboratorio.php'>Laboratórios</a> | <a href='equipamento.php'>Equipamentos</a> | <a href='chamado.php'>Chamados</a> | <a href='log.php'>Logs</a> | <a href='consulta.php'>Consultas</a> | <a href='logout.php'>Sair</a>";
?>