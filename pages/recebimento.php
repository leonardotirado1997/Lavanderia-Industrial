<?php
$pageTitle = "Recebimento";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/qrcode_helper.php';

$mensagem = '';
$tipo_mensagem = '';
$pedido_criado = null;

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente = trim($_POST['cliente'] ?? '');
    $tipo_material = trim($_POST['tipo_material'] ?? '');
    $quantidade = intval($_POST['quantidade'] ?? 0);
    $observacao = trim($_POST['observacao'] ?? '');
    
    if (empty($cliente) || empty($tipo_material) || $quantidade <= 0) {
        $mensagem = 'Por favor, preencha todos os campos obrigatórios.';
        $tipo_mensagem = 'danger';
    } else {
        // Inserir pedido
        $stmt = $conn->prepare("INSERT INTO pedidos (cliente, tipo_material, quantidade, observacao, status) VALUES (?, ?, ?, ?, 'Recebido')");
        $stmt->bind_param("ssis", $cliente, $tipo_material, $quantidade, $observacao);
        
        if ($stmt->execute()) {
            $pedido_id = $conn->insert_id;
            
            // Gerar QR Code
            $codigo_qr_texto = 'PEDIDO-' . $pedido_id;
            $nome_qr = gerarQRCode($codigo_qr_texto, 'pedido_' . $pedido_id . '.png');
            
            if ($nome_qr) {
                // Atualizar pedido com o caminho do QR Code
                $stmt2 = $conn->prepare("UPDATE pedidos SET codigo_qr = ? WHERE id = ?");
                $stmt2->bind_param("si", $nome_qr, $pedido_id);
                $stmt2->execute();
                $stmt2->close();
            }
            
            // Buscar pedido criado
            $result = $conn->query("SELECT * FROM pedidos WHERE id = $pedido_id");
            $pedido_criado = $result->fetch_assoc();
            
            $mensagem = 'Pedido cadastrado com sucesso! QR Code gerado.';
            $tipo_mensagem = 'success';
        } else {
            $mensagem = 'Erro ao cadastrar pedido: ' . $conn->error;
            $tipo_mensagem = 'danger';
        }
        
        $stmt->close();
    }
}
?>

<div class="row">
    <div class="col-12">
        <h1 class="page-title">
            <i class="bi bi-box-arrow-in-down"></i> Recebimento de Pedidos
        </h1>
    </div>
</div>

<?php if ($mensagem): ?>
<div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($mensagem); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($pedido_criado): ?>
<!-- Exibir QR Code após criação -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="bi bi-check-circle"></i> Pedido Cadastrado com Sucesso!
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Dados do Pedido</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>ID do Pedido:</th>
                                <td><strong>#<?php echo $pedido_criado['id']; ?></strong></td>
                            </tr>
                            <tr>
                                <th>Cliente:</th>
                                <td><?php echo htmlspecialchars($pedido_criado['cliente']); ?></td>
                            </tr>
                            <tr>
                                <th>Tipo de Material:</th>
                                <td><?php echo htmlspecialchars($pedido_criado['tipo_material']); ?></td>
                            </tr>
                            <tr>
                                <th>Quantidade:</th>
                                <td><?php echo $pedido_criado['quantidade']; ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td><span class="badge bg-secondary"><?php echo $pedido_criado['status']; ?></span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="qr-code-container">
                            <h5>QR Code do Pedido</h5>
                            <?php if ($pedido_criado['codigo_qr']): ?>
                                <img src="<?php echo obterCaminhoQRCode($pedido_criado['codigo_qr']); ?>" alt="QR Code" class="img-fluid">
                                <p class="mt-3">
                                    <strong>Código:</strong> PEDIDO-<?php echo $pedido_criado['id']; ?>
                                </p>
                                <div class="d-grid gap-2">
                                    <button onclick="imprimirQRCode()" class="btn btn-primary">
                                        <i class="bi bi-printer"></i> Imprimir QR Code
                                    </button>
                                    <button onclick="copiarCodigoQR('PEDIDO-<?php echo $pedido_criado['id']; ?>')" class="btn btn-secondary">
                                        <i class="bi bi-clipboard"></i> Copiar Código
                                    </button>
                                </div>
                            <?php else: ?>
                                <p class="text-danger">Erro ao gerar QR Code</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Formulário de Recebimento -->
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-file-earmark-plus"></i> Novo Pedido
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="cliente" class="form-label">Cliente <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cliente" name="cliente" required 
                               placeholder="Nome da empresa cliente" value="<?php echo isset($_POST['cliente']) ? htmlspecialchars($_POST['cliente']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipo_material" class="form-label">Tipo de Material <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipo_material" name="tipo_material" required>
                            <option value="">Selecione...</option>
                            <option value="EPI" <?php echo (isset($_POST['tipo_material']) && $_POST['tipo_material'] == 'EPI') ? 'selected' : ''; ?>>EPI</option>
                            <option value="Uniforme" <?php echo (isset($_POST['tipo_material']) && $_POST['tipo_material'] == 'Uniforme') ? 'selected' : ''; ?>>Uniforme</option>
                            <option value="Toalha" <?php echo (isset($_POST['tipo_material']) && $_POST['tipo_material'] == 'Toalha') ? 'selected' : ''; ?>>Toalha</option>
                            <option value="Roupa de Cama" <?php echo (isset($_POST['tipo_material']) && $_POST['tipo_material'] == 'Roupa de Cama') ? 'selected' : ''; ?>>Roupa de Cama</option>
                            <option value="Outro" <?php echo (isset($_POST['tipo_material']) && $_POST['tipo_material'] == 'Outro') ? 'selected' : ''; ?>>Outro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quantidade" class="form-label">Quantidade <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantidade" name="quantidade" required 
                               min="1" placeholder="Quantidade de itens" value="<?php echo isset($_POST['quantidade']) ? htmlspecialchars($_POST['quantidade']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacao" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacao" name="observacao" rows="3" 
                                  placeholder="Observações gerais sobre o pedido"><?php echo isset($_POST['observacao']) ? htmlspecialchars($_POST['observacao']) : ''; ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save"></i> Salvar Pedido e Gerar QR Code
                        </button>
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar ao Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>

