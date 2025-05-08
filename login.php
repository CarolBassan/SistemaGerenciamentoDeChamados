<?php
session_start();
include 'conexao.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $senha = md5($_POST['senha']);

    $stmt = $conn->prepare("SELECT id, nome, nivel_acesso FROM usuarios WHERE email = ? AND senha = ?");
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $nome, $nivel);
        $stmt->fetch();
        $_SESSION['id'] = $id;
        $_SESSION['nome'] = $nome;
        $_SESSION['nivel'] = $nivel;

        $pagina = 'login';
        $op = 'login';
        $log = $conn->prepare("INSERT INTO log_acesso (id_usuario, pagina_acessada, operacao) VALUES (?, ?, ?)");
        $log->bind_param("iss", $id, $pagina, $op);
        $log->execute();

        header("Location: principal.php");
        exit();
    } else {
        echo "<p>Usuário ou senha inválidos!</p>";
    }
}
?>
<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="senha" placeholder="Senha" required><br>
    <input type="submit" value="Entrar">
</form>