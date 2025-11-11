<?php
// Helper para geração de QR Codes
// Requer a biblioteca phpqrcode: composer require endroid/qr-code ou baixar manualmente

function gerarQRCode($texto, $nomeArquivo = null) {
    $qrDir = QR_CODE_DIR;
    
    // Garantir que o diretório existe
    if (!file_exists($qrDir)) {
        mkdir($qrDir, 0777, true);
    }
    
    // Se não foi fornecido nome, gerar um único
    if ($nomeArquivo === null) {
        $nomeArquivo = 'qr_' . md5($texto . time()) . '.png';
    }
    
    $caminhoCompleto = $qrDir . $nomeArquivo;
    
    // Tentar usar a biblioteca phpqrcode (se disponível)
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        // Usando Composer (endroid/qr-code)
        require_once __DIR__ . '/../vendor/autoload.php';
        
        try {
            $qrCode = \Endroid\QrCode\QrCode::create($texto)
                ->setSize(300)
                ->setMargin(10);
            
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            $result->saveToFile($caminhoCompleto);
            
            return $nomeArquivo;
        } catch (Exception $e) {
            // Fallback para método alternativo
        }
    }
    
    // Método alternativo: usar API online ou biblioteca simples
    // Usando API do QR Server (gratuita, sem necessidade de biblioteca)
    $url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($texto);
    $qrImage = file_get_contents($url);
    
    if ($qrImage !== false) {
        file_put_contents($caminhoCompleto, $qrImage);
        return $nomeArquivo;
    }
    
    // Se tudo falhar, retornar null
    return null;
}

function obterCaminhoQRCode($nomeArquivo) {
    // Determinar o caminho base baseado na localização da página
    $base_path = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '';
    return $base_path . 'qrcodes/' . $nomeArquivo;
}
?>

