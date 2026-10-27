<?php if ($produto): ?>

    <div class="page-header">
        <h2>Produto Localizado</h2>
        <p>O produto foi identificado pelo scanner. Confira abaixo a posição cadastrada no CD.</p>
    </div>

    <div class="card">

        <div class="info-produto" style="margin-bottom:18px;">
            <p><strong>Produto:</strong> <?= e($produto['nome']) ?></p>
            <p><strong>Código:</strong> <?= e($codigo) ?></p>
            <p><strong>Estoque:</strong> <?= (int) $produto['estoque'] ?></p>
        </div>

        <div class="aviso aviso-azul">
            <strong>Localização:</strong> Corredor <?= e($produto['corredor']) ?> - Prateleira <?= e($produto['prateleira']) ?> - Nível <?= e($produto['nivel']) ?>
        </div>

        <div class="acoes">
            <a href="<?= url('mapa?busca=' . urlencode($codigo)) ?>" class="btn">Ver no Mapa</a>
            <a href="<?= url('scanner') ?>" class="btn btn-secundario">Voltar ao Scanner</a>
            <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
        </div>

    </div>

<?php else: ?>

    <div class="card" style="max-width:620px;">
        <div class="page-header">
            <h2>Produto não encontrado</h2>
            <p>Nenhum produto foi encontrado com o código lido pelo scanner.</p>
        </div>
        <div class="acoes">
            <a href="<?= url('scanner') ?>" class="btn">Tentar Novamente</a>
            <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
        </div>
    </div>

<?php endif; ?>
