<?php

/**
 * PDO Database
 *
 **/

declare(strict_types=1);

namespace App\Libs;

use PDO;
use PDOException;

class Database
{
    private $_db_host;
    private $_db_user;
    private $_db_password;
    private $_database;
    private $_db_driver;

    private $_pdo;
    private $_stmt;

    public function __construct()
    {
        $this->_init();


        $options = array(
            PDO::ATTR_PERSISTENT => true, //persistent connection
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //enable error mode
        );

        try {
            $this->_pdo = new PDO(
                $this->_getDSN(),
                $this->_db_user,
                $this->_db_password,
                $options
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Loading db conf for example
     * **/
    private function _init(): void
    {
        $this->_db_host = getenv('MYSQL_SERVER');
        $this->_db_user = getenv('MYSQL_USER');
        $this->_db_password = getenv('MYSQL_PASSWORD');
        $this->_database = getenv('MYSQL_DATABASE');
        $this->_db_driver = 'mysql';
    }

    private function _getDSN(): string
    {
        return "{$this->_db_driver}:host={$this->_db_host};dbname={$this->_database}";
    }

    public function query(string $sql): self
    {
        $this->_stmt = $this->_pdo->prepare($sql);
        return $this;
    }

    public function bind($param, $value): self
    {
        $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;

        $this->_stmt->bindValue($param, $value, $type);
        return $this;
    }

    public function execute(): self
    {
        $this->_stmt->execute();
        return $this;
    }

    public function getAll()
    {
        return $this->_stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getRow()
    {
        return $this->_stmt->fetch(PDO::FETCH_OBJ);
    }
}
