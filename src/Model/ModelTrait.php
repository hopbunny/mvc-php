<?php 

namespace Model;

use Database\Database;
use Exception;
use stdClass;

trait ModelTrait 
{

    protected stdClass $data;

    public function __construct(?stdClass $data = null) 
    {
        $this->data = $data ?? new stdClass;
    }

    protected static abstract function getTableName(): string;

    public function __get(string $param): mixed 
    {
        return $this->data->{$param};
    }

    public function __set(string $key, mixed $value): void 
    {
        $this->data->{$key} = $value;
    }

    public function verify(): void 
    {}

    public function create(): bool 
    {
        $this->verify();

        $columns = '`'.implode('`, `', array_keys((array)$this->data)).'`';
        $values = '\''.implode('\', \'', array_values((array)$this->data)).'\'';

        $tableName = self::getTableName();
        $query = "INSERT INTO `{$tableName}` ({$columns}) VALUES ($values)";

        $connection = Database::getConnection();
        $result = $connection->exec($query) !== false;
        if($result) {
            $this->data->id = $connection->lastInsertId();
        }

        return $result;
    }

    public function update(): bool 
    {
        $this->verify();
        
        $data = (array)$this->data;
        
        $id = $data['id'] ?? null;
        if(empty($id)) {
            throw new Exception('Received a invalid id');
        }

        unset($data['id']);

        $values = [];
        foreach($data as $column => $value) {
            $values[] = " `{$column}` = '{$value}'";
        }
        $values = implode(', ', $values);

        $tableName = self::getTableName();
        $query = "UPDATE `{$tableName}` SET {$values} WHERE `id` = '{$id}'";
        return Database::getConnection()->exec($query) !== false;
    }

    public function delete(int $limit = 1): bool 
    {
        $id = $this->data->id ?? null;
        if(empty($id)) {
            throw new Exception('Received a invalid id');
        }

        $tableName = self::getTableName();
        $query = "DELETE FROM `{$tableName}` WHERE `id` = '{$id}' LIMIT {$limit}";
        return Database::getConnection()->exec($query) !== false;
    }

    public static function where(string $column, mixed $value, bool $fetchAll = false, string $operator = '='): null|static|array 
    {
        $tableName = self::getTableName();
        $query = "SELECT * FROM `{$tableName}` WHERE `{$column}` {$operator} '{$value}'";

        $resultToModel = fn(stdClass $data) => new static($data);

        $result = Database::getConnection()->query($query);
        if($fetchAll) {
            $result = $result->fetchAll();
            $result = array_filter($result, fn(mixed $data) => $data instanceof stdClass);
            return array_map($resultToModel, $result);
        }

        $result = $result->fetch();
        return $result instanceof stdClass ? $resultToModel($result) : null;
    }

    public static function all(): array 
    {
        $tableName = self::getTableName();
        $query = "SELECT * FROM `{$tableName}`";
        $result = Database::getConnection()->query($query)->fetchAll();

        $result = array_filter($result, fn(mixed $data) => $data instanceof stdClass);
        return array_map(fn(stdClass $data) => new static($data), $result);
    }
}