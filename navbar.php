<?php
// Verifica se a sessão está ativa antes de acessar $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determina qual página está ativa
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="principal.php">
            <i class="bi bi-building-gear me-2"></i>
            <span>Sistema CIET</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'principal.php' ? 'active fw-bold' : ''; ?>"
                        href="principal.php">
                        <i class="bi bi-house-door me-1"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'laboratorio.php' ? 'active fw-bold' : ''; ?>"
                        href="laboratorio.php">
                        <i class="bi bi-building me-1"></i> Laboratórios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'equipamento.php' ? 'active fw-bold' : ''; ?>"
                        href="equipamento.php">
                        <i class="bi bi-pc-display me-1"></i> Equipamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'chamado.php' ? 'active fw-bold' : ''; ?>"
                        href="chamado.php">
                        <i class="bi bi-ticket-detailed me-1"></i> Chamados
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center <?php echo $current_page == 'perfil.php' ? 'active fw-bold' : ''; ?>"
                        href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <span><?php echo isset($_SESSION['nome']) ? htmlspecialchars($_SESSION['nome']) : 'Usuário'; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text">
                                <small>Nível:
                                    <?php echo isset($_SESSION['nivel']) ? ($_SESSION['nivel'] == 2 ? 'Administrador' : 'Técnico') : 'Não definido'; ?></small>
                            </span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item <?php echo $current_page == 'perfil.php' ? 'active fw-bold' : ''; ?>"
                                href="perfil.php">
                                <i class="bi bi-person me-2"></i> Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Sair
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar .nav-link.active {
    font-weight: bold !important;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}

.navbar .dropdown-item.active {
    font-weight: bold !important;
    background-color: rgba(0, 0, 0, 0.05);
}
</style>