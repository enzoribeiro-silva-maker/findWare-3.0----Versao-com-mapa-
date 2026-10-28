
<div class="page-header">
    <h2>Conexão Arduino</h2>
    <p>Conecte o Arduino Uno pelo cabo USB para ler tags RFID e consultar os produtos no FindWare.</p>
</div>

<div class="layout-mapa">
    <section class="card">
        <div class="page-header" style="margin-bottom:16px;">
            <h2>Estado da conexão</h2>
            <p>Use Chrome ou Edge atualizado em localhost.</p>
        </div>

        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
            <span id="indicadorConexao" style="width:12px;height:12px;border-radius:50%;background:#9ca3af;display:inline-block;"></span>
            <strong id="estadoConexao">Desconectado</strong>
        </div>

        <div class="acoes" style="margin-top:0;">
            <button type="button" id="btnConectar">Conectar Arduino</button>
            <button type="button" id="btnDesconectar" class="btn btn-secundario" disabled>Desconectar</button>
        </div>

        <div class="form-group" style="margin-top:18px;">
            <label for="uidBusca">UID RFID para testar uma busca</label>
            <input id="uidBusca" type="text" maxlength="32" placeholder="Ex.: C3BEE62C" autocomplete="off">
        </div>

        <div class="acoes" style="margin-top:10px;">
            <button type="button" id="btnConsultarManual">Consultar UID informado</button>
        </div>

        <div class="acoes">
            <button type="button" id="btnPing" disabled>Testar comunicação</button>
            <button type="button" id="btnBuscar" disabled>Enviar busca</button>
            <button type="button" id="btnParar" class="btn btn-secundario" disabled>Parar busca</button>
        </div>

        <p id="mensagemEstado" class="status-sinal" role="status" aria-live="polite" style="margin-top:16px;">
            Conecte o Arduino ou informe um UID para testar a consulta.
        </p>

        <div style="margin-top:18px;padding-top:16px;border-top:1px solid var(--borda);">
            <strong>Protocolo serial</strong>
            <p style="margin:8px 0 0;color:var(--texto-secundario, #64748b);font-size:14px;line-height:1.6;">
                Comandos: <code>PING</code>, <code>BUSCAR:UID</code> e <code>PARAR</code>.
                Respostas: <code>PONG</code>, <code>FINDWARE_PRONTO</code>,
                <code>UID_LIDO:UID</code> e <code>ENCONTRADO:UID</code>.
            </p>
        </div>
    </section>

    <section class="card">
        <div class="page-header" style="margin-bottom:16px;">
            <h2>Monitor serial</h2>
            <p>Mensagens recebidas e enviadas pelo Arduino.</p>
        </div>

        <div id="monitorSerial" aria-live="polite"
             style="height:300px;overflow:auto;background:#111827;color:#d1fae5;border-radius:10px;padding:14px;font:13px/1.6 Consolas,Monaco,monospace;white-space:pre-wrap;overflow-wrap:anywhere;">FindWare Serial Monitor
Aguardando conexão...</div>

        <div class="acoes" style="margin-top:12px;">
            <button type="button" id="btnLimparLog" class="btn btn-secundario">Limpar mensagens</button>
        </div>
    </section>
</div>

<div id="resultadoRfid" class="card" style="margin-top:20px;" aria-live="polite">
    <h2>Resultado da leitura RFID</h2>
    <p id="detalheRfid">Aguardando leitura de uma tag...</p>

    <form id="formVincularRfid" style="display:none;margin-top:20px;">
        <h3>Vincular tag a um produto existente</h3>

        <div class="form-group">
            <label for="uidVincular">UID RFID</label>
            <input id="uidVincular" name="uid" type="text" maxlength="32" readonly required>
        </div>

        <div class="form-group">
            <label for="produtoIdVincular">ID do produto no banco</label>
            <input id="produtoIdVincular" name="produto_id" type="number" min="1" placeholder="Ex.: 2" required>
            <small>Consulte o ID na tabela produtos do Supabase.</small>
        </div>

        <button type="submit" id="btnVincularRfid">Vincular tag RFID</button>
        <p id="mensagemVinculo" role="status" aria-live="polite"></p>
    </form>
