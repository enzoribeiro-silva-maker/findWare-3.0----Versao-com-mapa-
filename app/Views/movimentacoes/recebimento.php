
<div class="page-header">
    <h2>Recebimento de Produtos</h2>
    <p>Registre entradas e selecione o endereço de armazenagem no CD.</p>
</div>

<div class="card">
    <form action="<?= url('movimentacoes/recebimento') ?>" method="POST" id="formRecebimento">

        <div class="aviso aviso-azul">
            <label for="codigo_barras" style="margin-bottom:8px;">
                Código de barras / QR Code / UID
            </label>

            <input
                type="text"
                id="codigo_barras"
                name="codigo_barras"
                placeholder="Digite ou leia o código"
                value="<?= e($codigo ?? '') ?>"
                autofocus
                required
                autocomplete="off"
                style="margin-bottom:10px;"
            >

            <button
                type="button"
                class="btn-secundario"
                onclick="abrirScanner()"
            >
                Ler QR Code com a câmera
            </button>

            <div id="reader" style="max-width:340px; margin-top:14px;"></div>

            <p id="statusProduto" role="status" aria-live="polite"
               style="margin-top:10px;"></p>
        </div>

        <div class="form-grid">
            <div class="form-group full">
                <label for="produto">Produto</label>
                <input
                    type="text"
                    id="produto"
                    name="produto"
                    placeholder="Nome do produto"
                    required
                >
            </div>

            <div class="form-group">
                <label for="quantidade">Quantidade recebida</label>
                <input
                    type="number"
                    id="quantidade"
                    name="quantidade"
                    placeholder="Ex: 10"
                    min="1"
                    step="1"
                    required
                >
            </div>

            <div class="form-group">
                <label for="corredor">Corredor</label>
                <input
                    type="text"
                    id="corredor"
                    name="corredor"
                    placeholder="Ex: A"
                    readonly
                >
            </div>

            <div class="form-group">
                <label for="prateleira">Estante</label>
                <input
                    type="number"
                    id="prateleira"
                    name="prateleira"
                    placeholder="Ex: 1 ou 2"
                    min="1"
                    max="2"
                    readonly
                >
            </div>

            <div class="form-group">
                <label for="nivel">Nível</label>
                <input
                    type="number"
                    id="nivel"
                    name="nivel"
                    placeholder="Ex: 3"
                    min="1"
                    max="5"
                    readonly
                >
            </div>

            <div class="form-group full">
                <label for="endereco_id">Posição de armazenagem no CD</label>

                <select name="endereco_id" id="endereco_id">
                    <option value="">Selecione uma posição vaga</option>

                    <?php foreach (($enderecosVagos ?? []) as $endereco): ?>
                        <option
                            value="<?= (int) $endereco['id'] ?>"
                            data-corredor="<?= e($endereco['corredor']) ?>"
                            data-estante="<?= (int) $endereco['estante'] ?>"
                            data-nivel="<?= (int) $endereco['nivel'] ?>"
                        >
                            <?= e($endereco['codigo_endereco']) ?>
                            — Corredor <?= e($endereco['corredor']) ?>,
                            Estante <?= (int) $endereco['estante'] ?>,
                            Nível <?= (int) $endereco['nivel'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <small>
                    Produto novo ou sem endereço: selecione uma posição vaga.
                    Produto já armazenado: a posição atual será mantida.
                </small>

                <p id="statusEndereco" role="status" aria-live="polite"
                   style="margin-top:8px;"></p>
            </div>
        </div>

        <div class="acoes">
            <button type="submit">Salvar Recebimento</button>

            <a href="<?= url('menu') ?>" class="btn btn-secundario">
                Voltar ao Menu
            </a>
        </div>
    </form>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
const campoCodigo = document.getElementById('codigo_barras');
const campoProduto = document.getElementById('produto');
const campoCorredor = document.getElementById('corredor');
const campoPrateleira = document.getElementById('prateleira');
const campoNivel = document.getElementById('nivel');
const campoEndereco = document.getElementById('endereco_id');
const statusProduto = document.getElementById('statusProduto');
const statusEndereco = document.getElementById('statusEndereco');

let scanner = null;
let temporizadorBusca = null;
let ultimaBusca = '';
let numeroRequisicao = 0;
let produtoEncontrado = false;
let enderecoAtualProduto = null;

function mostrarStatus(mensagem, tipo = '') {
    statusProduto.textContent = mensagem;
    statusProduto.style.color =
        tipo === 'erro' ? '#b91c1c' :
        tipo === 'sucesso' ? '#15803d' : '';
}

function mostrarStatusEndereco(mensagem, tipo = '') {
    statusEndereco.textContent = mensagem;
    statusEndereco.style.color =
        tipo === 'erro' ? '#b91c1c' :
        tipo === 'sucesso' ? '#15803d' : '';
}

function limparDadosProduto() {
    campoProduto.value = '';
    campoCorredor.value = '';
    campoPrateleira.value = '';
    campoNivel.value = '';
    produtoEncontrado = false;
    enderecoAtualProduto = null;
}

function limparEndereco() {
    campoEndereco.value = '';
    campoCorredor.value = '';
    campoPrateleira.value = '';
    campoNivel.value = '';
    mostrarStatusEndereco('');
}

function sincronizarEnderecoComCampos() {
    const corredor = campoCorredor.value.trim().toUpperCase();
    const estante = campoPrateleira.value.trim();
    const nivel = campoNivel.value.trim();

    if (!corredor || !estante || !nivel) {
        campoEndereco.value = '';
        return;
    }

    const opcao = Array.from(campoEndereco.options).find(item =>
        item.value !== '' &&
        item.dataset.corredor === corredor &&
        item.dataset.estante === estante &&
        item.dataset.nivel === nivel
    );

    if (opcao) {
        campoEndereco.value = opcao.value;
        mostrarStatusEndereco(
            'Posição disponível selecionada: ' + opcao.textContent.trim(),
            'sucesso'
        );
    } else {
        campoEndereco.value = '';

        if (produtoEncontrado && enderecoAtualProduto) {
            mostrarStatusEndereco(
                'Produto já armazenado em ' + enderecoAtualProduto +
                '. A localização atual será mantida.'
            );
        } else if (produtoEncontrado) {
            mostrarStatusEndereco(
                'Produto encontrado, mas sem posição no mapa. Selecione uma vaga.'
            );
        } else {
            mostrarStatusEndereco(
                'Selecione uma posição vaga na lista.'
            );
        }
    }
}

campoEndereco.addEventListener('change', () => {
    const opcao = campoEndereco.selectedOptions[0];

    if (!opcao || !opcao.value) {
        if (produtoEncontrado && enderecoAtualProduto) {
            mostrarStatusEndereco(
                'A posição atual do produto será mantida: ' +
                enderecoAtualProduto + '.'
            );
        } else {
            mostrarStatusEndereco('');
        }
        return;
    }

    campoCorredor.value = opcao.dataset.corredor;
    campoPrateleira.value = opcao.dataset.estante;
    campoNivel.value = opcao.dataset.nivel;

    mostrarStatusEndereco(
        'Posição selecionada: ' + opcao.textContent.trim(),
        'sucesso'
    );
});

function buscarProduto(codigo) {
    codigo = codigo.trim();

    if (!codigo) {
        ultimaBusca = '';
        numeroRequisicao++;
        limparDadosProduto();
        limparEndereco();
        mostrarStatus('');
        return;
    }

    if (codigo === ultimaBusca) return;

    ultimaBusca = codigo;
    const requisicaoAtual = ++numeroRequisicao;

    mostrarStatus('Consultando produto...');

    fetch('<?= url('produtos/buscar-codigo') ?>?codigo='
        + encodeURIComponent(codigo), {
            headers: { 'Accept': 'application/json' }
        })
        .then(resposta => {
            if (!resposta.ok) {
                throw new Error('Falha na consulta do produto.');
            }
            return resposta.json();
        })
        .then(data => {
            if (requisicaoAtual !== numeroRequisicao) return;

            if (data.encontrado) {
                produtoEncontrado = true;
                campoProduto.value = data.nome ?? '';

                enderecoAtualProduto = data.codigo_endereco ?? null;

                if (enderecoAtualProduto) {
                    // Produto já associado a uma posição no mapa.
                    campoCorredor.value = data.corredor ?? '';
                    campoPrateleira.value = data.prateleira ?? '';
                    campoNivel.value = data.nivel ?? '';

                    campoEndereco.value = '';

                    mostrarStatusEndereco(
                        'Localização atual: ' + enderecoAtualProduto +
                        '. Ela será mantida durante a reposição.'
                    );
                } else {
                    // Produto existente sem posição associada.
                    campoCorredor.value = '';
                    campoPrateleira.value = '';
                    campoNivel.value = '';
                    campoEndereco.value = '';

                    mostrarStatusEndereco(
                        'Este produto ainda não tem posição no mapa. Selecione uma vaga.'
                    );
                }

                mostrarStatus(
                    'Produto encontrado! Informe a quantidade recebida.',
                    'sucesso'
                );
            } else {
                limparDadosProduto();
                limparEndereco();

                mostrarStatus(
                    'Produto novo. Preencha o nome e selecione uma posição vaga.'
                );
                campoProduto.focus();
            }
        })
        .catch(() => {
            if (requisicaoAtual !== numeroRequisicao) return;

            mostrarStatus(
                'Não foi possível consultar o produto. Verifique a conexão e tente novamente.',
                'erro'
            );
        });
}

campoCodigo.addEventListener('input', () => {
    clearTimeout(temporizadorBusca);

    numeroRequisicao++;
    ultimaBusca = '';
    limparDadosProduto();
    limparEndereco();

    const codigo = campoCodigo.value.trim();

    if (!codigo) {
        mostrarStatus('');
        return;
    }

    mostrarStatus('Aguardando leitura completa...');

    temporizadorBusca = setTimeout(() => {
        buscarProduto(codigo);
    }, 400);
});

campoCodigo.addEventListener('change', () => {
    clearTimeout(temporizadorBusca);
    buscarProduto(campoCodigo.value);
});

async function abrirScanner() {
    if (scanner) return;

    if (typeof Html5Qrcode === 'undefined') {
        mostrarStatus('Não foi possível carregar o leitor de QR Code.', 'erro');
        return;
    }

    scanner = new Html5Qrcode('reader');

    try {
        await scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 250 },
            async codigoLido => {
                campoCodigo.value = codigoLido;
                clearTimeout(temporizadorBusca);
                ultimaBusca = '';
                buscarProduto(codigoLido);

                if (scanner) {
                    const leitorAtual = scanner;
                    scanner = null;

                    try {
                        await leitorAtual.stop();
                        leitorAtual.clear();
                    } catch (erro) {
                        console.warn(
                            'Não foi possível encerrar a câmera.',
                            erro
                        );
                    }
                }
            },
            () => {}
        );
    } catch (erro) {
        scanner = null;
        mostrarStatus(
            'Não foi possível abrir a câmera. Verifique a permissão do navegador.',
            'erro'
        );
    }
}

document.getElementById('formRecebimento').addEventListener('submit', evento => {
    if (!campoCodigo.value.trim() || !campoProduto.value.trim()) {
        evento.preventDefault();
        mostrarStatus('Informe o código e o nome do produto.', 'erro');
        return;
    }

    // Para produtos novos ou existentes sem endereço, exige uma vaga.
    // Para produto que já tem endereço, a posição atual é mantida.
    if (!campoEndereco.value && !enderecoAtualProduto) {
        evento.preventDefault();

        mostrarStatusEndereco(
            'Selecione uma posição vaga do CD antes de salvar.',
            'erro'
        );

        campoEndereco.focus();
    }
});

if (campoCodigo.value.trim()) {
    buscarProduto(campoCodigo.value);
}
</script>
