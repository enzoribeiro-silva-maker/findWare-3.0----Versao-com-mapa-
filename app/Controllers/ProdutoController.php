<?php
require_once __DIR__ . '/../Models/Produto.php';

class ProdutoController extends Controller
{
    public function cadastrarForm(): void
    {
        $this->view('produtos/cadastrar');
    }

    public function salvar(): void
    {
        $produtoModel = new Produto();

        $dados = [
            'nome' => trim($_POST['nome'] ?? ''),
            'codigo' => trim($_POST['codigo'] ?? ''),
            'codigo_barras' => trim($_POST['codigo_barras'] ?? ''),
            'corredor' => strtoupper(trim($_POST['corredor'] ?? '')),
            'prateleira' => trim($_POST['prateleira'] ?? ''),
            'nivel' => trim($_POST['nivel'] ?? ''),
            'estoque' => intval($_POST['estoque'] ?? 0),
        ];

        foreach (['nome', 'codigo', 'codigo_barras', 'corredor', 'prateleira', 'nivel'] as $campo) {
            if ($dados[$campo] === '') {
                $this->view('mensagem', [
                    'status' => 'erro',
                    'titulo' => 'Campos obrigatórios',
                    'mensagem' => 'Preencha todos os campos antes de cadastrar o produto.',
                    'botoes' => [
                        ['rota' => 'produtos/cadastrar', 'texto' => 'Voltar ao Cadastro'],
                        ['rota' => 'menu', 'texto' => 'Voltar ao Menu', 'secundario' => true],
                    ],
                ]);
                return;
            }
        }

        $produtoModel->criar($dados);

        $this->view('mensagem', [
            'status' => 'sucesso',
            'titulo' => 'Produto cadastrado com sucesso!',
            'mensagem' => "O produto {$dados['nome']} foi registrado no estoque do FindWare.",
            'resumo' => [
                'Produto' => $dados['nome'],
                'Código' => $dados['codigo'],
                'Estoque' => $dados['estoque'],
                'Localização' => $dados['corredor'] . '-' . $dados['prateleira'] . '-N' . $dados['nivel'],
            ],
            'botoes' => [
                ['rota' => 'produtos/cadastrar', 'texto' => 'Cadastrar Outro Produto'],
                ['rota' => 'menu', 'texto' => 'Voltar ao Menu', 'secundario' => true],
            ],
        ]);
    }

    public function buscarForm(): void
    {
        $this->view('produtos/buscar');
    }

    public function resultado(): void
    {
        $produtoModel = new Produto();
        $busca = trim($_GET['busca'] ?? '');
        $produtos = $busca !== '' ? $produtoModel->buscarPorTermo($busca) : [];

        $this->view('produtos/resultado', compact('busca', 'produtos'));
    }

    public function localizarForm(): void
    {
        $this->view('produtos/localizar');
    }

    public function perdidoForm(): void
    {
        $produtoModel = new Produto();
        $busca = trim($_GET['busca'] ?? '');
        $produto = $busca !== '' ? $produtoModel->buscarPorTermoOuCodigo($busca) : null;

        $this->view('produtos/perdido', compact('busca', 'produto'));
    }

    public function atualizarLocalizacao(): void
    {
        $produtoModel = new Produto();

        $codigo = trim($_POST['codigo_barras'] ?? '');
        $corredor = strtoupper(trim($_POST['corredor'] ?? ''));
        $prateleira = trim($_POST['prateleira'] ?? '');
        $nivel = trim($_POST['nivel'] ?? '');

        $produtoModel->atualizarLocalizacao($codigo, $corredor, $prateleira, $nivel);

        $this->view('mensagem', [
            'status' => 'sucesso',
            'titulo' => 'Localização atualizada com sucesso!',
            'mensagem' => 'O novo endereço do produto foi salvo no sistema.',
            'resumo' => [
                'Código' => $codigo,
                'Novo corredor' => $corredor,
                'Nova prateleira' => $prateleira,
                'Novo nível' => $nivel,
            ],
            'botoes' => [
                ['rota' => 'mapa?busca=' . urlencode($codigo), 'texto' => 'Ver no Mapa'],
                ['rota' => 'menu', 'texto' => 'Voltar ao Menu', 'secundario' => true],
            ],
        ]);
    }

