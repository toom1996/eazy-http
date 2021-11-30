<?php

namespace eazy\http;

use Swoole\Database\PDOConfig;
use Swoole\Database\PDOPool;

class Connection extends ContextComponent
{

    public $connection;

    public $dsn;
    /**
     * @var string the username for establishing DB connection. Defaults to `null` meaning no username to use.
     */
    public $username;
    /**
     * @var string the password for establishing DB connection. Defaults to `null` meaning no password to use.
     */
    public $password;

    public function init()
    {
        $this->connection = new PDOPool((new PDOConfig())
            ->withHost('127.0.0.1')
            ->withPort(3306)
            // ->withUnixSocket('/tmp/mysql.sock')
            ->withDbName('mjb')
            ->withCharset('utf8mb4')
            ->withUsername('root')
            ->withPassword('root')
        );

        var_dump($this->connection);
    }

    public function createCommand()
    {
        $pdo = $this->connection->get();
//        $pdo->prepare('SELECT * FROM mjb_order_custom_xhs WHERE id = 50');
//        $result = $statement->execute();
        var_dump($result);
//        $this->connection->put($pdo);
    }
}