<?php
require_once __DIR__ . '/../Models/Produto.php';

class ScannerController extends Controller
{
    public function index(): void
    {
        $this->view('scanner/index');
    }

    public function entrada(): void
    {
        $codigo = $_GET['codigo'] ?? '';
        $this->view('scanner/resultado_entrada', compact('codigo'));
    }

    public function saida(): void
    {
        $produtoModel = new Produto();
        $codigo = $_GET['codigo'] ?? '';
        $produto = $codigo !== '' ? $produtoModel->buscarPorCodigo($codigo) : null;

        $this->view('scanner/resultado_saida', compact('codigo', 'produto'));
    }

    public function localizar(): void
    {
        $produtoModel = new Produto();
        $codigo = $_GET['codigo'] ?? '';
        $produto = $codigo !== '' ? $produtoModel->buscarPorCodigo($codigo) : null;

        $this->view('scanner/resultado_localizar', compact('codigo', 'produto'));
    }
}
