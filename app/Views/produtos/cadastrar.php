
<div class="page-header">
    <h2>Cadastrar Produto</h2>
    <p>Registre um novo item e gere seu QR Code de identificação.</p>
</div>

<div class="card">
    <form action="<?= url('produtos/salvar') ?>" method="POST" id="formProduto">
        <div class="form-grid">
            <div class="form-group full">
                <label for="nome">Nome do produto</label>
                <input type="text" id="nome" name="nome"
                       placeholder="Ex: Mouse Gamer" required>
            </div>

            <div class="form-group">
                <label for="codigo">Código interno</label>
                <input type="text" id="codigo" name="codigo"
                       placeholder="Ex: 00103" required
                       autocomplete="off">
            </div>

            <div class="form-group">
                <label for="codigo_barras">Código de barras / QR Code</label>
                <input type="text" id="codigo_barras" name="codigo_barras"
                       placeholder="Gerado automaticamente" readonly required>
            </div>

            <div class="form-group">
                <label for="estoque">Quantidade em estoque</label>
                <input type="number" id="estoque" name="estoque"
                       placeholder="Ex: 10" min="0" required>
            </div>

            <div class="form-group">
                <label for="corredor">Corredor</label>
                <input type="text" id="corredor" name="corredor"
                       placeholder="Ex: A" required>
            </div>

            <div class="form-group">
                <label for="prateleira">Prateleira</label>
                <input type="number" id="prateleira" name="prateleira"
                       placeholder="Ex: 3" min="1" required>
            </div>

            <div class="form-group">
                <label for="nivel">Nível</label>
                <input type="number" id="nivel" name="nivel"
                       placeholder="Ex: 2" min="1" required>
            </div>
        </div>

        <div id="qrBox" style="display:none; margin-top:20px;">
            <div id="etiqueta" style="display:flex; align-items:center; gap:16px;
                padding:16px; border:1px solid var(--borda); border-radius:8px;
                background:white; color:#111; max-width:430px;">
                <img id="qrImg" alt="QR Code do produto"
                     style="width:140px;height:140px;object-fit:contain;">
                <div>
                    <strong id="qrNome">Produto</strong>
                    <p style="margin:8px 0;">Código: <span id="qrCodigo"></span></p>
                    <p style="margin:8px 0;">Local: <span id="qrLocal"></span></p>
                    <small>FindWare - Identificação de estoque</small>
                </div>
            </div>

            <p style="margin-top:10px; color:var(--texto-suave);">
                Confira os dados antes de imprimir. O QR Code contém o código interno.
            </p>

            <button type="button" class="btn-secundario"
                    onclick="imprimirEtiqueta()">
                Imprimir etiqueta
            </button>
        </div>

        <div class="acoes">
            <button type="submit">Salvar Produto</button>
            <a class="btn btn-secundario" href="<?= url('menu') ?>">
                Voltar ao Menu
            </a>
        </div>
    </form>
</div>

<script>
const campos = ['codigo', 'nome', 'corredor', 'prateleira', 'nivel'];

function gerarQRCode() {
    const codigo = document.getElementById('codigo').value.trim();
    const nome = document.getElementById('nome').value.trim();
    const corredor = document.getElementById('corredor').value.trim();
    const prateleira = document.getElementById('prateleira').value.trim();
    const nivel = document.getElementById('nivel').value.trim();

    document.getElementById('codigo_barras').value = codigo;

    const box = document.getElementById('qrBox');
    const img = document.getElementById('qrImg');

    if (!codigo) {
        box.style.display = 'none';
        img.removeAttribute('src');
        return;
    }

    box.style.display = 'block';
    img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data='
        + encodeURIComponent(codigo);

    document.getElementById('qrNome').textContent = nome || 'Produto';
    document.getElementById('qrCodigo').textContent = codigo;
    document.getElementById('qrLocal').textContent =
        [corredor, prateleira, nivel].some(Boolean)
            ? `${corredor || '-'} / ${prateleira || '-'} / ${nivel || '-'}`
            : 'A definir';
}

campos.forEach(id => {
    document.getElementById(id).addEventListener('input', gerarQRCode);
});

function imprimirEtiqueta() {
    const codigo = document.getElementById('codigo').value.trim();

    if (!codigo) {
        alert('Informe o código interno primeiro.');
        return;
    }

    const img = document.getElementById('qrImg');

    if (!img.complete || img.naturalWidth === 0) {
        alert('Aguarde o carregamento do QR Code antes de imprimir.');
        return;
    }

    const etiqueta = document.getElementById('etiqueta').outerHTML;
    const janela = window.open('', '_blank', 'width=600,height=500');

    if (!janela) {
        alert('Permita janelas pop-up para imprimir a etiqueta.');
        return;
    }

    janela.document.write(`
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <title>Etiqueta FindWare</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                #etiqueta { display:flex; align-items:center; gap:16px;
                    padding:12px; border:1px solid #ccc; width:380px; }
                #etiqueta img { width:130px; height:130px; }
                p { margin:8px 0; }
                @media print { body { padding:0; } }
            </style>
        </head>
        <body>${etiqueta}</body>
        </html>
    `);

    janela.document.close();
    janela.onload = () => {
        janela.focus();
        janela.print();
    };
}
</script>
