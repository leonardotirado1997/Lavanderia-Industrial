<?php
$pageTitle = "Expedição";
require_once __DIR__ . '/../includes/header.php';

$mensagem = '';
$tipo_mensagem = '';
$pedido = null;
$mostrar_comprovante = false;

// Processar leitura do QR Code
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo_qr = trim($_POST['codigo_qr'] ?? '');
    $acao = $_POST['acao'] ?? 'expedir';
    
    if (empty($codigo_qr)) {
        $mensagem = 'Por favor, informe o código do QR Code.';
        $tipo_mensagem = 'danger';
    } else {
        // Extrair ID do pedido do código
        if (preg_match('/PEDIDO-(\d+)/i', $codigo_qr, $matches)) {
            $pedido_id = intval($matches[1]);
            
            // Buscar pedido
            $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = ?");
            $stmt->bind_param("i", $pedido_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $pedido = $result->fetch_assoc();
                
                if ($acao == 'expedir') {
                    // Verificar se pode expedir
                    if ($pedido['status'] == 'Em Lavagem') {
                        // Atualizar para "Pronto para Expedição"
                        $stmt2 = $conn->prepare("UPDATE pedidos SET status = 'Pronto para Expedição' WHERE id = ?");
                        $stmt2->bind_param("i", $pedido_id);
                        
                        if ($stmt2->execute()) {
                            $pedido['status'] = 'Pronto para Expedição';
                            $mensagem = 'Pedido marcado como pronto para expedição!';
                            $tipo_mensagem = 'info';
                        } else {
                            $mensagem = 'Erro ao atualizar status: ' . $conn->error;
                            $tipo_mensagem = 'danger';
                        }
                        $stmt2->close();
                    } else if ($pedido['status'] == 'Pronto para Expedição') {
                        $mensagem = 'Este pedido já está pronto para expedição. Use o botão "Concluir Pedido" para finalizar.';
                        $tipo_mensagem = 'info';
                    } else if ($pedido['status'] == 'Concluído') {
                        $mensagem = 'Este pedido já foi concluído e expedido.';
                        $tipo_mensagem = 'info';
                        $mostrar_comprovante = true;
                    } else {
                        $mensagem = 'Este pedido não está pronto para expedição. Status atual: ' . $pedido['status'];
                        $tipo_mensagem = 'warning';
                    }
                } else if ($acao == 'concluir') {
                    // Concluir pedido
                    if ($pedido['status'] == 'Pronto para Expedição') {
                        $stmt3 = $conn->prepare("UPDATE pedidos SET status = 'Concluído' WHERE id = ?");
                        $stmt3->bind_param("i", $pedido_id);
                        
                        if ($stmt3->execute()) {
                            $pedido['status'] = 'Concluído';
                            $mensagem = 'Pedido concluído e expedido com sucesso!';
                            $tipo_mensagem = 'success';
                            $mostrar_comprovante = true;
                        } else {
                            $mensagem = 'Erro ao concluir pedido: ' . $conn->error;
                            $tipo_mensagem = 'danger';
                        }
                        $stmt3->close();
                    } else if ($pedido['status'] == 'Concluído') {
                        $mensagem = 'Este pedido já foi concluído e expedido.';
                        $tipo_mensagem = 'info';
                        $mostrar_comprovante = true;
                    } else {
                        $mensagem = 'Este pedido precisa estar "Pronto para Expedição" antes de ser concluído. Status atual: ' . $pedido['status'];
                        $tipo_mensagem = 'warning';
                    }
                }
            } else {
                $mensagem = 'Pedido não encontrado. Verifique o código do QR Code.';
                $tipo_mensagem = 'danger';
            }
            
            $stmt->close();
        } else {
            $mensagem = 'Código QR inválido. Formato esperado: PEDIDO-123';
            $tipo_mensagem = 'danger';
        }
    }
}
?>

<div class="row">
    <div class="col-12">
        <h1 class="page-title">
            <i class="bi bi-box-arrow-up"></i> Expedição
        </h1>
    </div>
</div>

