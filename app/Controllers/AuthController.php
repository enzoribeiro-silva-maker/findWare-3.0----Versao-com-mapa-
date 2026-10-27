<?php
require_once __DIR__ . '/../Models/Usuario.php';

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct(false); // Rotas públicas (login/cadastro)
    }

    public function loginForm(): void
    {
        if (usuarioLogado()) {
            $this->redirect('menu');
        }

        $this->view('auth/login', ['erro' => isset($_GET['erro'])], 'auth');
    }

    public function login(): void
    {
        $usuarioModel = new Usuario();

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === '') {
            $this->redirect('login?erro=1');
            return;
        }

        $usuario = $usuarioModel->encontrarPorEmailSenha($email, $senha);

        if (!$usuario) {
            $this->redirect('login?erro=1');
            return;
        }

        // Prevenção de Fixação de Sessão (Session Fixation)
        session_regenerate_id(true);
        $_SESSION['usuario'] = $usuario['nome'];
        $_SESSION['usuario_id'] = $usuario['id'];

        $this->redirect('menu');
    }

    public function cadastroForm(): void
    {
        $this->view('auth/cadastro', [], 'auth');
    }

    public function cadastrar(): void
    {
        $usuarioModel = new Usuario();

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($nome === '' || $email === '' || $senha === '') {
            $this->view('mensagem', [
                'status' => 'erro',
                'titulo' => 'Campos obrigatórios',
                'mensagem' => 'Preencha todos os campos para se cadastrar.',
                'botoes' => [['rota' => 'cadastro', 'texto' => 'Tentar Novamente']],
            ], 'auth');
            return;
        }

        // Validação estrita de formato de e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('mensagem', [
                'status' => 'erro',
                'titulo' => 'E-mail inválido',
                'mensagem' => 'Por favor, informe um endereço de e-mail válido.',
                'botoes' => [['rota' => 'cadastro', 'texto' => 'Corrigir E-mail']],
            ], 'auth');
            return;
        }

        if ($usuarioModel->emailExiste($email)) {
            $this->view('mensagem', [
                'status' => 'erro',
                'titulo' => 'E-mail já cadastrado',
                'mensagem' => 'Já existe uma conta cadastrada com esse e-mail.',
                'botoes' => [
                    ['rota' => 'login', 'texto' => 'Ir para Login'],
                    ['rota' => 'cadastro', 'texto' => 'Tentar Outro E-mail', 'secundario' => true],
                ],
            ], 'auth');
            return;
        }

        $usuarioModel->criar($nome, $email, $senha);

        $this->view('mensagem', [
            'status' => 'sucesso',
            'titulo' => 'Usuário cadastrado com sucesso!',
            'mensagem' => 'Agora você já pode acessar o sistema FindWare usando seu e-mail e senha.',
            'resumo' => ['Nome' => $nome, 'E-mail' => $email],
            'botoes' => [
                ['rota' => 'login', 'texto' => 'Ir para Login'],
                ['rota' => 'cadastro', 'texto' => 'Cadastrar Outro', 'secundario' => true],
            ],
        ], 'auth');
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        $this->redirect('login');
    }
}
