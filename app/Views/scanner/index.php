<div class="page-header">
    <h2>Scanner do Centro de Distribuição</h2>
    <p>Escolha a operação desejada e escaneie o QR Code ou código do produto.</p>
</div>

<div class="scanner-layout">

    <div class="card">

        <div class="page-header" style="margin-bottom:16px;">
            <h2>Operação</h2>
            <p>Selecione o tipo de ação antes de abrir a câmera.</p>
        </div>

        <div class="operacoes-scanner">
            <button type="button" class="operacao-btn" id="btnEntrada" onclick="selecionarOperacao('entrada')"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 7 5 5-5 5"/><path d="M18 12H3"/></svg></span> Entrada de Produto</button>
            <button type="button" class="operacao-btn" id="btnSaida" onclick="selecionarOperacao('saida')"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 12H6"/><path d="m11 7-5 5 5 5"/><path d="M5 12h14"/></svg></span> Saída de Produto</button>
            <button type="button" class="operacao-btn" id="btnLocalizar" onclick="selecionarOperacao('localizar')"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s6-5.4 6-10a6 6 0 1 0-12 0c0 4.6 6 10 6 10Z"/><circle cx="12" cy="11" r="2"/></svg></span> Localizar Produto</button>
        </div>

        <div class="aviso aviso-azul" id="statusOperacao" style="margin-top:18px;">
            Nenhuma operação selecionada.
        </div>

        <div class="acoes">
            <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
        </div>

    </div>

    <div class="card" style="text-align:center;">

        <div class="page-header" style="margin-bottom:16px;">
            <h2>Leitura do QR Code</h2>
            <p>Abra a câmera traseira e aponte para o código do produto.</p>
        </div>

        <button type="button" onclick="iniciarCamera()">Abrir Câmera Traseira</button>

        <div id="reader"></div>

        <div class="aviso aviso-azul" id="resultado" style="text-align:left;">
            Escolha a operação e escaneie o QR Code.
        </div>

    </div>

</div>

<audio id="beep" src="<?= asset('beep.mp3') ?>"></audio>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let scanner;
let operacao = '';

function selecionarOperacao(tipo) {
    operacao = tipo;

    ['btnEntrada', 'btnSaida', 'btnLocalizar'].forEach(id => document.getElementById(id).classList.remove('ativo'));

    const nomes = { entrada: 'Entrada de Produto', saida: 'Saída de Produto', localizar: 'Localizar Produto' };
    const idBotao = { entrada: 'btnEntrada', saida: 'btnSaida', localizar: 'btnLocalizar' };

    document.getElementById(idBotao[tipo]).classList.add('ativo');
    document.getElementById('statusOperacao').innerHTML = 'Operação selecionada: ' + nomes[tipo];
    document.getElementById('resultado').innerHTML = 'Agora abra a câmera e escaneie o QR Code.';
}

function onScanSuccess(codigo) {
    if (operacao === '') {
        alert('Escolha Entrada, Saída ou Localizar primeiro.');
        return;
    }

    document.getElementById('beep').play();
    document.getElementById('resultado').innerHTML = 'Código lido: ' + codigo;

    const rotas = {
        entrada: '<?= url('scanner/entrada') ?>?codigo=',
        saida: '<?= url('scanner/saida') ?>?codigo=',
        localizar: '<?= url('scanner/localizar') ?>?codigo=',
    };

    window.location.href = rotas[operacao] + encodeURIComponent(codigo);
}

function iniciarCamera() {
    if (operacao === '') {
        alert('Escolha uma operação antes de abrir a câmera.');
        return;
    }

    if (scanner) return;

    scanner = new Html5Qrcode('reader');
    scanner.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: 250 },
        onScanSuccess
    ).catch(function () {
        document.getElementById('resultado').innerHTML = 'Não foi possível abrir a câmera.';
        scanner = null;
    });
}
</script>