</div>

<div class="card" style="margin-top:20px;">
    <div class="page-header" style="margin-bottom:10px;">
        <h2>Como conectar</h2>
    </div>
    <ol style="padding-left:22px;line-height:1.9;">
        <li>Grave no Arduino o firmware compatível com o FindWare.</li>
        <li>Feche o Monitor Serial da IDE Arduino para liberar a porta USB.</li>
        <li>Abra o FindWare em localhost.</li>
        <li>Clique em <strong>Conectar Arduino</strong> e selecione a porta USB.</li>
        <li>Clique em <strong>Testar comunicação</strong> e confira a resposta PONG.</li>
        <li>Aproxime uma tag RFID física quando estiver com o hardware.</li>
    </ol>
    <p style="margin-top:12px;color:var(--texto-secundario, #64748b);font-size:14px;">
        A leitura física exige Arduino conectado, firmware compatível e navegador com suporte à Web Serial.
    </p>
</div>

<script>
(() => {
    const $ = (id) => document.getElementById(id);

    const statusEl = $('mensagemEstado');
    const estadoConexao = $('estadoConexao');
    const indicador = $('indicadorConexao');
    const monitor = $('monitorSerial');
    const detalheRfid = $('detalheRfid');

    const btnConectar = $('btnConectar');
    const btnDesconectar = $('btnDesconectar');
    const btnPing = $('btnPing');
    const btnBuscar = $('btnBuscar');
    const btnParar = $('btnParar');

    let port = null;
    let writer = null;
    let reader = null;
    let reading = false;
    let buffer = '';
    let keepReading = false;
    let ultimoUidConsultado = '';

    function log(text) {
        monitor.textContent +=
            '\n[' + new Date().toLocaleTimeString() + '] ' + text;
        monitor.scrollTop = monitor.scrollHeight;
    }

    function setConnected(connected) {
        estadoConexao.textContent = connected
            ? 'Conectado via USB/Serial'
            : 'Desconectado';

        indicador.style.background = connected ? '#16a34a' : '#9ca3af';
        btnConectar.disabled = connected;
        btnDesconectar.disabled = !connected;
        btnPing.disabled = !connected;
        btnBuscar.disabled = !connected;
        btnParar.disabled = !connected;
    }

    async function send(message) {
        if (!writer) {
            throw new Error('Conecte o Arduino primeiro.');
        }

        log('>> ' + message);
        await writer.write(new TextEncoder().encode(message + '\n'));
    }

    // Consulta o UID no backend PHP/Supabase.
    async function consultarRfid(uid) {
        detalheRfid.textContent =
            'Consultando a tag ' + uid + ' no banco de dados...';

        try {
            const resposta = await fetch(
                '/produtos/buscar-rfid?uid=' + encodeURIComponent(uid),
                {
                    headers: { 'Accept': 'application/json' }
                }
            );

            const dados = await resposta.json();

            if (!resposta.ok) {
                throw new Error(
                    dados.mensagem || 'Falha na consulta RFID.'
                );
            }

            if (!dados.encontrado) {
                detalheRfid.textContent =
                    'Tag ' + uid + ' sem produto vinculado.';

                $('uidVincular').value = uid;
                $('produtoIdVincular').value = '';
                $('mensagemVinculo').textContent = '';
                $('formVincularRfid').style.display = 'block';

                statusEl.textContent =
                    'Tag sem produto vinculado. Informe o ID de um produto existente.';

                log('UID sem produto vinculado: ' + uid);
                return;
            }

            $('formVincularRfid').style.display = 'none';

            const p = dados.produto;
            detalheRfid.replaceChildren();

            const titulo = document.createElement('strong');
            titulo.textContent = p.nome || 'Produto encontrado';

            const lista = document.createElement('ul');
            lista.style.lineHeight = '1.9';

            const campos = [
                ['ID', p.id],
                ['Código', p.codigo],
                ['Código de barras', p.codigo_barras],
                ['Estoque', p.estoque],
                ['Corredor', p.corredor],
                ['Prateleira', p.prateleira],
                ['Nível', p.nivel],
                ['UID RFID', p.rfid_uid || uid]
            ];

            campos.forEach(([rotulo, valor]) => {
                const item = document.createElement('li');
                item.textContent = rotulo + ': ' +
                    (valor !== null && valor !== undefined && valor !== ''
                        ? String(valor)
                        : 'Não informado');

                lista.appendChild(item);
            });

            detalheRfid.append(titulo, lista);

            statusEl.textContent =
                'Produto encontrado para a tag ' + uid + '.';

            log('Produto encontrado para UID: ' + uid);

        } catch (erro) {
            detalheRfid.textContent =
                'Erro ao consultar o produto: ' + erro.message;

            statusEl.textContent =
                'Não foi possível consultar a tag no banco de dados.';

            log('Erro na consulta RFID: ' + erro.message);
        }
    }

    // Permite consultar um UID sem Arduino conectado.
    $('btnConsultarManual').addEventListener('click', () => {
        const uid = $('uidBusca').value
            .trim()
            .replace(/[^0-9a-f]/gi, '')
            .toUpperCase();

        if (!uid) {
            statusEl.textContent = 'Informe um UID RFID válido.';
            return;
        }

        $('uidBusca').value = uid;
        ultimoUidConsultado = uid;
        consultarRfid(uid);
    });

    function processLine(line) {
        line = line.trim();
        if (!line) return;

        log('<< ' + line);

        if (line === 'PONG') {
            statusEl.textContent =
                'Comunicação confirmada: Arduino respondeu PONG.';

        } else if (line === 'FINDWARE_PRONTO') {
            statusEl.textContent =
                'Firmware FindWare detectado e pronto.';

        } else if (line.startsWith('ENCONTRADO:')) {
            const uid = line.substring('ENCONTRADO:'.length).trim();
            statusEl.textContent =
                'Tag correspondente encontrada: ' + uid;

        } else if (line.startsWith('UID_LIDO:')) {
            const uid = line.substring('UID_LIDO:'.length)
                .trim()
                .replace(/[^0-9a-f]/gi, '')
                .toUpperCase();

            if (!uid) {
                statusEl.textContent = 'O Arduino enviou um UID inválido.';
                return;
            }

            $('uidBusca').value = uid;
            statusEl.textContent =
                'Tag lida: ' + uid + '. Consultando produto...';

            if (uid !== ultimoUidConsultado) {
                ultimoUidConsultado = uid;
                consultarRfid(uid);
            }

        } else if (line.startsWith('ERRO:')) {
            statusEl.textContent = 'Arduino informou: ' + line;
        }
    }

    async function readLoop() {
        if (!port || !port.readable || reading) return;

        reading = true;
        keepReading = true;
        const decoder = new TextDecoder();

        try {
            while (port && port.readable && keepReading) {
                reader = port.readable.getReader();

                try {
                    while (keepReading) {
                        const { value, done } = await reader.read();
                        if (done) break;

                        buffer += decoder.decode(value, { stream: true });
                        const linhas = buffer.split(/\r?\n/);
                        buffer = linhas.pop() || '';
                        linhas.forEach(processLine);
                    }
                } finally {
                    reader.releaseLock();
                    reader = null;
                }
            }
        } catch (erro) {
            if (keepReading) {
                log('Erro de leitura: ' + erro.message);
                statusEl.textContent =
                    'Erro na leitura serial: ' + erro.message;
            }
        } finally {
            reading = false;
        }
    }

    async function connect() {
        if (!('serial' in navigator)) {
            statusEl.textContent =
                'Web Serial indisponível. Use Chrome ou Edge atualizado em localhost.';
            return;
        }

        try {
            port = await navigator.serial.requestPort();
            await port.open({ baudRate: 9600 });
            writer = port.writable.getWriter();

            setConnected(true);
            statusEl.textContent =
                'Porta aberta. Teste a comunicação com o Arduino.';

            log('Porta serial aberta a 9600 baud.');
            readLoop();

        } catch (erro) {
            statusEl.textContent =
                'Não foi possível conectar: ' + erro.message;
            log('Falha na conexão: ' + erro.message);

            if (port) {
                try {
                    await port.close();
                } catch (_) {}
            }

            port = null;
            writer = null;
            setConnected(false);
        }
    }

    async function disconnect() {
        keepReading = false;

        try {
            if (reader) {
                await reader.cancel().catch(() => {});
            }

            if (writer) {
                writer.releaseLock();
                writer = null;
            }

            if (port) {
                await port.close();
                port = null;
            }

            statusEl.textContent = 'Arduino desconectado.';
            log('Porta serial fechada.');

        } catch (erro) {
            statusEl.textContent =
                'Erro ao desconectar: ' + erro.message;
            log('Falha ao desconectar: ' + erro.message);

        } finally {
            setConnected(false);
        }
    }

    // Envia o UID e o ID do produto para o backend.
    $('formVincularRfid').addEventListener('submit', async (event) => {
        event.preventDefault();

        const uid = $('uidVincular').value.trim().toUpperCase();
        const produtoId = $('produtoIdVincular').value.trim();
        const mensagem = $('mensagemVinculo');
        const botao = $('btnVincularRfid');

        if (!uid || !produtoId ||
            !/^\d+$/.test(produtoId) ||
            Number(produtoId) <= 0) {
            mensagem.textContent =
                'Informe um UID e um ID de produto válido.';
            return;
        }

        botao.disabled = true;
        mensagem.textContent = 'Salvando vínculo...';

        try {
            const corpo = new URLSearchParams();
            corpo.set('uid', uid);
            corpo.set('produto_id', produtoId);

            const resposta = await fetch('/produtos/vincular-rfid', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                },
                body: corpo.toString()
            });

            const dados = await resposta.json();

            if (!resposta.ok || !dados.sucesso) {
                throw new Error(
                    dados.mensagem || 'Não foi possível vincular a tag.'
                );
            }

            mensagem.textContent = dados.mensagem;
            statusEl.textContent = 'Tag RFID vinculada com sucesso.';
            log('Tag ' + uid + ' vinculada ao produto ID ' + produtoId + '.');

            // Atualiza o resultado para mostrar os dados do produto vinculado.
            await consultarRfid(uid);

        } catch (erro) {
            mensagem.textContent = 'Erro: ' + erro.message;
        } finally {
            botao.disabled = false;
        }
    });

    btnConectar.addEventListener('click', connect);
    btnDesconectar.addEventListener('click', disconnect);

    btnPing.addEventListener('click', () => {
        send('PING').catch(erro => {
            statusEl.textContent = erro.message;
        });
    });

    btnBuscar.addEventListener('click', () => {
        const uid = $('uidBusca').value
            .trim()
            .replace(/[^0-9a-f]/gi, '')
            .toUpperCase();

        if (!uid) {
            statusEl.textContent =
                'Informe o UID RFID que deseja procurar.';
            return;
        }

        ultimoUidConsultado = uid;
        consultarRfid(uid);

        send('BUSCAR:' + uid).then(() => {
            statusEl.textContent =
                'Busca enviada ao Arduino. Aproxime a tag do leitor.';
        }).catch(erro => {
            statusEl.textContent = erro.message;
        });
    });

    btnParar.addEventListener('click', () => {
        send('PARAR').then(() => {
            statusEl.textContent =
                'Comando para parar a busca enviado.';
        }).catch(erro => {
            statusEl.textContent = erro.message;
        });
    });

    $('btnLimparLog').addEventListener('click', () => {
        monitor.textContent = 'FindWare Serial Monitor';
    });

    if ('serial' in navigator) {
        navigator.serial.addEventListener('disconnect', () => {
            log('Dispositivo serial desconectado.');
            port = null;
            writer = null;
            keepReading = false;
            setConnected(false);
            statusEl.textContent = 'O dispositivo foi desconectado.';
        });
    }

    setConnected(false);
})();
</script>
