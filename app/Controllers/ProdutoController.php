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

}
