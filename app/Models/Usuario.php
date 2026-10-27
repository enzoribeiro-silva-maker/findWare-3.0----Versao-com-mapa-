<?php

/**
 * Model responsável pelos usuários do sistema (login/cadastro).
 * Atualizado na v1.1.0 com segurança reforçada e migração automática de hash.
 */
class Usuario
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Autentica o usuário e migra automaticamente senhas legadas em texto puro
     * para hash BCRYPT sem interromper a experiência do usuário.
     */
    public function encontrarPorEmailSenha(string $email, string $senha): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nome, email, senha FROM usuarios WHERE LOWER(email) = LOWER(:email) LIMIT 1');
        $stmt->execute(['email' => trim($email)]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            return null;
        }

        $senhaSalva = $usuario['senha'] ?? '';
        $autenticado = false;
        $precisaRehash = false;

        // 1. Verifica se é um hash válido (BCRYPT / ARGON2)
        if (str_starts_with($senhaSalva, '$2y$') || str_starts_with($senhaSalva, '$argon2')) {
            if (password_verify($senha, $senhaSalva)) {
                $autenticado = true;
                if (password_needs_rehash($senhaSalva, PASSWORD_DEFAULT)) {
                    $precisaRehash = true;
                }
            }
        } 
        // 2. Fallback seguro contra timing-attacks para senhas antigas em texto puro
        else if (hash_equals($senhaSalva, $senha)) {
            $autenticado = true;
            $precisaRehash = true; // Força a atualização imediata para hash criptografado
        }

        if (!$autenticado) {
            return null;
        }

        // 3. Atualiza a senha no BD de forma transparente para hash seguro se necessário
        if ($precisaRehash) {
            $novoHash = password_hash($senha, PASSWORD_DEFAULT);
            $updateStmt = $this->pdo->prepare('UPDATE usuarios SET senha = :senha WHERE id = :id');
            $updateStmt->execute(['senha' => $novoHash, 'id' => $usuario['id']]);
            $usuario['senha'] = $novoHash;
        }

        return $usuario;
    }

    /**
     * Verifica se o e-mail já existe na base de dados (Case-Insensitive).
     */
    public function emailExiste(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE LOWER(email) = LOWER(:email)');
        $stmt->execute(['email' => trim($email)]);
        return ((int) $stmt->fetchColumn()) > 0;
    }

    /**
     * Cadastra um novo usuário gerando hash criptográfico seguro da senha.
     */
    public function criar(string $nome, string $email, string $senha): bool
    {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)');
        return $stmt->execute([
            'nome' => trim($nome),
            'email' => strtolower(trim($email)),
            'senha' => $hash
        ]);
    }
}
