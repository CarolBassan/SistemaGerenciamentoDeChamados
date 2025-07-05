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
$nivel_necessario = 1; // Nível técnico para gerenciar chamados
if ($_SESSION['nivel'] < $nivel_necessario) {
    header("Location: acesso_negado.php");
    exit();
}

// Adicionar novo chamado
if (isset($_POST['add_chamado'])) {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $id_equipamento = intval($_POST['id_equipamento']);
    $status = $conn->real_escape_string($_POST['status']);
    $prioridade = isset($_POST['prioridade']) ? $conn->real_escape_string($_POST['prioridade']) : 'media';
    $id_usuario = $_SESSION['id'];

    $stmt = $conn->prepare("INSERT INTO chamados (titulo, descricao, id_equipamento, status, prioridade, id_usuario) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissi", $titulo, $descricao, $id_equipamento, $status, $prioridade, $id_usuario);
    
    if($stmt->execute()) {
        $msg = "Chamado cadastrado com sucesso!";
        
        // Registrar log
        $pagina = 'chamado';
        $op = 'cadastro';
        $log = $conn->prepare("INSERT INTO log_acesso (id_usuario, pagina_acessada, operacao) VALUES (?, ?, ?)");
        $log->bind_param("iss", $_SESSION['id'], $pagina, $op);
        $log->execute();
    } else {
        $erro = "Erro ao cadastrar chamado: " . $conn->error;
    }
}

