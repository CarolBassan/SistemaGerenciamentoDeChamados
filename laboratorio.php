<?php
// Habilitar relatório de erros para desenvolvimento
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclui o arquivo de conexão
require_once 'conexao.php';

// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Obtém a conexão com o banco de dados
$conexao = Conexao::getInstance();
$conn = $conexao->getConnection();

// Verifica se a conexão foi estabelecida
if (!$conn) {
    die("Erro na conexão com o banco de dados");
}

// Processa o formulário de cadastro
if (isset($_POST['add_lab'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $localizacao = $conn->real_escape_string($_POST['localizacao']);
    $responsavel = $conn->real_escape_string($_POST['responsavel']);

    $stmt = $conn->prepare("INSERT INTO laboratorios (nome, localizacao, responsavel) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $localizacao, $responsavel);
    
    if($stmt->execute()) {
        $msg = "Laboratório cadastrado com sucesso!";
    } else {
        $erro = "Erro ao cadastrar laboratório: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratórios - Sistema CIET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #34495e;
        --accent-color: #3498db;
        --light-color: #ecf0f1;
        --dark-color: #2c3e50;
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f5f7fa;
    }

    .main-content {
        padding: 30px;
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
        border: none;
        margin-bottom: 25px;
    }

    .card:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 20px;
    }

    .card-body {
        padding: 25px;
    }

    .btn-primary {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
    }

    .btn-primary:hover {
        background-color: #2980b9;
        border-color: #2980b9;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th {
        background-color: var(--primary-color);
        color: white;
        font-weight: 500;
    }

    .table td,
    .table th {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.1);
    }

    .alert {
        border-radius: 8px;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #ddd;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 15px;
        }
    }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="main-content">
        <?php if(isset($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if(isset($erro)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $erro; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="bi bi-building"></i> Gerenciamento de Laboratórios</h2>
                <p class="text-muted mb-0">Gerencie todos os laboratórios do sistema</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoLaboratorioModal">
                <i class="bi bi-plus-circle me-1"></i> Novo Laboratório
            </button>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Lista de Laboratórios</h4>
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control" placeholder="Pesquisar...">
                    <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Localização</th>
                                <th>Responsável</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = $conn->query("SELECT * FROM laboratorios ORDER BY nome");
                            if ($res->num_rows > 0):
                                while($l = $res->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($l['nome']); ?></td>
                                <td><?php echo htmlspecialchars($l['localizacao']); ?></td>
                                <td><?php echo htmlspecialchars($l['responsavel']); ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info me-1" title="Visualizar"><i
                                            class="bi bi-eye"></i></a>
                                    <?php if($_SESSION['nivel'] >= 1): ?>
                                    <a href="#" class="btn btn-sm btn-warning" title="Editar"><i
                                            class="bi bi-pencil"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="4" class="text-center">Nenhum laboratório cadastrado.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Laboratório -->
    <div class="modal fade" id="novoLaboratorioModal" tabindex="-1" aria-labelledby="novoLaboratorioModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novoLaboratorioModalLabel">Novo Laboratório</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome *</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="localizacao" class="form-label">Localização *</label>
                            <input type="text" class="form-control" id="localizacao" name="localizacao" required>
                        </div>
                        <div class="mb-3">
                            <label for="responsavel" class="form-label">Responsável *</label>
                            <input type="text" class="form-control" id="responsavel" name="responsavel" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="add_lab" class="btn btn-primary">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Script para exibir mensagens de alerta temporariamente
    document.addEventListener('DOMContentLoaded', function() {
        // Fechar alertas automaticamente
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Animação para os cards
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease ' + (index * 0.1) + 's';

            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    });
    </script>
</body>

</html>