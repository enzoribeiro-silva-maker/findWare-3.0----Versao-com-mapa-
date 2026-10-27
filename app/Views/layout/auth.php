<?php
$tituloPagina = $tituloPagina ?? 'Acesso ao Sistema';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($tituloPagina) ?> - FindWare</title>
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<div class="auth-shell">

    <div class="auth-brand-side">
        <div class="logo-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-4v-5H9v5H5a1 1 0 0 1-1-1z"/>
            </svg>
        </div>
        <h1>FindWare</h1>
        <p>Sistema Inteligente de Localização de Produtos para centros de distribuição. Saiba sempre onde cada item está guardado.</p>

        <div class="auth-features">
            <div class="item"><span class="bolha"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s6-5.4 6-10a6 6 0 1 0-12 0c0 4.6 6 10 6 10Z"/><circle cx="12" cy="11" r="2"/></svg></span> Mapa visual do centro de distribuição</div>
            <div class="item"><span class="bolha"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="7" width="16" height="12" rx="2"/><path d="M9 7V5h6v2"/><path d="M9 13h6"/></svg></span> Leitura de QR Code para entrada e saída</div>
            <div class="item"><span class="bolha"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 18V10M12 18V5M19 18v-7"/></svg></span> Indicadores de estoque em tempo real</div>
        </div>
    </div>

    <div class="auth-form-side">
        <?php if (isset($conteudo) && is_callable($conteudo)) { $conteudo(); } ?>
    </div>

</div>

<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