// Atualizar status do chamado
if (isset($_POST['atualizar_status']) && isset($_GET['view'])) {
    $novo_status = $conn->real_escape_string($_POST['novo_status']);
    $id_chamado = intval($_GET['view']);
    
    $stmt = $conn->prepare("UPDATE chamados SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $novo_status, $id_chamado);
    
    if($stmt->execute()) {
        $msg = "Status do chamado atualizado com sucesso!";
        // Atualiza a visualização do chamado
        $view_chamado['status'] = $novo_status;
    } else {
        $erro = "Erro ao atualizar status: " . $conn->error;
    }
}

// Visualizar chamado específico
$view_chamado = null;
if(isset($_GET['view'])) {
    $id = intval($_GET['view']);
    $res = $conn->query("SELECT c.*, e.descricao as equipamento, u.nome as usuario 
                        FROM chamados c 
                        LEFT JOIN equipamentos e ON c.id_equipamento = e.id 
                        LEFT JOIN usuarios u ON c.id_usuario = u.id 
                        WHERE c.id = $id");
    $view_chamado = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chamados - Sistema CIET</title>
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

    .badge-danger {
        background-color: var(--danger-color);
    }

    .badge-warning {
        background-color: var(--warning-color);
        color: #000;
    }

    .badge-success {
        background-color: var(--success-color);
    }

    .badge-info {
        background-color: var(--accent-color);
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
                <h2 class="mb-1"><i class="bi bi-ticket-detailed"></i> Gerenciamento de Chamados</h2>
                <p class="text-muted mb-0">Gerencie todos os chamados do sistema</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoChamadoModal">
                <i class="bi bi-plus-circle me-1"></i> Novo Chamado
            </button>
        </div>

        <?php if($view_chamado): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Detalhes do Chamado #<?php echo $view_chamado['id']; ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="text-muted">Título</h5>
                            <p class="fs-5"><?php echo htmlspecialchars($view_chamado['titulo']); ?></p>
                        </div>
                        <div class="mb-3">
                            <h5 class="text-muted">Descrição</h5>
                            <p><?php echo nl2br(htmlspecialchars($view_chamado['descricao'])); ?></p>
                        </div>
                        <div class="mb-3">
                            <h5 class="text-muted">Equipamento</h5>
                            <p><?php echo htmlspecialchars($view_chamado['equipamento'] ?? 'Não especificado'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="text-muted">Status</h5>
                            <?php 
                            $status_class = '';
                            if($view_chamado['status'] == 'aberto') $status_class = 'bg-danger';
                            if($view_chamado['status'] == 'em andamento') $status_class = 'bg-warning';
                            if($view_chamado['status'] == 'concluído') $status_class = 'bg-success';
                            ?>
                            <span
                                class="badge <?php echo $status_class; ?> p-2 fs-6"><?php echo ucfirst($view_chamado['status']); ?></span>
                        </div>
                        <div class="mb-3">
                            <h5 class="text-muted">Prioridade</h5>
                            <?php 
                            $prioridade_class = '';
                            if($view_chamado['prioridade'] == 'alta') $prioridade_class = 'bg-danger';
                            if($view_chamado['prioridade'] == 'media') $prioridade_class = 'bg-warning';
                            if($view_chamado['prioridade'] == 'baixa') $prioridade_class = 'bg-info';
                            ?>
                            <span
                                class="badge <?php echo $prioridade_class; ?> p-2 fs-6"><?php echo ucfirst($view_chamado['prioridade']); ?></span>
                        </div>
                        <div class="mb-3">
                            <h5 class="text-muted">Solicitante</h5>
                            <p><?php echo htmlspecialchars($view_chamado['usuario'] ?? 'Não identificado'); ?></p>
                        </div>
                        <div class="mb-3">
                            <h5 class="text-muted">Data de Abertura</h5>
                            <p><?php echo date('d/m/Y H:i', strtotime($view_chamado['data_abertura'])); ?></p>
                        </div>
                    </div>
                </div>

                <?php if($_SESSION['nivel'] >= 1): ?>
                <hr>
                <h5 class="mb-3">Atualizar Status</h5>
                <form method="POST" class="row g-3">
                    <div class="col-md-4">
                        <select name="novo_status" class="form-select">
                            <option value="aberto" <?php if($view_chamado['status'] == 'aberto') echo 'selected'; ?>>
                                Aberto</option>
                            <option value="em andamento"
                                <?php if($view_chamado['status'] == 'em andamento') echo 'selected'; ?>>Em Andamento
                            </option>
                            <option value="concluído"
                                <?php if($view_chamado['status'] == 'concluído') echo 'selected'; ?>>Concluído</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <button type="submit" name="atualizar_status" class="btn btn-primary">Atualizar</button>
                        <a href="chamado.php" class="btn btn-outline-secondary">Voltar</a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Lista de Chamados</h4>
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
                                <th>Título</th>
                                <th>Equipamento</th>
                                <th>Status</th>
                                <th>Prioridade</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = $conn->query("SELECT c.*, e.descricao as equipamento 
                                                FROM chamados c 
                                                LEFT JOIN equipamentos e ON c.id_equipamento = e.id 
                                                ORDER BY 
                                                    CASE WHEN c.prioridade = 'alta' THEN 1 
                                                         WHEN c.prioridade = 'media' THEN 2 
                                                         ELSE 3 END,
                                                c.data_abertura DESC");
                            if ($res) {
                                while($c = $res->fetch_assoc()) {
                                    $status_class = '';
                                    if($c['status'] == 'aberto') $status_class = 'bg-danger';
                                    if($c['status'] == 'em andamento') $status_class = 'bg-warning';
                                    if($c['status'] == 'concluído') $status_class = 'bg-success';
                                    
                                    $prioridade_class = '';
                                    if($c['prioridade'] == 'alta') $prioridade_class = 'bg-danger';
                                    if($c['prioridade'] == 'media') $prioridade_class = 'bg-warning';
                                    if($c['prioridade'] == 'baixa') $prioridade_class = 'bg-info';
                                    
                                    echo "<tr>
                                            <td>{$c['id']}</td>
                                            <td>".htmlspecialchars($c['titulo'])."</td>
                                            <td>".htmlspecialchars($c['equipamento'] ?? 'Não especificado')."</td>
                                            <td><span class='badge $status_class'>".ucfirst($c['status'])."</span></td>
                                            <td><span class='badge $prioridade_class'>".ucfirst($c['prioridade'])."</span></td>
                                            <td>".date('d/m/Y H:i', strtotime($c['data_abertura']))."</td>
                                            <td>
                                                <a href='chamado.php?view={$c['id']}' class='btn btn-sm btn-info me-1' title='Visualizar'><i class='bi bi-eye'></i></a>
                                                ".($_SESSION['nivel'] >= 2 ? "<a href='editar_chamado.php?id={$c['id']}' class='btn btn-sm btn-warning' title='Editar'><i class='bi bi-pencil'></i></a>" : "")."
                                            </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>Erro ao carregar chamados: " . $conn->error . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Chamado -->
    <div class="modal fade" id="novoChamadoModal" tabindex="-1" aria-labelledby="novoChamadoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novoChamadoModalLabel">Novo Chamado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título *</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição *</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="id_equipamento" class="form-label">Equipamento *</label>
                            <select class="form-select" id="id_equipamento" name="id_equipamento" required>
                                <option value="">Selecione um equipamento</option>
                                <?php
                                $equipamentos = $conn->query("SELECT id, descricao FROM equipamentos ORDER BY descricao");
                                if ($equipamentos) {
                                    while($eqp = $equipamentos->fetch_assoc()) {
                                        echo "<option value='{$eqp['id']}'>".htmlspecialchars($eqp['descricao'])."</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="prioridade" class="form-label">Prioridade *</label>
                            <select class="form-select" id="prioridade" name="prioridade" required>
                                <option value="alta">Alta</option>
                                <option value="media" selected>Média</option>
                                <option value="baixa">Baixa</option>
                            </select>
                        </div>
                        <input type="hidden" name="status" value="aberto">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="add_chamado" class="btn btn-primary">Cadastrar</button>
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
<?php
// Fechar conexão com o banco de dados
$conn->close();
?>