
<?php

require_once __DIR__ . '/../Models/Produto.php';
require_once __DIR__ . '/../Models/EnderecoArmazenagem.php';

class MapaController extends Controller
{
    public function index(): void
    {
        $produtoModel = new Produto();
        $enderecoModel = new EnderecoArmazenagem();

        $busca = trim($_GET['busca'] ?? '');
        $produto = null;
        $erro = null;

        if ($busca !== '') {
            $produto = $produtoModel->buscarPorTermoOuCodigo($busca);

            if (!$produto) {
                $erro = 'Nenhum produto foi encontrado com a busca informada.';
            }
        }

        $enderecos = $enderecoModel->todos();

        $this->view('mapa/index', compact(
            'produto',
            'erro',
            'busca',
            'enderecos'
        ));
    }
}
