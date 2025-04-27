<?php

class DataMapper {
    private $pdo;
    private $table;

    public function __construct($host, $dbname, $username, $password, $table) {
        $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->table = $table;
    }

    public function select($conditions = []) {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($conditions)) {
            $fields = array_keys($conditions);
            $where = implode(' AND ', array_map(fn($f) => "$f = :$f", $fields));
            $sql .= " WHERE $where";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($conditions);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $fields = array_keys($data);
        $columns = implode(',', $fields);
        $placeholders = implode(',', array_map(fn($f) => ":$f", $fields));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data, $idField = 'id') {
        $fields = array_keys($data);
        $set = implode(',', array_map(fn($f) => "$f = :$f", $fields));

        $sql = "UPDATE {$this->table} SET $set WHERE $idField = :id";
        $stmt = $this->pdo->prepare($sql);

        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id, $idField = 'id') {
        $sql = "DELETE FROM {$this->table} WHERE $idField = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}

// Exemplo de uso:
// $mapper = new DataMapper('localhost', 'meubanco', 'usuario', 'senha', 'usuarios');
// $usuarios = $mapper->select();
// $mapper->insert(['nome' => 'Denner', 'email' => 'Denner@example.com']);
// $mapper->update(1, ['nome' => 'Denner Atualizado']);
// $mapper->delete(1);
