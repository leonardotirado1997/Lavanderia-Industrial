<?php
$pageTitle = "Dashboard";
require_once __DIR__ . '/includes/header.php';

// Buscar contadores
$sql_recebidos = "SELECT COUNT(*) as total FROM pedidos WHERE status = 'Recebido'";
$sql_lavagem = "SELECT COUNT(*) as total FROM pedidos WHERE status = 'Em Lavagem'";
$sql_expedicao = "SELECT COUNT(*) as total FROM pedidos WHERE status = 'Pronto para Expedição'";
$sql_concluidos = "SELECT COUNT(*) as total FROM pedidos WHERE status = 'Concluído'";

$result_recebidos = $conn->query($sql_recebidos);
$result_lavagem = $conn->query($sql_lavagem);
$result_expedicao = $conn->query($sql_expedicao);
$result_concluidos = $conn->query($sql_concluidos);

$total_recebidos = $result_recebidos->fetch_assoc()['total'];
$total_lavagem = $result_lavagem->fetch_assoc()['total'];
$total_expedicao = $result_expedicao->fetch_assoc()['total'];
$total_concluidos = $result_concluidos->fetch_assoc()['total'];
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="page-title">
            <i class="bi bi-speedometer2"></i> Dashboard
        </h1>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card fade-in">
            <div class="card-body">
                <div class="stat-number"><?php echo $total_recebidos; ?></div>
                <div class="stat-label">
                    <i class="bi bi-box-arrow-in-down"></i> Recebidos
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card fade-in">
            <div class="card-body">
                <div class="stat-number text-warning"><?php echo $total_lavagem; ?></div>
                <div class="stat-label">
                    <i class="bi bi-droplet"></i> Em Lavagem
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card fade-in">
            <div class="card-body">
                <div class="stat-number text-info"><?php echo $total_expedicao; ?></div>
                <div class="stat-label">
                    <i class="bi bi-box-arrow-up"></i> Prontos para Expedição
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card fade-in">
            <div class="card-body">
                <div class="stat-number text-success"><?php echo $total_concluidos; ?></div>
                <div class="stat-label">
                    <i class="bi bi-check-circle"></i> Concluídos
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Links Rápidos -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning-charge"></i> Ações Rápidas
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="pages/recebimento.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-down"></i> Novo Recebimento
                    </a>
                    <a href="pages/lavagem.php" class="btn btn-warning btn-lg">
                        <i class="bi bi-droplet"></i> Iniciar Lavagem
                    </a>
                    <a href="pages/expedicao.php" class="btn btn-info btn-lg">
                        <i class="bi bi-box-arrow-up"></i> Expedir Pedido
                    </a>
                    <a href="pages/relatorios.php" class="btn btn-secondary btn-lg">
                        <i class="bi bi-file-earmark-text"></i> Ver Relatórios
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Pedidos Recentes
            </div>
            <div class="card-body">
                <?php
                $sql_recentes = "SELECT * FROM pedidos ORDER BY data_cadastro DESC LIMIT 5";
                $result_recentes = $conn->query($sql_recentes);
                
                if ($result_recentes->num_rows > 0) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-sm">';
                    echo '<thead><tr><th>ID</th><th>Cliente</th><th>Status</th></tr></thead>';
                    echo '<tbody>';
                    while ($row = $result_recentes->fetch_assoc()) {
                        $badge_class = '';
                        switch($row['status']) {
                            case 'Recebido': $badge_class = 'bg-secondary'; break;
                            case 'Em Lavagem': $badge_class = 'bg-warning'; break;
                            case 'Pronto para Expedição': $badge_class = 'bg-info'; break;
                            case 'Concluído': $badge_class = 'bg-success'; break;
                        }
                        echo '<tr>';
                        echo '<td>#' . $row['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['cliente']) . '</td>';
                        echo '<td><span class="badge ' . $badge_class . '">' . $row['status'] . '</span></td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                    echo '</div>';
                } else {
                    echo '<p class="text-muted">Nenhum pedido cadastrado ainda.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once __DIR__ . '/includes/footer.php';
?>

