<?php 

class Database {
    private $host;
    private $db_name;
    private $usuario;
    private $senha;
    private $conexao;

    public function __construct() {
        $this->host = getenv('MYSQL_ADDON_HOST') ?: 'localhost';
        $this->db_name = getenv('MYSQL_ADDON_DB') ?: 'login_iventario';
        $this->usuario = getenv('MYSQL_ADDON_USER') ?: 'root';
        $this->senha = getenv('MYSQL_ADDON_PASSWORD') ?: '';
    }
    public function getConnection() {
        $this->conexao = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";

            $this->conexao = new PDO($dsn, $this->usuario, $this->senha, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }

        return $this->conexao;
    }

}
