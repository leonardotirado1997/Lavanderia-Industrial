<?php
$pageTitle = "Sugestões e Feedback";
require_once __DIR__ . '/../includes/header.php';

$mensagem = '';
$tipo_mensagem = '';
$formulario_limpo = false;

// Processar formulário de sugestão
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mensagem_texto = trim($_POST['mensagem'] ?? '');
    
    if (empty($nome) || empty($email) || empty($mensagem_texto)) {
        $mensagem = 'Por favor, preencha todos os campos obrigatórios.';
        $tipo_mensagem = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Por favor, insira um e-mail válido.';
        $tipo_mensagem = 'danger';
    } else {
        // Inserir sugestão no banco
        $stmt = $conn->prepare("INSERT INTO sugestoes (nome, email, mensagem) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $mensagem_texto);
        
        if ($stmt->execute()) {
            $mensagem = 'Obrigado pela sua sugestão! Ela será analisada pela nossa equipe.';
            $tipo_mensagem = 'success';
            $formulario_limpo = true;
        } else {
            $mensagem = 'Erro ao enviar sugestão: ' . $conn->error;
            $tipo_mensagem = 'danger';
        }
        
        $stmt->close();
    }
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="page-title">
            <i class="bi bi-lightbulb"></i> Sugestões e Feedback
        </h1>
    </div>
</div>

<?php if ($mensagem): ?>
<div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-<?php echo $tipo_mensagem == 'success' ? 'check-circle' : 'exclamation-triangle'; ?>-fill"></i>
    <?php echo htmlspecialchars($mensagem); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Informações -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-info-circle-fill"></i> Sua opinião é importante!
                </h5>
                <p class="card-text mb-0">
                    Compartilhe suas ideias, sugestões de melhorias ou feedback sobre o sistema LuvaSul. 
                    Todas as sugestões são analisadas pela nossa equipe de desenvolvimento e podem contribuir 
                    para tornar o sistema ainda melhor.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Formulário de Sugestão -->
<div class="row mb-4">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-chat-dots"></i> Enviar Sugestão
            </div>
            <div class="card-body">
                <form method="POST" action="" id="formSugestao">
                    <div class="mb-3">
                        <label for="nome" class="form-label">
                            <i class="bi bi-person"></i> Nome <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nome" name="nome" required 
                               placeholder="Digite seu nome completo" 
                               value="<?php echo ($formulario_limpo ? '' : (isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '')); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i> E-mail <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="seu.email@exemplo.com" 
                               value="<?php echo ($formulario_limpo ? '' : (isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '')); ?>">
                        <div class="form-text">Utilizaremos este e-mail apenas para responder sua sugestão, se necessário.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mensagem" class="form-label">
                            <i class="bi bi-chat-left-text"></i> Sua Sugestão <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="mensagem" name="mensagem" rows="6" required 
                                  placeholder="Descreva sua sugestão, ideia ou feedback sobre o sistema..."><?php echo ($formulario_limpo ? '' : (isset($_POST['mensagem']) ? htmlspecialchars($_POST['mensagem']) : '')); ?></textarea>
                        <div class="form-text">
                            <i class="bi bi-lightbulb"></i> 
                            Dica: Seja específico e detalhado. Quanto mais informações, melhor poderemos avaliar sua sugestão!
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send-fill"></i> Enviar Sugestão
                        </button>
                        <button type="reset" class="btn btn-secondary" onclick="limparFormulario()">
                            <i class="bi bi-arrow-counterclockwise"></i> Limpar Formulário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Sugestões Enviadas Recentemente (Estatísticas) -->
<?php
// Buscar total de sugestões diretamente do banco (sem cache)
// Usar query direta para garantir dados atualizados
$sql_total = "SELECT COUNT(*) as total FROM sugestoes";
$result_total = $conn->query($sql_total);
$total_sugestoes = 0;
if ($result_total && $result_total->num_rows > 0) {
    $row = $result_total->fetch_assoc();
    $total_sugestoes = isset($row['total']) ? (int)$row['total'] : 0;
}
if ($result_total) {
    $result_total->free();
}

if ($total_sugestoes > 0):
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Estatísticas
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white mb-3">
                            <div class="card-body">
                                <h2 class="display-4"><?php echo $total_sugestoes; ?></h2>
                                <p class="mb-0">Sugestões Recebidas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white mb-3">
                            <div class="card-body">
                                <h2 class="display-4">
                                    <i class="bi bi-check-circle"></i>
                                </h2>
                                <p class="mb-0">Todas Analisadas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white mb-3">
                            <div class="card-body">
                                <h2 class="display-4">
                                    <i class="bi bi-heart"></i>
                                </h2>
                                <p class="mb-0">Obrigado pelo Feedback!</p>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center text-muted mt-3">
                    <i class="bi bi-info-circle"></i> 
                    Todas as sugestões são cuidadosamente analisadas pela equipe de desenvolvimento.
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function limparFormulario() {
    document.getElementById('formSugestao').reset();
}

// Limpar formulário após envio bem-sucedido
<?php if ($formulario_limpo && $tipo_mensagem == 'success'): ?>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.getElementById('formSugestao').reset();
    }, 100);
});
<?php endif; ?>
</script>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>

