<?php
// Verificar se é exportação CSV ANTES de incluir o header
if (isset($_GET['exportar']) && $_GET['exportar'] == 'csv') {
    // Inicializar conexão para exportação
    require_once __DIR__ . '/../conexao.php';
    $conn = inicializarDB();
    
    $filtro_status = $_GET['status'] ?? 'todos';
    $pedidos = [];
    
    // Buscar pedidos com filtro
    $sql = "SELECT * FROM pedidos WHERE 1=1";
    
    if ($filtro_status != 'todos') {
        $sql .= " AND status = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $filtro_status);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
    
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = $row;
    }
    
    if (isset($stmt)) {
        $stmt->close();
    }
    
    // Enviar headers antes de qualquer output
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=relatorio_pedidos_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8 (Excel)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Cabeçalho (adicionando parâmetro $escape para evitar depreciação)
    fputcsv($output, ['ID', 'Cliente', 'Tipo de Material', 'Quantidade', 'Status', 'Observação', 'Data de Cadastro'], ';', '"', '\\');
    
    // Dados (adicionando parâmetro $escape para evitar depreciação)
    foreach ($pedidos as $pedido) {
        fputcsv($output, [
            $pedido['id'],
            $pedido['cliente'],
            $pedido['tipo_material'],
            $pedido['quantidade'],
            $pedido['status'],
            $pedido['observacao'],
            date('d/m/Y H:i', strtotime($pedido['data_cadastro']))
        ], ';', '"', '\\');
    }
    
    fclose($output);
    $conn->close();
    exit;
}

// Se não for exportação, continuar normalmente
$pageTitle = "Relatórios";
require_once __DIR__ . '/../includes/header.php';

$filtro_status = $_GET['status'] ?? 'todos';
$pedidos = [];

// Buscar pedidos com filtro
$sql = "SELECT * FROM pedidos WHERE 1=1";

if ($filtro_status != 'todos') {
    $sql .= " AND status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filtro_status);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

while ($row = $result->fetch_assoc()) {
    $pedidos[] = $row;
}

if (isset($stmt)) {
    $stmt->close();
}
?>

<div class="row">
    <div class="col-12">
        <h1 class="page-title">
            <i class="bi bi-file-earmark-text"></i> Relatórios
        </h1>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-funnel"></i> Filtros
            </div>
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Filtrar por Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="todos" <?php echo $filtro_status == 'todos' ? 'selected' : ''; ?>>Todos os Status</option>
                            <option value="Recebido" <?php echo $filtro_status == 'Recebido' ? 'selected' : ''; ?>>Recebido</option>
                            <option value="Em Lavagem" <?php echo $filtro_status == 'Em Lavagem' ? 'selected' : ''; ?>>Em Lavagem</option>
                            <option value="Pronto para Expedição" <?php echo $filtro_status == 'Pronto para Expedição' ? 'selected' : ''; ?>>Pronto para Expedição</option>
                            <option value="Concluído" <?php echo $filtro_status == 'Concluído' ? 'selected' : ''; ?>>Concluído</option>
                        </select>
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="?status=<?php echo $filtro_status; ?>&exportar=csv" class="btn btn-success">
                            <i class="bi bi-download"></i> Exportar CSV
                        </a>
                        <a href="relatorios.php" class="btn btn-secondary ms-2">
                            <i class="bi bi-arrow-clockwise"></i> Limpar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-muted">Total de Pedidos</h5>
                <h2 class="text-primary"><?php echo count($pedidos); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-muted">Recebidos</h5>
                <h2 class="text-secondary"><?php echo count(array_filter($pedidos, fn($p) => $p['status'] == 'Recebido')); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-muted">Em Processo</h5>
                <h2 class="text-warning"><?php echo count(array_filter($pedidos, fn($p) => in_array($p['status'], ['Em Lavagem', 'Pronto para Expedição']))); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-muted">Concluídos</h5>
                <h2 class="text-success"><?php echo count(array_filter($pedidos, fn($p) => $p['status'] == 'Concluído')); ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Pedidos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ul"></i> Lista de Pedidos</span>
                <span class="badge bg-primary"><?php echo count($pedidos); ?> registro(s)</span>
            </div>
            <div class="card-body">
                <?php if (count($pedidos) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Tipo de Material</th>
                                <th>Quantidade</th>
                                <th>Status</th>
                                <th>Observações</th>
                                <th>Data de Cadastro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): 
                                $badge_class = '';
                                switch($pedido['status']) {
                                    case 'Recebido': $badge_class = 'bg-secondary'; break;
                                    case 'Em Lavagem': $badge_class = 'bg-warning text-dark'; break;
                                    case 'Pronto para Expedição': $badge_class = 'bg-info'; break;
                                    case 'Concluído': $badge_class = 'bg-success'; break;
                                }
                            ?>
                            <tr>
                                <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($pedido['cliente']); ?></td>
                                <td><?php echo htmlspecialchars($pedido['tipo_material']); ?></td>
                                <td><?php echo $pedido['quantidade']; ?></td>
                                <td>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo $pedido['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    if ($pedido['observacao']) {
                                        echo htmlspecialchars(substr($pedido['observacao'], 0, 50));
                                        if (strlen($pedido['observacao']) > 50) echo '...';
                                    } else {
                                        echo '<span class="text-muted">-</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_cadastro'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Nenhum pedido encontrado com os filtros selecionados.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>

