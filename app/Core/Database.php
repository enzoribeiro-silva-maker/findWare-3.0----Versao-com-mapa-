
<?php

/**
 * Conexão única (singleton) com o PostgreSQL do Supabase usando PDO.
 */
class Database
{
    private static ?PDO $instancia = null;

    public static function getConnection(): PDO
    {
        if (self::$instancia === null) {
            $config = require __DIR__ . '/../../config.php';

            $dsn = "pgsql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']}";

            try {
                self::$instancia = new PDO(
                    $dsn,
                    $config['db_user'],
                    $config['db_pass'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                error_log('Erro ao conectar ao banco: ' . $e->getMessage());
                die('Não foi possível conectar ao banco de dados. Confira a configuração.');
            }
        }

        return self::$instancia;
    }
}
