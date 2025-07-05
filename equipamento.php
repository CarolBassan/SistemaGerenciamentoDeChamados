<?php
// Habilitar relatório de erros para desenvolvimento
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclui o arquivo de conexão
require_once 'conexao.php';

// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Verificar nível de acesso
$nivel_necessario = 1; // Nível técnico para gerenciar equipamentos
if ($_SESSION['nivel'] < $nivel_necessario) {
    header("Location: acesso_negado.php");
    exit();
}

// Adicionar novo equipamento
if (isset($_POST['add_eqp'])) {
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $id_laboratorio = intval($_POST['id_laboratorio']);
    $numero_patrimonio = $conn->real_escape_string($_POST['numero_patrimonio']);
    $tipo = $conn->real_escape_string($_POST['tipo']);

    $stmt = $conn->prepare("INSERT INTO equipamentos (descricao, marca, id_laboratorio, numero_patrimonio, tipo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $descricao, $marca, $id_laboratorio, $numero_patrimonio, $tipo);
    
    if($stmt->execute()) {
        $msg = "Equipamento cadastrado com sucesso!";
        
        // Registrar log
        $pagina = 'equipamento';
        $op = 'cadastro';
        $log = $conn->prepare("INSERT INTO log_acesso (id_usuario, pagina_acessada, operacao) VALUES (?, ?, ?)");
        $log->bind_param("iss", $_SESSION['id'], $pagina, $op);
        $log->execute();
    } else {
        $erro = "Erro ao cadastrar equipamento: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipamentos - Sistema CIET</title>
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

    .modal-content {
        border-radius: 10px;
        overflow: hidden;
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
                <h2 class="mb-1"><i class="bi bi-pc-display"></i> Gerenciamento de Equipamentos</h2>
                <p class="text-muted mb-0">Gerencie todos os equipamentos do sistema</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoEquipamentoModal">
                <i class="bi bi-plus-circle me-1"></i> Novo Equipamento
            </button>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Lista de Equipamentos</h4>
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
                                <th>ID</th>
                                <th>Descrição</th>
                                <th>Marca</th>
                                <th>Tipo</th>
                                <th>Patrimônio</th>
                                <th>Laboratório</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = $conn->query("SELECT e.*, l.nome as laboratorio 
                                                FROM equipamentos e 
                                                LEFT JOIN laboratorios l ON e.id_laboratorio = l.id 
                                                ORDER BY e.descricao");
                            while($e = $res->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$e['id']}</td>
                                        <td>".htmlspecialchars($e['descricao'])."</td>
                                        <td>".htmlspecialchars($e['marca'])."</td>
                                        <td>".htmlspecialchars($e['tipo'])."</td>
                                        <td>".htmlspecialchars($e['numero_patrimonio'])."</td>
                                        <td>".htmlspecialchars($e['laboratorio'] ?? 'Não especificado')."</td>
                                        <td>
                                            <a href='#' class='btn btn-sm btn-info me-1' title='Visualizar'><i class='bi bi-eye'></i></a>
                                            ".($_SESSION['nivel'] >= 1 ? "<a href='#' class='btn btn-sm btn-warning' title='Editar'><i class='bi bi-pencil'></i></a>" : "")."
                                        </td>
                                    </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Equipamento -->
    <div class="modal fade" id="novoEquipamentoModal" tabindex="-1" aria-labelledby="novoEquipamentoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novoEquipamentoModalLabel">Novo Equipamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição *</label>
                            <input type="text" class="form-control" id="descricao" name="descricao" required>
                        </div>
                        <div class="mb-3">
                            <label for="marca" class="form-label">Marca *</label>
                            <input type="text" class="form-control" id="marca" name="marca" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo *</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="Computador">Computador</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Impressora">Impressora</option>
                                <option value="Projetor">Projetor</option>
                                <option value="Roteador">Roteador</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="numero_patrimonio" class="form-label">Número de Patrimônio</label>
                            <input type="text" class="form-control" id="numero_patrimonio" name="numero_patrimonio">
                        </div>
                        <div class="mb-3">
                            <label for="id_laboratorio" class="form-label">Laboratório *</label>
                            <select class="form-select" id="id_laboratorio" name="id_laboratorio" required>
                                <option value="">Selecione um laboratório</option>
                                <?php
                                $laboratorios = $conn->query("SELECT id, nome FROM laboratorios ORDER BY nome");
                                while($lab = $laboratorios->fetch_assoc()) {
                                    echo "<option value='{$lab['id']}'>".htmlspecialchars($lab['nome'])."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="add_eqp" class="btn btn-primary">Cadastrar</button>
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