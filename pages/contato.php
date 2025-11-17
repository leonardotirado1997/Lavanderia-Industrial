<?php
$pageTitle = "Contato com a Equipe";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/qrcode_helper.php';

// Dados da equipe
$equipe = [
    [
        'nome' => 'Leonardo Pires',
        'funcao' => 'Desenvolvedor Full Stack',
        'email' => 'leonardo.pires@luvasul.com',
        'linkedin' => 'https://www.linkedin.com/in/leonardo-pires-tirado/',
        'foto' => 'leonardo_pires.jpeg'
    ],
    [
        'nome' => 'Renan Portela',
        'funcao' => 'Desenvolvedor Full Stack',
        'email' => 'renan.portela@luvasul.com',
        'linkedin' => 'https://www.linkedin.com/in/portela-renan/',
        'foto' => 'renan_portela.jpeg'
    ],
    [
        'nome' => 'Lucas Alves',
        'funcao' => 'Product Owner (PO) e Gerente de negócio',
        'email' => 'lucas.alves@luvasul.com',
        'linkedin' => 'https://www.linkedin.com/in/lucas-alves-a02514178/',
        'foto' => 'lucas_alves.jpeg'
    ],
    [
        'nome' => 'José Vitor',
        'funcao' => 'DevOps Engineer e Big Data',
        'email' => 'jose.vitor@luvasul.com',
        'linkedin' => 'https://www.linkedin.com/in/jos%C3%A9-vitor-ferreira-dos-santos/',
        'foto' => 'jose_vitor.jpeg'
    ]
];

// Gerar QR codes do LinkedIn - sempre regenerar para garantir links corretos
foreach ($equipe as &$membro) {
    $qr_filename = 'linkedin_' . strtolower(str_replace(' ', '_', $membro['nome'])) . '.png';
    $qr_path = QR_CODE_DIR . $qr_filename;
    
    // Sempre regenerar os QR codes para garantir que usam os links corretos
    gerarQRCode($membro['linkedin'], $qr_filename);
    
    $membro['qr_code'] = $qr_filename;
}
unset($membro);

// Determinar caminho base
$base_path = '../';
?>

<div class="team-container">
    <div class="container">
        <div class="page-header">
            <h1><i class="bi bi-people-fill"></i> Nossa Equipe</h1>
            <p>Conheça os profissionais que desenvolvem e mantêm o sistema LuvaSul</p>
        </div>

        <div class="row g-4">
            <?php foreach ($equipe as $membro): ?>
            <div class="col-lg-3 col-md-6">
                <div class="team-card">
                    <!-- Foto do membro -->
                    <?php 
                    $foto_path = $base_path . 'assets/images/' . $membro['foto'];
                    $foto_exists = file_exists(__DIR__ . '/../assets/images/' . $membro['foto']);
                    ?>
                    <?php if ($foto_exists): ?>
                        <img src="<?php echo $foto_path; ?>" 
                             alt="<?php echo htmlspecialchars($membro['nome']); ?>" 
                             class="team-photo">
                    <?php else: ?>
                        <div class="team-photo" style="line-height: 150px; color: white; font-size: 3rem; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person-circle"></i>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Nome e Função -->
                    <h3 class="team-name"><?php echo htmlspecialchars($membro['nome']); ?></h3>
                    <p class="team-role">
                        <i class="bi bi-briefcase"></i> <?php echo htmlspecialchars($membro['funcao']); ?>
                    </p>
                    
                    <!-- QR Code do LinkedIn -->
                    <div class="qr-code-wrapper">
                        <p class="small text-muted mb-2">QR Code LinkedIn</p>
                        <img src="<?php echo obterCaminhoQRCode($membro['qr_code']); ?>" 
                             alt="QR Code LinkedIn - <?php echo htmlspecialchars($membro['nome']); ?>">
                    </div>
                    
                    <!-- Botão de Contato -->
                    <a href="mailto:<?php echo htmlspecialchars($membro['email']); ?>" 
                       class="btn contact-btn">
                        <i class="bi bi-envelope"></i> Entrar em Contato
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Informações Adicionais -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card" style="background: rgba(255,255,255,0.95);">
                    <div class="card-body text-center">
                        <h4 class="mb-3">
                            <i class="bi bi-info-circle text-primary"></i> Sobre a Equipe
                        </h4>
                        <p class="lead">
                            Nossa equipe é composta por profissionais especializados em desenvolvimento de software, 
                            garantindo que o sistema LuvaSul esteja sempre atualizado, seguro e funcionando perfeitamente.
                        </p>
                        <p class="text-muted">
                            Escaneie os QR Codes acima para conectar-se com nossos desenvolvedores no LinkedIn, 
                            ou clique em "Entrar em Contato" para enviar um e-mail diretamente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../includes/footer.php';
?>

