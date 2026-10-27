<div class="page-header">
    <h2>Procurar Produto Perdido</h2>
    <p>Simule a busca de um produto dentro do centro de distribuição usando sinal de proximidade e alerta sonoro.</p>
</div>

<div class="card" style="max-width:760px;">

    <form method="GET">
        <label>Produto ou código</label>

        <div class="campo-busca">
            <input type="text" name="busca" placeholder="Digite ou bipe o código do produto" value="<?= e($busca) ?>" required>
            <button type="submit">Buscar Produto</button>
        </div>
    </form>

    <div class="acoes">
        <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
    </div>

</div>

<?php if ($produto): ?>

    <div class="layout-mapa">

        <div class="card">
            <div class="page-header" style="margin-bottom:16px;">
                <h2><?= e($produto['nome']) ?></h2>
                <p>Produto encontrado no cadastro.</p>
            </div>

            <div class="info-produto">
                <p><strong>Código:</strong> <?= e($produto['codigo_barras']) ?></p>
                <p><strong>Estoque:</strong> <?= (int) $produto['estoque'] ?></p>
                <p><strong>Corredor:</strong> <?= e($produto['corredor']) ?></p>
                <p><strong>Prateleira:</strong> <?= e($produto['prateleira']) ?></p>
                <p><strong>Nível:</strong> <?= e($produto['nivel']) ?></p>
            </div>

            <div class="acoes">
                <a href="<?= url('mapa?busca=' . urlencode($produto['codigo_barras'])) ?>" class="btn">Ver no Mapa</a>
                <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
            </div>
        </div>

        <div class="card">
            <div class="page-header" style="margin-bottom:16px;">
                <h2>Busca por proximidade</h2>
                <p>Inicie a busca para simular a aproximação do operador até o produto.</p>
            </div>

            <div class="acoes" style="margin-top:0;">
                <button onclick="iniciarBusca()" type="button">Iniciar Busca</button>
                <button onclick="pararBusca()" class="btn-sucesso" type="button">Produto Encontrado</button>
            </div>

            <div id="status" class="status-sinal">Aguardando busca...</div>

            <div class="barra-sinal">
                <div id="progresso"></div>
            </div>

            <form id="formAtualizar" action="<?= url('produtos/atualizar-localizacao') ?>" method="POST" style="display:none; margin-top:22px; padding-top:18px; border-top:1px solid var(--borda);">

                <div class="page-header" style="margin-bottom:14px;">
                    <h2 style="font-size:16px;">Atualizar localização</h2>
                    <p>Use esta área caso o produto tenha sido encontrado em outro local.</p>
                </div>

                <input type="hidden" name="codigo_barras" value="<?= e($produto['codigo_barras']) ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Novo corredor</label>
                        <input type="text" name="corredor" placeholder="Ex: B" required>
                    </div>
                    <div class="form-group">
                        <label>Nova prateleira</label>
                        <input type="number" name="prateleira" placeholder="Ex: 2" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Novo nível</label>
                        <input type="number" name="nivel" placeholder="Ex: 1" min="1" required>
                    </div>
                </div>

                <button type="submit">Salvar Nova Localização</button>

            </form>
        </div>

    </div>

    <audio id="beep" src="<?= asset('beep.mp3') ?>" loop></audio>

<?php elseif ($busca !== ''): ?>

    <div class="card" style="max-width:620px;">
        <div class="page-header">
            <h2>Produto não encontrado</h2>
            <p>Nenhum produto foi encontrado com o código ou nome informado.</p>
        </div>
        <div class="acoes">
            <a href="<?= url('produtos/perdido') ?>" class="btn">Tentar Novamente</a>
            <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
        </div>
    </div>

<?php endif; ?>

<script>
let sinal = 0;
let intervalo;

function iniciarBusca() {
    sinal = 0;
    document.getElementById('status').innerHTML = 'Buscando sinal do produto...';
    document.getElementById('progresso').style.width = '0%';
    document.getElementById('formAtualizar').style.display = 'none';

    intervalo = setInterval(() => {
        sinal += 20;
        document.getElementById('progresso').style.width = sinal + '%';

        if (sinal < 40) {
            document.getElementById('status').innerHTML = 'Sinal fraco...';
        } else if (sinal < 80) {
            document.getElementById('status').innerHTML = 'Sinal médio...';
        } else {
            document.getElementById('status').innerHTML = 'Sinal forte! Produto próximo!';
            document.getElementById('beep')?.play();
            clearInterval(intervalo);
        }
    }, 1500);
}

function pararBusca() {
    clearInterval(intervalo);
    const beep = document.getElementById('beep');
    if (beep) { beep.pause(); beep.currentTime = 0; }
    document.getElementById('status').innerHTML = 'Produto encontrado!';
    document.getElementById('formAtualizar').style.display = 'block';
}
</script>
