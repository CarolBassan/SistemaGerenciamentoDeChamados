<?php

class Conexao {
    private $host = 'localhost';
    private $usuario = 'root';
    private $senha = '';
    private $banco = 'sistema_chamados';
    private $conn = null;

    public function connect() {
        if ($this->conn === null) {
            $this->conn = new mysqli($this->host, $this->usuario, $this->senha, $this->banco);
            
            if ($this->conn->connect_error) {
                die("Conexão falhou: " . $this->conn->connect_error);
            }
            return true;
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
        return $this->conn->query($sql);
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>