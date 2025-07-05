<?php
session_start();

// Define um usuário padrão automaticamente
$_SESSION['id'] = 1;
$_SESSION['nome'] = 'Administrador';
$_SESSION['nivel'] = 2; // Nível de administrador

// Redireciona direto para a página principal
header("Location: principal.php");
exit();
?>