    public function excluir(): void
    {
        $produtoModel = new Produto();
        $id = intval($_GET['id'] ?? 0);

        if ($id > 0) {
            $produtoModel->excluir($id);
        }

        $this->view('mensagem', [
            'status' => 'sucesso',
            'titulo' => 'Produto excluído com sucesso!',
            'mensagem' => 'O produto foi removido do cadastro do FindWare.',
            'botoes' => [
                ['rota' => 'dashboard', 'texto' => 'Voltar ao Dashboard'],
                ['rota' => 'menu', 'texto' => 'Voltar ao Menu', 'secundario' => true],
            ],
        ]);
    }

    
public function buscarPorCodigoJson(): void
{
    require_once __DIR__ . '/../Core/Database.php';

    $pdo = Database::getConnection();
    $codigo = trim($_GET['codigo'] ?? '');

    if ($codigo === '') {
        $this->json(['encontrado' => false]);
        return;
    }

    $sql = "
        SELECT
            p.id,
            p.nome,
            p.codigo,
            p.codigo_barras,
            p.estoque,
            p.corredor,
            p.prateleira,
            p.nivel,
            e.codigo_endereco
        FROM public.produtos p
        LEFT JOIN public.enderecos_armazenagem e
            ON e.produto_id = p.id
        WHERE p.codigo_barras = :codigo_barras
           OR p.codigo = :codigo
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'codigo_barras' => $codigo,
        'codigo' => $codigo,
    ]);

    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        $this->json([
            'encontrado' => true,
            'id' => (int) $produto['id'],
            'nome' => $produto['nome'],
            'codigo' => $produto['codigo'],
            'codigo_barras' => $produto['codigo_barras'],
            'estoque' => (int) $produto['estoque'],
            'corredor' => $produto['corredor'],
            'prateleira' => $produto['prateleira'],
            'nivel' => $produto['nivel'],
            'codigo_endereco' => $produto['codigo_endereco'],
        ]);
        return;
    }

    $this->json(['encontrado' => false]);
}

public function buscarPorRfidJson(): void
{
    header('Content-Type: application/json; charset=utf-8');

    $uid = strtoupper(trim($_GET['uid'] ?? ''));

    if ($uid === '') {
        http_response_code(400);
        echo json_encode([
            'encontrado' => false,
            'mensagem' => 'UID não informado.'
        ]);
        return;
    }

    try {
        $produtoModel = new Produto();
        $produto = $produtoModel->buscarPorRfidUid($uid);

        if (!$produto) {
            echo json_encode([
                'encontrado' => false,
                'uid' => $uid,
                'mensagem' => 'Tag RFID sem produto vinculado.'
            ]);
            return;
        }

        echo json_encode([
            'encontrado' => true,
            'produto' => $produto
        ]);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'encontrado' => false,
            'mensagem' => 'Erro ao consultar a tag RFID.'
        ]);
    }
}

public function vincularRfid(): void
{
    header('Content-Type: application/json; charset=utf-8');

    $uid = strtoupper(trim($_POST['uid'] ?? ''));
    $produtoId = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);

    if ($uid === '' || strlen($uid) > 32 || !$produtoId || $produtoId <= 0) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Informe um UID válido e o ID do produto.'
        ]);
        return;
    }

    try {
        $produtoModel = new Produto();

        // Confirma se o produto existe antes de vincular.
        $produto = $produtoModel->buscarPorId($produtoId);

        if (!$produto) {
            http_response_code(404);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Produto não encontrado.'
            ]);
            return;
        }

        $salvou = $produtoModel->vincularRfid($produtoId, $uid);

        if (!$salvou) {
            http_response_code(409);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível vincular a tag. Verifique se ela já está vinculada.'
            ]);
            return;
        }

        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Tag RFID vinculada ao produto com sucesso.'
        ]);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Erro ao vincular a tag RFID. Verifique se ela já está vinculada a outro produto.'
        ]);
    }
}

public function listarJson(): void
{
    header('Content-Type: application/json; charset=utf-8');

    try {
        $produtoModel = new Produto();
        $produtos = $produtoModel->todos();

        $resultado = array_map(function ($produto) {
            return [
                'id' => (int) $produto['id'],
                'nome' => $produto['nome'] ?? '',
                'codigo' => $produto['codigo'] ?? '',
                'codigo_barras' => $produto['codigo_barras'] ?? ''
            ];
        }, $produtos);

        echo json_encode(
            $resultado,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    } catch (Throwable $e) {
        http_response_code(500);

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Não foi possível carregar os produtos.'
        ], JSON_UNESCAPED_UNICODE);
    }
}



}
