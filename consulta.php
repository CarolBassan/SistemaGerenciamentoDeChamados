<?php
include 'conexao.php';
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas - Sistema CIET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h2><i class="bi bi-search"></i> Consultas</h2>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="termo"
                            placeholder="Buscar por título ou descrição..."
                            value="<?php echo isset($_GET['termo']) ? htmlspecialchars($_GET['termo']) : ''; ?>">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>

                <?php if (isset($_GET['termo'])): ?>
                <?php
                $termo = '%' . $conn->real_escape_string($_GET['termo']) . '%';
                $stmt = $conn->prepare("SELECT c.*, e.descricao as equipamento 
                                      FROM chamados c 
                                      LEFT JOIN equipamentos e ON c.id_equipamento = e.id 
                                      WHERE c.titulo LIKE ? OR c.descricao LIKE ? 
                                      ORDER BY c.data_abertura DESC");
                $stmt->bind_param("ss", $termo, $termo);
                $stmt->execute();
                $res = $stmt->get_result();
                
                if ($res->num_rows > 0):
                ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Descrição</th>
                                <th>Equipamento</th>
                                <th>Status</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $res->fetch_assoc()): ?>
                            <?php
                            $status_class = '';
                            if($row['status'] == 'aberto') $status_class = 'bg-danger';
                            if($row['status'] == 'em andamento') $status_class = 'bg-warning';
                            if($row['status'] == 'concluído') $status_class = 'bg-success';
                            ?>
                            <tr>
                                <td><?php echo $row['titulo']; ?></td>
                                <td><?php echo (strlen($row['descricao']) > 50) ? substr($row['descricao'], 0, 50) . '...' : $row['descricao']; ?>
                                </td>
                                <td><?php echo $row['equipamento']; ?></td>
                                <td><span
                                        class="badge <?php echo $status_class; ?>"><?php echo $row['status']; ?></span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($row['data_abertura'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">Nenhum chamado encontrado com o termo
                    "<?php echo htmlspecialchars($_GET['termo']); ?>"</div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>