<?php if ($mensagem): ?>
<div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($mensagem); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-qr-code-scan"></i> Leitura de QR Code
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="acao" value="expedir">
                    <div class="mb-3">
                        <label for="codigo_qr" class="form-label">Código do QR Code</label>
                        <input type="text" class="form-control form-control-lg" id="codigo_qr" name="codigo_qr" 
                               placeholder="PEDIDO-123 ou escaneie o QR Code" required autofocus>
                        <small class="form-text text-muted">Digite o código ou escaneie o QR Code do pedido</small>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info btn-lg">
                            <i class="bi bi-check-circle"></i> Expedir Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php if ($pedido): ?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="bi bi-info-circle"></i> Informações do Pedido
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>ID do Pedido:</th>
                        <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                    </tr>
                    <tr>
                        <th>Cliente:</th>
                        <td><?php echo htmlspecialchars($pedido['cliente']); ?></td>
                    </tr>
                    <tr>
                        <th>Tipo de Material:</th>
                        <td><?php echo htmlspecialchars($pedido['tipo_material']); ?></td>
                    </tr>
                    <tr>
                        <th>Quantidade:</th>
                        <td><?php echo $pedido['quantidade']; ?></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <?php
                            $badge_class = '';
                            switch($pedido['status']) {
                                case 'Pronto para Expedição': $badge_class = 'bg-info'; break;
                                case 'Concluído': $badge_class = 'bg-success'; break;
                                default: $badge_class = 'bg-secondary';
                            }
                            ?>
                            <span class="badge <?php echo $badge_class; ?>"><?php echo $pedido['status']; ?></span>
                        </td>
                    </tr>
                    <?php if ($pedido['observacao']): ?>
                    <tr>
                        <th>Observações:</th>
                        <td><?php echo nl2br(htmlspecialchars($pedido['observacao'])); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Data de Cadastro:</th>
                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_cadastro'])); ?></td>
                    </tr>
                </table>
                
                <?php if ($pedido['status'] == 'Pronto para Expedição'): ?>
                <div class="mt-3">
                    <form method="POST" action="">
                        <input type="hidden" name="codigo_qr" value="PEDIDO-<?php echo $pedido['id']; ?>">
                        <input type="hidden" name="acao" value="concluir">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle-fill"></i> Concluir Pedido
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if ($mostrar_comprovante && $pedido): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card" id="comprovante">
            <div class="card-header bg-success text-white">
                <i class="bi bi-receipt"></i> Comprovante de Expedição
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h3><?php echo SITE_NAME; ?></h3>
                    <p class="text-muted">Comprovante de Expedição</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <table class="table table-bordered">
                            <tr>
                                <th>Número do Pedido:</th>
                                <td><strong>#<?php echo $pedido['id']; ?></strong></td>
                            </tr>
                            <tr>
                                <th>Cliente:</th>
                                <td><?php echo htmlspecialchars($pedido['cliente']); ?></td>
                            </tr>
                            <tr>
                                <th>Tipo de Material:</th>
                                <td><?php echo htmlspecialchars($pedido['tipo_material']); ?></td>
                            </tr>
                            <tr>
                                <th>Quantidade:</th>
                                <td><?php echo $pedido['quantidade']; ?> itens</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td><span class="badge bg-success">Concluído</span></td>
                            </tr>
                            <tr>
                                <th>Data de Expedição:</th>
                                <td><?php echo date('d/m/Y H:i:s'); ?></td>
                            </tr>
                        </table>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">
                                <small>Este comprovante confirma a expedição do pedido.</small>
                            </p>
                            <button onclick="window.print()" class="btn btn-primary">
                                <i class="bi bi-printer"></i> Imprimir Comprovante
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ul"></i> Pedidos Prontos para Expedição
            </div>
            <div class="card-body">
                <?php
                $sql = "SELECT * FROM pedidos WHERE status = 'Pronto para Expedição' ORDER BY data_cadastro DESC";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-hover">';
                    echo '<thead><tr><th>ID</th><th>Cliente</th><th>Tipo</th><th>Quantidade</th><th>Data</th></tr></thead>';
                    echo '<tbody>';
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>#' . $row['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['cliente']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['tipo_material']) . '</td>';
                        echo '<td>' . $row['quantidade'] . '</td>';
                        echo '<td>' . date('d/m/Y H:i', strtotime($row['data_cadastro'])) . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                    echo '</div>';
                } else {
                    echo '<p class="text-muted">Nenhum pedido pronto para expedição no momento.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>

