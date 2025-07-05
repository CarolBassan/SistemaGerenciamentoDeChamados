<?php

class Conexao {
    private $host = 'localhost';
    private $usuario = 'root';
    private $senha = '';
    private $banco = 'sistema_chamados';
    private $conn = null;
    private static $instance = null;
    private function __construct() {}
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->connect();
        }
        return self::$instance;
    }

    public function connect() {
        if ($this->conn === null) {
            try {
                $this->conn = new mysqli($this->host, $this->usuario, $this->senha, $this->banco);
                
                if ($this->conn->connect_error) {
                    throw new Exception("Conexão falhou: " . $this->conn->connect_error);
                }
                
                $this->conn->set_charset("utf8mb4");
                return true;
            } catch (Exception $e) {
                error_log($e->getMessage());
                throw $e;
            }
        }
        return false;
    }

    public function disconnect() {
        if ($this->conn !== null) {
            $this->conn->close();
            $this->conn = null;
            return true;
        }
        return false;
    }

    public function query($sql) {
        try {
            return $this->conn->query($sql);
        } catch (Exception $e) {
            error_log("Erro na query: " . $e->getMessage());
            throw $e;
        }
    }

    public function prepare($sql) {
        try {
            return $this->conn->prepare($sql);
        } catch (Exception $e) {
            error_log("Erro no prepare: " . $e->getMessage());
            throw $e;
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function beginTransaction() {
        return $this->conn->begin_transaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollback() {
        return $this->conn->rollback();
    }

    public function lastInsertId() {
        return $this->conn->insert_id;
    }
    
    // Previne clonagem da instância
    private function __clone() {}
    
    // Método mágico __wakeup() agora com visibilidade pública
    public function __wakeup() {
        // Reconecta se a conexão foi perdida durante a desserialização
        $this->connect();
    }
}
?>