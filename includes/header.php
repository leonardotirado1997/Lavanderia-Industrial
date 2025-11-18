<?php
require_once __DIR__ . '/../conexao.php';
$conn = inicializarDB();

// Garantir que o diretório de QR codes existe
if (!file_exists(QR_CODE_DIR)) {
    mkdir(QR_CODE_DIR, 0777, true);
}

// Determinar o caminho base (raiz do projeto)
$base_path = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- CSS Customizado -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $base_path; ?>index.php">
                <i class="bi bi-house-door-fill"></i> <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>index.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'recebimento.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/recebimento.php">
                            <i class="bi bi-box-arrow-in-down"></i> Recebimento
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lavagem.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/lavagem.php">
                            <i class="bi bi-droplet"></i> Lavagem
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'expedicao.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/expedicao.php">
                            <i class="bi bi-box-arrow-up"></i> Expedição
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'relatorios.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/relatorios.php">
                            <i class="bi bi-file-earmark-text"></i> Relatórios
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <?php
                        $currentPage = basename($_SERVER['PHP_SELF']);
                        $dropdownPages = ['sobre.php', 'avaliacao.php', 'contato.php', 'sugestoes.php'];
                        $isDropdownActive = in_array($currentPage, $dropdownPages);
                        ?>
                        <a class="nav-link dropdown-toggle <?php echo $isDropdownActive ? 'active' : ''; ?>" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i> Mais
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item <?php echo $currentPage == 'sobre.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/sobre.php">
                                    <i class="bi bi-info-circle"></i> Sobre
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $currentPage == 'avaliacao.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/avaliacao.php">
                                    <i class="bi bi-star"></i> Avaliação
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $currentPage == 'contato.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/contato.php">
                                    <i class="bi bi-people"></i> Contato
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $currentPage == 'sugestoes.php' ? 'active' : ''; ?>" href="<?php echo $base_path; ?>pages/sugestoes.php">
                                    <i class="bi bi-lightbulb"></i> Sugestões
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">

