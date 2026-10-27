
<?php
$enderecosPorCodigo = [];

foreach (($enderecos ?? []) as $endereco) {
    $enderecosPorCodigo[$endereco['codigo_endereco']] = $endereco;
}

$total = count($enderecosPorCodigo);
$ocupadas = 0;
$vagas = 0;
$reservadas = 0;
$indisponiveis = 0;

foreach ($enderecosPorCodigo as $endereco) {
    if (!empty($endereco['produto_id'])) {
        $ocupadas++;
    } elseif ($endereco['status'] === 'vago') {
        $vagas++;
    } elseif ($endereco['status'] === 'reservado') {
        $reservadas++;
    } elseif ($endereco['status'] === 'indisponivel') {
        $indisponiveis++;
    }
}

$codigoEnderecoProduto = null;

if (!empty($produto)) {
    foreach ($enderecosPorCodigo as $endereco) {
        if (
            !empty($endereco['produto_id']) &&
            (string) $endereco['produto_id'] === (string) $produto['id']
        ) {
            $codigoEnderecoProduto = $endereco['codigo_endereco'];
            break;
        }
    }
}
?>

<div class="page-header">
    <h2>Mapa Inteligente do Centro de Distribuição</h2>
    <p>Visualize os endereços de armazenagem e sua ocupação.</p>
</div>

<?php if (!empty($erro)): ?>
    <div class="aviso aviso-azul">
        <?= e($erro) ?>
    </div>
<?php endif; ?>

<?php if (!empty($produto)): ?>
    <div class="card" style="margin-bottom:20px;">
        <h3>Produto pesquisado: <?= e($produto['nome']) ?></h3>
        <p><strong>Código:</strong> <?= e($produto['codigo']) ?></p>
        <p><strong>Estoque:</strong> <?= (int) $produto['estoque'] ?></p>

        <?php if ($codigoEnderecoProduto): ?>
            <p><strong>Endereço:</strong> <?= e($codigoEnderecoProduto) ?></p>
        <?php else: ?>
            <p>Este produto ainda não está vinculado a uma posição do novo mapa.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="grid-resumo" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px;margin-bottom:20px;">
    <div class="card">
        <p>Total de posições</p>
        <h2><?= $total ?></h2>
    </div>
    <div class="card">
        <p>Ocupadas</p>
        <h2><?= $ocupadas ?></h2>
    </div>
    <div class="card">
        <p>Vagas</p>
        <h2><?= $vagas ?></h2>
    </div>
    <div class="card">
        <p>Reservadas</p>
        <h2><?= $reservadas ?></h2>
    </div>
    <div class="card">
        <p>Indisponíveis</p>
        <h2><?= $indisponiveis ?></h2>
    </div>
</div>

<div class="card">
    <div class="mapa-legenda" style="display:flex;flex-wrap:wrap;gap:14px;margin-bottom:20px;">
        <span>🟩 Vago</span>
        <span>🟥 Ocupado</span>
        <span>🟨 Reservado</span>
        <span>⬜ Indisponível</span>
        <span>🔵 Produto pesquisado</span>
    </div>

    <?php foreach (['A', 'B', 'C', 'D'] as $corredor): ?>
        <section style="margin-bottom:26px;">
            <h3>Corredor <?= e($corredor) ?></h3>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">
                <?php for ($estante = 1; $estante <= 2; $estante++): ?>
                    <div style="border:1px solid var(--borda);border-radius:10px;padding:12px;">
                        <h4 style="margin:0 0 12px;">
                            Estante <?= $estante ?>
                        </h4>

                        <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:6px;">
                            <?php for ($nivel = 5; $nivel >= 1; $nivel--): ?>
                                <?php
                                $codigo = $corredor . '-E' . $estante . '-N' . $nivel;
                                $endereco = $enderecosPorCodigo[$codigo] ?? null;

                                $status = $endereco['status'] ?? 'indisponivel';
                                $temProduto = $endereco && !empty($endereco['produto_id']);
                                $pesquisado = $codigoEnderecoProduto === $codigo;

                                if ($temProduto) {
                                    $cor = '#DC2626';
                                    $texto = 'Ocupado';
                                } elseif ($status === 'reservado') {
                                    $cor = '#EAB308';
                                    $texto = 'Reservado';
                                } elseif ($status === 'indisponivel' || !$endereco) {
                                    $cor = '#9CA3AF';
                                    $texto = 'Indisponível';
                                } else {
                                    $cor = '#16A34A';
                                    $texto = 'Vago';
                                }

                                if ($pesquisado) {
                                    $cor = '#2563EB';
                                }

                                $nomeProduto = $endereco['produto_nome'] ?? '';
                                ?>

                                <button
                                    type="button"
                                    title="<?= e($codigo . ' - ' . $texto . ($nomeProduto ? ' - ' . $nomeProduto : '')) ?>"
                                    onclick="mostrarEndereco(
                                        <?= json_encode($codigo) ?>,
                                        <?= json_encode($texto) ?>,
                                        <?= json_encode($nomeProduto) ?>,
                                        <?= json_encode($endereco['produto_codigo'] ?? '') ?>,
                                        <?= json_encode($endereco['produto_estoque'] ?? '') ?>
                                    )"
                                    style="
                                        min-width:0;
                                        min-height:54px;
                                        padding:6px 2px;
                                        border:<?= $pesquisado ? '3px solid #1D4ED8' : '1px solid rgba(0,0,0,.12)' ?>;
                                        border-radius:6px;
                                        background:<?= $cor ?>;
                                        color:<?= $status === 'reservado' && !$temProduto ? '#422006' : '#FFFFFF' ?>;
                                        cursor:pointer;
                                        font-size:12px;
                                    "
                                >
                                    <strong>N<?= $nivel ?></strong>
                                    <br>
                                    <?= $temProduto ? 'Ocupado' : ($status === 'vago' && $endereco ? 'Vago' : $texto) ?>
                                </button>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>

<div class="card" id="detalheEndereco" style="margin-top:20px;">
    <h3>Detalhes da posição</h3>
    <p>Selecione uma posição no mapa para consultar seus dados.</p>
</div>

<div class="acoes">
    <a href="<?= url('produtos/localizar') ?>" class="btn btn-secundario">
        Buscar Produto
    </a>
    <a href="<?= url('menu') ?>" class="btn btn-secundario">
        Voltar ao Menu
    </a>
</div>

<script>
function mostrarEndereco(codigo, status, nome, produtoCodigo, estoque) {
    const painel = document.getElementById('detalheEndereco');
    painel.replaceChildren();

    function adicionar(tag, texto) {
        const elemento = document.createElement(tag);
        elemento.textContent = texto;
        painel.appendChild(elemento);
    }

    adicionar('h3', 'Endereço ' + codigo);
    adicionar('p', 'Situação: ' + status);

    if (nome) {
        adicionar('p', 'Produto: ' + nome);
        adicionar('p', 'Código: ' + produtoCodigo);
        adicionar('p', 'Estoque: ' + estoque);
    } else {
        adicionar('p', 'Nenhum produto está associado a esta posição.');
    }
}
</script>
