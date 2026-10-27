<?php $botoes = $botoes ?? []; ?>

<div class="resultado-wrap">
    <div class="card resultado-card">

        <div class="icone-status <?= e($status) ?>">
            <?= $status === 'sucesso' ? '✓' : '!' ?>
        </div>

        <h2><?= e($titulo) ?></h2>
        <p><?= e($mensagem) ?></p>

        <?php if (!empty($resumo)): ?>
            <div class="resumo">
                <?php foreach ($resumo as $rotulo => $valor): ?>
                    <p><strong><?= e($rotulo) ?>:</strong> <?= e($valor) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="acoes" style="justify-content:center;">
            <?php foreach ($botoes as $btn): ?>
                <a href="<?= url($btn['rota']) ?>" class="btn <?= !empty($btn['secundario']) ? 'btn-secundario' : '' ?>">
                    <?= e($btn['texto']) ?>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
</div>
