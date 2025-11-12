<?php
$pageTitle = "Avalie o Sistema LuvaSul";
require_once __DIR__ . '/../includes/header.php';

$mensagem = '';
$tipo_mensagem = '';
$media_geral = 0;
$total_avaliacoes = 0;

// Processar formulário de avaliação
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $estrelas = intval($_POST['estrelas'] ?? 0);
    $comentario = trim($_POST['comentario'] ?? '');
    
    if (empty($nome) || $estrelas < 1 || $estrelas > 5) {
        $mensagem = 'Por favor, preencha seu nome e selecione uma avaliação de 1 a 5 estrelas.';
        $tipo_mensagem = 'danger';
    } else {
        // Inserir avaliação no banco
        $stmt = $conn->prepare("INSERT INTO avaliacoes (nome, estrelas, comentario) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $nome, $estrelas, $comentario);
        
        if ($stmt->execute()) {
            $mensagem = 'Obrigado pela sua avaliação! Sua opinião é muito importante para nós.';
            $tipo_mensagem = 'success';
            // Limpar campos após sucesso
            $_POST = array();
        } else {
            $mensagem = 'Erro ao salvar avaliação: ' . $conn->error;
            $tipo_mensagem = 'danger';
        }
        
        $stmt->close();
    }
}

// Calcular média geral das avaliações - sempre buscar dados atualizados
$sql_media = "SELECT AVG(estrelas) as media, COUNT(*) as total FROM avaliacoes";
$result_media = $conn->query($sql_media);
if ($result_media && $result_media->num_rows > 0) {
    $row_media = $result_media->fetch_assoc();
    $media_geral = isset($row_media['media']) && $row_media['media'] !== null ? round((float)$row_media['media'], 1) : 0;
    $total_avaliacoes = isset($row_media['total']) ? (int)$row_media['total'] : 0;
}
if ($result_media) {
    $result_media->free();
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="page-title">
            <i class="bi bi-star"></i> Avalie o Sistema LuvaSul
        </h1>
    </div>
</div>

<?php if ($mensagem): ?>
<div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($mensagem); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Média Geral -->
<?php if ($total_avaliacoes > 0): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-3">Avaliação Geral do Sistema</h3>
                <div class="display-4 mb-2">
                    <?php echo $media_geral; ?>/5.0
                </div>
                <div class="mb-3">
                    <?php
                    $estrelas_cheias = floor($media_geral);
                    $tem_meia = ($media_geral - $estrelas_cheias) >= 0.5;
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $estrelas_cheias) {
                            echo '<i class="bi bi-star-fill" style="font-size: 2rem;"></i> ';
                        } elseif ($i == $estrelas_cheias + 1 && $tem_meia) {
                            echo '<i class="bi bi-star-half" style="font-size: 2rem;"></i> ';
                        } else {
                            echo '<i class="bi bi-star" style="font-size: 2rem;"></i> ';
                        }
                    }
                    ?>
                </div>
                <p class="mb-0">Baseado em <strong><?php echo $total_avaliacoes; ?></strong> avaliação(ões)</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Formulário de Avaliação -->
<div class="row mb-4">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-star-fill"></i> Deixe sua Avaliação
            </div>
            <div class="card-body">
                <form method="POST" action="" id="formAvaliacao">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Seu Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome" required 
                               placeholder="Digite seu nome" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Avaliação <span class="text-danger">*</span></label>
                        <div class="star-rating">
                            <input type="hidden" name="estrelas" id="estrelas" value="0" required>
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star star-icon" data-rating="<?php echo $i; ?>" style="font-size: 2.5rem; color: #ddd; cursor: pointer; margin: 0 5px;"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-muted mt-2" id="rating-text">Clique nas estrelas para avaliar</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="comentario" class="form-label">Comentário (Opcional)</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="4" 
                                  placeholder="Deixe um comentário sobre sua experiência com o sistema..."><?php echo isset($_POST['comentario']) ? htmlspecialchars($_POST['comentario']) : ''; ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send"></i> Enviar Avaliação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Avaliações Recentes -->
<?php
// Buscar avaliações recentes - sempre buscar dados atualizados do banco
$sql_recentes = "SELECT * FROM avaliacoes ORDER BY data_avaliacao DESC LIMIT 10";
$result_recentes = $conn->query($sql_recentes);
$tem_avaliacoes = false;
if ($result_recentes && $result_recentes->num_rows > 0) {
    $tem_avaliacoes = true;
}
?>
<?php if ($tem_avaliacoes): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-chat-left-text"></i> Avaliações Recentes
            </div>
            <div class="card-body">
                <div class="row">
                    <?php while ($avaliacao = $result_recentes->fetch_assoc()): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($avaliacao['nome']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y H:i', strtotime($avaliacao['data_avaliacao'])); ?>
                                    </small>
                                </div>
                                <div class="mb-2">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $avaliacao['estrelas']) {
                                            echo '<i class="bi bi-star-fill text-warning"></i> ';
                                        } else {
                                            echo '<i class="bi bi-star text-muted"></i> ';
                                        }
                                    }
                                    ?>
                                </div>
                                <?php if (!empty($avaliacao['comentario'])): ?>
                                <p class="card-text text-muted small mb-0">
                                    "<?php echo htmlspecialchars($avaliacao['comentario']); ?>"
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
endif; 
if ($result_recentes) {
    $result_recentes->free();
}
?>

<script>
// Sistema de estrelas interativo
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-icon');
    const hiddenInput = document.getElementById('estrelas');
    const ratingText = document.getElementById('rating-text');
    
    const ratings = {
        1: 'Péssimo',
        2: 'Ruim',
        3: 'Regular',
        4: 'Bom',
        5: 'Excelente'
    };
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            hiddenInput.value = rating;
            updateStars(rating);
            ratingText.textContent = ratings[rating] + ' (' + rating + ' estrelas)';
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            highlightStars(rating);
        });
    });
    
    document.querySelector('.star-rating').addEventListener('mouseleave', function() {
        const currentRating = parseInt(hiddenInput.value);
        if (currentRating > 0) {
            updateStars(currentRating);
        } else {
            stars.forEach(star => {
                star.classList.remove('bi-star-fill', 'text-warning');
                star.classList.add('bi-star');
                star.style.color = '#ddd';
            });
        }
    });
    
    function updateStars(rating) {
        stars.forEach((star, index) => {
            const starRating = parseInt(star.getAttribute('data-rating'));
            if (starRating <= rating) {
                star.classList.remove('bi-star');
                star.classList.add('bi-star-fill', 'text-warning');
                star.style.color = '#ffc107';
            } else {
                star.classList.remove('bi-star-fill', 'text-warning');
                star.classList.add('bi-star');
                star.style.color = '#ddd';
            }
        });
    }
    
    function highlightStars(rating) {
        stars.forEach(star => {
            const starRating = parseInt(star.getAttribute('data-rating'));
            if (starRating <= rating) {
                star.style.color = '#ffc107';
            } else {
                star.style.color = '#ddd';
            }
        });
    }
});
</script>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>

