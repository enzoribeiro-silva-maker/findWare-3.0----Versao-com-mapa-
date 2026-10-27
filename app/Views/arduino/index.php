<div class="page-header">
    <h2>Conexão Arduino</h2>
    <p>Conecte o Arduino Uno ao FindWare pelo cabo USB, acompanhe a comunicação serial e teste a leitura RFID.</p>
</div>

<div class="layout-mapa">
    <section class="card">
        <div class="page-header" style="margin-bottom:16px;">
            <h2>Estado da conexão</h2>
            <p>A conexão é feita pelo navegador com sua autorização. Use Chrome ou Edge atualizado em localhost.</p>
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

        <div class="acoes">
            <button type="button" id="btnPing" disabled>Testar comunicação</button>
            <button type="button" id="btnBuscar" disabled>Enviar busca</button>
            <button type="button" id="btnParar" class="btn btn-secundario" disabled>Parar busca</button>
        </div>

        <p id="mensagemEstado" class="status-sinal" role="status" aria-live="polite" style="margin-top:16px;">
            Conecte o Arduino para começar. Sem hardware, você ainda pode consultar esta tela, mas a conexão real não poderá ser validada.
        </p>

        <div style="margin-top:18px;padding-top:16px;border-top:1px solid var(--borda);">
            <strong>Protocolo esperado</strong>
            <p style="margin:8px 0 0;color:var(--texto-secundario, #64748b);font-size:14px;line-height:1.6;">
                O firmware deve aceitar <code>PING</code>, <code>BUSCAR:UID</code> e <code>PARAR</code>. Respostas esperadas: <code>PONG</code>, <code>FINDWARE_PRONTO</code>, <code>UID_LIDO:UID</code> e <code>ENCONTRADO:UID</code>.
            </p>
        </div>
    </section>

    <section class="card">
        <div class="page-header" style="margin-bottom:16px;">
            <h2>Monitor serial</h2>
            <p>Mensagens recebidas do Arduino aparecerão aqui.</p>
        </div>
        <div id="monitorSerial" aria-live="polite" style="height:300px;overflow:auto;background:#111827;color:#d1fae5;border-radius:10px;padding:14px;font:13px/1.6 Consolas,Monaco,monospace;white-space:pre-wrap;overflow-wrap:anywhere;">FindWare Serial Monitor\nAguardando conexão...</div>
        <div class="acoes" style="margin-top:12px;">
            <button type="button" id="btnLimparLog" class="btn btn-secundario">Limpar mensagens</button>
        </div>
    </section>
</div>

<div class="card" style="margin-top:20px;">
    <div class="page-header" style="margin-bottom:10px;">
        <h2>Como conectar quando estiver com o Arduino</h2>
    </div>
    <ol style="padding-left:22px;line-height:1.9;">
        <li>Grave no Arduino o firmware compatível com o protocolo do FindWare.</li>
        <li>Feche o Monitor Serial da IDE Arduino para liberar a porta USB.</li>
        <li>Abra o FindWare em <code>http://localhost</code> ou no endereço local que você já usa.</li>
        <li>Clique em <strong>Conectar Arduino</strong> e selecione a porta correspondente.</li>
        <li>Clique em <strong>Testar comunicação</strong>. O firmware deverá responder <code>PONG</code>.</li>
        <li>Informe o UID real da tag e clique em <strong>Enviar busca</strong>.</li>
    </ol>
    <p style="margin-top:12px;color:var(--texto-secundario, #64748b);font-size:14px;">Esta página já contém a interface de conexão, mas só confirmará uma conexão real quando o Arduino estiver ligado e executando firmware compatível. A API Web Serial requer navegador compatível e contexto seguro, como localhost.</p>
</div>

