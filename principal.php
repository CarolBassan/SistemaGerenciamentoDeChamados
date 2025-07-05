<?php
session_start();

// Verificação simplificada (cria usuário se não existir)
if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = 1;
    $_SESSION['nome'] = 'Administrador';
    $_SESSION['nivel'] = 2;
}

// Inicializa a conexão com o banco de dados
require_once 'conexao.php';
$conexao = Conexao::getInstance();
$conn = $conexao->getConnection();

// Verifica se a conexão foi estabelecida
if (!$conn) {
    die("Erro na conexão com o banco de dados");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal - Sistema CIET</title>
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

    .sidebar {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .sidebar:hover {
        box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        border-radius: 5px;
        margin: 5px 0;
        padding: 10px 15px;
        transition: all 0.3s;
    }

    .sidebar .nav-link:hover {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }

    .sidebar .nav-link.active {
        color: #fff;
        background-color: var(--accent-color);
        font-weight: 500;
    }

    .sidebar .nav-link i {
        margin-right: 10px;
        font-size: 1.1rem;
    }

    .sidebar-header {
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .user-profile {
        text-align: center;
        padding: 20px 0;
    }

    .user-profile i {
        font-size: 3.5rem;
        color: var(--accent-color);
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        padding: 15px;
        margin-bottom: 10px;
    }

    .main-content {
        padding: 30px;
    }

    .welcome-header {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .stats-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
        border: none;
        margin-bottom: 25px;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .stats-card .card-body {
        padding: 25px;
    }

    .stats-card .card-title {
        font-size: 1rem;
        font-weight: 500;
        margin-bottom: 15px;
    }

    .stats-card .card-text {
        font-size: 2.5rem;
        font-weight: 600;
        margin: 10px 0;
    }

    .stats-card a {
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }

    .stats-card a i {
        margin-left: 5px;
        transition: transform 0.3s;
    }

    .stats-card a:hover i {
        transform: translateX(3px);
    }

    .bg-primary {
        background: linear-gradient(135deg, #3498db, #2980b9) !important;
    }

    .bg-success {
        background: linear-gradient(135deg, #2ecc71, #27ae60) !important;
    }

    .bg-warning {
        background: linear-gradient(135deg, #f39c12, #e67e22) !important;
    }

    @media (max-width: 768px) {
        .sidebar {
            min-height: auto;
            width: 100%;
        }

        .main-content {
            padding: 15px;
        }
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 d-none d-md-block sidebar p-0">
                <div class="sidebar-header">
                    <h4 class="text-white">CIET SVS</h4>
                </div>
                <div class="user-profile">
                    <i class="bi bi-person-circle"></i>
                    <p class="mb-0 text-white"><?php echo $_SESSION['nome']; ?></p>
                    <small class="text-muted">Administrador do Sistema</small>
                </div>
                <ul class="nav flex-column px-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="principal.php"><i class="bi bi-house-door"></i> Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laboratorio.php"><i class="bi bi-building"></i> Laboratórios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="equipamento.php"><i class="bi bi-pc-display"></i> Equipamentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chamado.php"><i class="bi bi-ticket-detailed"></i> Chamados</a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 ms-sm-auto main-content">
                <div class="welcome-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="h4 mb-1">Bem-vindo, <?php echo $_SESSION['nome']; ?></h2>
                            <p class="mb-0 text-muted">Aqui está o resumo do sistema hoje</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-dark"><i
                                    class="bi bi-calendar me-2"></i><?php echo date('d/m/Y'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Chamados Abertos -->
                    <div class="col-md-4">
                        <div class="card text-white stats-card bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title">CHAMADOS ABERTOS</h5>
                                        <?php
                                        $res = $conn->query("SELECT COUNT(*) as total FROM chamados WHERE status = 'aberto'");
                                        $row = $res->fetch_assoc();
                                        ?>
                                        <p class="card-text"><?php echo $row['total']; ?></p>
                                    </div>
                                    <div class="icon-circle">
                                        <i class="bi bi-exclamation-triangle fs-1 opacity-75"></i>
                                    </div>
                                </div>
                                <a href="chamado.php" class="text-white">Ver detalhes <i
                                        class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Chamados em Andamento -->
                    <div class="col-md-4">
                        <div class="card text-white stats-card bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title">EM ANDAMENTO</h5>
                                        <?php
                                        $res = $conn->query("SELECT COUNT(*) as total FROM chamados WHERE status = 'em andamento'");
                                        $row = $res->fetch_assoc();
                                        ?>
                                        <p class="card-text"><?php echo $row['total']; ?></p>
                                    </div>
                                    <div class="icon-circle">
                                        <i class="bi bi-gear fs-1 opacity-75"></i>
                                    </div>
                                </div>
                                <a href="chamado.php" class="text-white">Ver detalhes <i
                                        class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Equipamentos Cadastrados -->
                    <div class="col-md-4">
                        <div class="card text-white stats-card bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title">EQUIPAMENTOS</h5>
                                        <?php
                                        $res = $conn->query("SELECT COUNT(*) as total FROM equipamentos");
                                        $row = $res->fetch_assoc();
                                        ?>
                                        <p class="card-text"><?php echo $row['total']; ?></p>
                                    </div>
                                    <div class="icon-circle">
                                        <i class="bi bi-pc-display-horizontal fs-1 opacity-75"></i>
                                    </div>
                                </div>
                                <a href="equipamento.php" class="text-white">Ver detalhes <i
                                        class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção adicional para gráficos/resumo -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title d-flex justify-content-between align-items-center">
                                    <span>Atividade Recente</span>
                                    <a href="#" class="btn btn-sm btn-outline-primary">Ver tudo</a>
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Descrição</th>
                                                <th>Data</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge bg-primary">Chamado</span></td>
                                                <td>Problema com projetor no Lab 3</td>
                                                <td><?php echo date('d/m/Y H:i', strtotime('-1 hour')); ?></td>
                                                <td><span class="badge bg-warning">Em andamento</span></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-success">Equipamento</span></td>
                                                <td>Novo computador cadastrado</td>
                                                <td><?php echo date('d/m/Y H:i', strtotime('-3 hours')); ?></td>
                                                <td><span class="badge bg-success">Concluído</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Efeitos dinâmicos
    document.addEventListener('DOMContentLoaded', function() {
        // Animação dos cards
        const cards = document.querySelectorAll('.stats-card');
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