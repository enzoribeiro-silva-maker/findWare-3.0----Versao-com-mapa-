<?php
require_once __DIR__ . '/../Models/Produto.php';
require_once __DIR__ . '/../Models/Movimentacao.php';

class MovimentacaoController extends Controller
{
    
public function recebimentoForm(): void
{
    require_once __DIR__ . '/../Models/EnderecoArmazenagem.php';

    $codigo = trim($_GET['codigo'] ?? '');

    $enderecoModel = new EnderecoArmazenagem();
    $enderecosVagos = $enderecoModel->vagas();

    $this->view('movimentacoes/recebimento', compact(
        'codigo',
        'enderecosVagos'
    ));
}


    
public function salvarRecebimento(): void
{
    require_once __DIR__ . '/../Core/Database.php';
    require_once __DIR__ . '/../Models/EnderecoArmazenagem.php';

    $pdo = Database::getConnection();
    $movimentacaoModel = new Movimentacao();

    $codigo = trim($_POST['codigo_barras'] ?? '');
    $nome = trim($_POST['produto'] ?? '');
    $quantidade = filter_var(
        $_POST['quantidade'] ?? null,
        FILTER_VALIDATE_INT
    );
    $enderecoId = filter_var(
        $_POST['endereco_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($codigo === '' || $quantidade === false ||
        $quantidade === null || $quantidade < 1) {
        $this->view('mensagem', [
            'status' => 'erro',
            'titulo' => 'Dados inválidos',
            'mensagem' => 'Informe o código do produto e uma quantidade inteira maior que zero.',
            'botoes' => [
                ['rota' => 'movimentacoes/recebimento', 'texto' => 'Tentar novamente'],
                ['rota' => 'menu', 'texto' => 'Voltar ao Menu', 'secundario' => true],
            ],
        ]);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Localiza o produto pelo código e bloqueia sua linha.
        $stmt = $pdo->prepare("
            SELECT *
            FROM public.produtos
            WHERE codigo_barras = :codigo_barras
               OR codigo = :codigo
            LIMIT 1
            FOR UPDATE
        ");
        $stmt->execute([
            'codigo_barras' => $codigo,
            'codigo' => $codigo,
        ]);

        $produtoExistente = $stmt->fetch(PDO::FETCH_ASSOC);
        $enderecoModel = new EnderecoArmazenagem();

        if ($produtoExistente) {
            $produtoId = (int) $produtoExistente['id'];

            // Verifica se o produto já tem uma posição no mapa.
            $stmt = $pdo->prepare("
                SELECT codigo_endereco
                FROM public.enderecos_armazenagem
                WHERE produto_id = :produto_id
                LIMIT 1
            ");
            $stmt->execute(['produto_id' => $produtoId]);
            $enderecoAtual = $stmt->fetch(PDO::FETCH_ASSOC);

            // Produto já armazenado: mantém a posição atual.
            if ($enderecoAtual) {
                $localizacao = $enderecoAtual['codigo_endereco'];

                $stmt = $pdo->prepare("
                    UPDATE public.produtos
                    SET estoque = estoque + :quantidade
                    WHERE id = :id
                ");
                $stmt->execute([
                    'quantidade' => $quantidade,
                    'id' => $produtoId,
                ]);
            } else {
                // Produto sem posição: precisa ocupar uma vaga.
                if (!$enderecoId) {
                    throw new RuntimeException(
                        'Este produto ainda não possui endereço. Selecione uma posição vaga.'
                    );
                }

                $stmt = $pdo->prepare("
                    SELECT id, codigo_endereco, corredor, estante, nivel
                    FROM public.enderecos_armazenagem
                    WHERE id = :id
                      AND produto_id IS NULL
                      AND status = 'vago'
                    FOR UPDATE
                ");
                $stmt->execute(['id' => $enderecoId]);
                $endereco = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$endereco) {
                    throw new RuntimeException(
                        'A posição selecionada não está mais disponível. Atualize a página e escolha outra.'
                    );
                }

                $stmt = $pdo->prepare("
                    UPDATE public.enderecos_armazenagem
                    SET produto_id = :produto_id
                    WHERE id = :endereco_id
                      AND produto_id IS NULL
                      AND status = 'vago'
                ");
                $stmt->execute([
                    'produto_id' => $produtoId,
                    'endereco_id' => $enderecoId,
                ]);

                if ($stmt->rowCount() !== 1) {
                    throw new RuntimeException(
                        'Não foi possível reservar a posição selecionada.'
                    );
                }

                $localizacao = $endereco['codigo_endereco'];

                $stmt = $pdo->prepare("
                    UPDATE public.produtos
                    SET estoque = estoque + :quantidade,
                        corredor = :corredor,
                        prateleira = :prateleira,
                        nivel = :nivel
                    WHERE id = :id
                ");
                $stmt->execute([
                    'quantidade' => $quantidade,
                    'corredor' => $endereco['corredor'],
                    'prateleira' => $endereco['estante'],
                    'nivel' => $endereco['nivel'],
                    'id' => $produtoId,
                ]);
            }

            $nomeFinal = $produtoExistente['nome'];
        } else {
            // Produto novo: exige nome e posição disponível.
            if ($nome === '' || !$enderecoId) {
                throw new RuntimeException(
                    'Para cadastrar um produto novo, informe o nome e selecione uma posição vaga.'
                );
            }

            $stmt = $pdo->prepare("
                SELECT id, codigo_endereco, corredor, estante, nivel
                FROM public.enderecos_armazenagem
                WHERE id = :id
                  AND produto_id IS NULL
                  AND status = 'vago'
                FOR UPDATE
            ");
            $stmt->execute(['id' => $enderecoId]);
            $endereco = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$endereco) {
                throw new RuntimeException(
                    'A posição selecionada não está mais disponível. Atualize a página e escolha outra.'
                );
            }

            $stmt = $pdo->prepare("
                INSERT INTO public.produtos
                    (nome, codigo, codigo_barras, corredor, prateleira, nivel, estoque)
                VALUES
                    (:nome, :codigo, :codigo_barras, :corredor, :prateleira, :nivel, :estoque)
                RETURNING id
            ");
            $stmt->execute([
                'nome' => $nome,
                'codigo' => $codigo,
                'codigo_barras' => $codigo,
                'corredor' => $endereco['corredor'],
                'prateleira' => $endereco['estante'],
                'nivel' => $endereco['nivel'],
                'estoque' => $quantidade,
            ]);

            $produtoId = (int) $stmt->fetchColumn();

            $stmt = $pdo->prepare("
                UPDATE public.enderecos_armazenagem
                SET produto_id = :produto_id
                WHERE id = :endereco_id
                  AND produto_id IS NULL
                  AND status = 'vago'
            ");
            $stmt->execute([
                'produto_id' => $produtoId,
                'endereco_id' => $enderecoId,
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Não foi possível ocupar a posição selecionada.'
                );
            }

            $nomeFinal = $nome;
            $localizacao = $endereco['codigo_endereco'];
        }

        // Registra o histórico dentro da mesma transação.
        $movimentacaoModel->registrar(
            $codigo,
            $nomeFinal,
            $quantidade,
            $localizacao,
            'ENTRADA'
        );

        $pdo->commit();

        $this->view('mensagem', [
            'status' => 'sucesso',
            'titulo' => 'Recebimento salvo com sucesso!',
            'mensagem' => 'Estoque, endereço e histórico foram atualizados.',
            'resumo' => [
                'Produto' => $nomeFinal,
                'Código' => $codigo,
                'Quantidade recebida' => $quantidade,
                'Localização' => $localizacao,
            ],
            'botoes' => [
                ['rota' => 'movimentacoes/recebimento', 'texto' => 'Novo Recebimento'],
                ['rota' => 'movimentacoes/historico', 'texto' => 'Ver Movimentações', 'secundario' => true],
                ['rota' => 'menu', 'texto' => 'Voltar ao Menu', 'secundario' => true],
            ],
        ]);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log('Erro no recebimento FindWare: ' . $e->getMessage());

        $this->view('mensagem', [
            'status' => 'erro',
            'titulo' => 'Não foi possível salvar o recebimento',
            'mensagem' => $e instanceof RuntimeException
                ? $e->getMessage()
                : 'Ocorreu um erro ao salvar. Confira o log do PHP para identificar a causa.',
            'botoes' => [
                ['rota' => 'movimentacoes/recebimento', 'texto' => 'Tentar novamente'],
                ['rota' => 'menu', 'texto' => 'Voltar ao Menu', 'secundario' => true],
            ],
        ]);
    }
}


    public function saidaForm(): void
    {
        $produtoModel = new Produto();
        $codigo = $_GET['codigo'] ?? '';
        $produto = $codigo !== '' ? $produtoModel->buscarPorCodigo($codigo) : null;

        $this->view('movimentacoes/saida', compact('codigo', 'produto'));
    }

    public function salvarSaida(): void
    {
        $produtoModel = new Produto();
        $movimentacaoModel = new Movimentacao();

        $codigo = trim($_POST['codigo_barras'] ?? '');
        $produtoNome = trim($_POST['produto'] ?? '');
        $quantidade = intval($_POST['quantidade'] ?? 0);
        $motivo = trim($_POST['motivo'] ?? '');

        $resultado = $produtoModel->darSaida($codigo, $quantidade);

        if (!$resultado['ok']) {
            $this->view('mensagem', [
                'status' => 'erro',
                'titulo' => $resultado['motivo'] === 'estoque_insuficiente' ? 'Estoque insuficiente' : 'Produto não encontrado',
                'mensagem' => $resultado['erro'],
                'botoes' => [
                    ['rota' => 'movimentacoes/saida', 'texto' => 'Tentar Novamente'],
                    ['rota' => 'menu', 'texto' => 'Voltar ao Menu', 'secundario' => true],
                ],
            ]);
            return;
        }

        $estoqueAnterior = (int) $resultado['produto']['estoque'];
        $movimentacaoModel->registrar($codigo, $produtoNome, $quantidade, $motivo, 'SAIDA');

        $this->view('mensagem', [
            'status' => 'sucesso',
            'titulo' => 'Saída registrada com sucesso!',
            'mensagem' => 'A saída do produto foi registrada no sistema.',
            'resumo' => [
                'Produto' => $produtoNome,
                'Código' => $codigo,
                'Quantidade retirada' => $quantidade,
                'Motivo' => $motivo,
                'Estoque anterior' => $estoqueAnterior,
                'Estoque atual' => $estoqueAnterior - $quantidade,
            ],
            'botoes' => [
                ['rota' => 'movimentacoes/saida', 'texto' => 'Nova Saída'],
                ['rota' => 'movimentacoes/historico', 'texto' => 'Ver Movimentações', 'secundario' => true],
                ['rota' => 'menu', 'texto' => 'Voltar ao Menu', 'secundario' => true],
            ],
        ]);
    }

    public function historico(): void
    {
        $movimentacaoModel = new Movimentacao();
        $movimentacoes = $movimentacaoModel->todas();

        $this->view('movimentacoes/historico', compact('movimentacoes'));
    }
}