<script>
(() => {
    const $ = (id) => document.getElementById(id);
    const statusEl = $('mensagemEstado');
    const estadoConexao = $('estadoConexao');
    const indicador = $('indicadorConexao');
    const monitor = $('monitorSerial');
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

    function log(text) {
        const time = new Date().toLocaleTimeString();
        monitor.textContent += `\n[${time}] ${text}`;
        monitor.scrollTop = monitor.scrollHeight;
    }

    function setConnected(connected) {
        estadoConexao.textContent = connected ? 'Conectado via USB/Serial' : 'Desconectado';
        indicador.style.background = connected ? '#16a34a' : '#9ca3af';
        btnConectar.disabled = connected;
        btnDesconectar.disabled = !connected;
        btnPing.disabled = !connected;
        btnBuscar.disabled = !connected;
        btnParar.disabled = !connected;
    }

    async function send(message) {
        if (!writer) throw new Error('Conecte o Arduino primeiro.');
        log('>> ' + message);
        await writer.write(new TextEncoder().encode(message + '\n'));
    }

    function processLine(line) {
        line = line.trim();
        if (!line) return;
        log('<< ' + line);
        if (line === 'PONG') {
            statusEl.textContent = 'Comunicação confirmada: o Arduino respondeu PONG.';
        } else if (line === 'FINDWARE_PRONTO') {
            statusEl.textContent = 'Firmware FindWare detectado e pronto para receber comandos.';
        } else if (line.startsWith('ENCONTRADO:')) {
            statusEl.textContent = 'Tag correspondente encontrada: ' + line.substring('ENCONTRADO:'.length);
        } else if (line.startsWith('UID_LIDO:')) {
            statusEl.textContent = 'Tag lida: ' + line.substring('UID_LIDO:'.length);
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
                        const lines = buffer.split(/\r?\n/);
                        buffer = lines.pop() || '';
                        lines.forEach(processLine);
                    }
                } finally {
                    reader.releaseLock();
                    reader = null;
                }
            }
        } catch (err) {
            if (keepReading) {
                log('Erro de leitura: ' + err.message);
                statusEl.textContent = 'Erro na leitura serial: ' + err.message;
            }
        } finally {
            reading = false;
        }
    }

    async function connect() {
        if (!('serial' in navigator)) {
            statusEl.textContent = 'Web Serial não está disponível. Use Chrome ou Edge atualizado em localhost.';
            return;
        }
        try {
            port = await navigator.serial.requestPort();
            await port.open({ baudRate: 9600 });
            writer = port.writable.getWriter();
            setConnected(true);
            statusEl.textContent = 'Porta aberta. Teste a comunicação com o Arduino.';
            log('Porta serial aberta a 9600 baud.');
            readLoop();
        } catch (err) {
            statusEl.textContent = 'Não foi possível conectar: ' + err.message;
            log('Falha na conexão: ' + err.message);
            if (port) {
                try { await port.close(); } catch (_) {}
            }
            port = null;
            writer = null;
            setConnected(false);
        }
    }

    async function disconnect() {
        keepReading = false;
        try {
            if (writer) {
                writer.releaseLock();
                writer = null;
            }
            if (reader) {
                await reader.cancel();
                reader = null;
            }
            if (port) {
                await port.close();
                port = null;
            }
            statusEl.textContent = 'Arduino desconectado.';
            log('Porta serial fechada.');
        } catch (err) {
            statusEl.textContent = 'Erro ao desconectar: ' + err.message;
            log('Falha ao desconectar: ' + err.message);
        } finally {
            setConnected(false);
        }
    }

    btnConectar.addEventListener('click', connect);
    btnDesconectar.addEventListener('click', disconnect);
    btnPing.addEventListener('click', () => send('PING').catch(err => statusEl.textContent = err.message));
    btnBuscar.addEventListener('click', () => {
        const uid = $('uidBusca').value.trim().replace(/[^0-9a-f]/gi, '').toUpperCase();
        if (!uid) {
            statusEl.textContent = 'Informe o UID RFID da tag que deseja procurar.';
            return;
        }
        send('BUSCAR:' + uid).then(() => {
            statusEl.textContent = 'Busca enviada para o Arduino. Aproxime as tags do leitor.';
        }).catch(err => statusEl.textContent = err.message);
    });
    btnParar.addEventListener('click', () => send('PARAR').then(() => {
        statusEl.textContent = 'Comando para parar a busca enviado.';
    }).catch(err => statusEl.textContent = err.message));
    $('btnLimparLog').addEventListener('click', () => monitor.textContent = 'FindWare Serial Monitor');

    if ('serial' in navigator) {
        navigator.serial.addEventListener('disconnect', () => {
            log('Dispositivo serial desconectado.');
            port = null;
            writer = null;
            setConnected(false);
            statusEl.textContent = 'O dispositivo foi desconectado.';
        });
    }
})();
</script>
