<?php

namespace eazy\http;

use Swoole\Coroutine;
use Swoole\Database\PDOConfig;
use Swoole\Database\PDOPool;

/**
 * @property \PDOStatement $statement
 * @property \PDO $pdo
 */
class Connection extends ContextComponent
{

    public $t;

    /**
     * @var PDOPool
     */
    private $connection;

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
            ->withHost('192.168.10.139')
            ->withPort(3306)
            // ->withUnixSocket('/tmp/mysql.sock')
            ->withDbName('mjb')
            ->withCharset('utf8mb4')
            ->withUsername('root')
            ->withPassword('root')
        , 1024);

        var_dump($this->connection);
    }

    public function createCommand(string $sql)
    {
        $this->setPdo($this->connection->get());
        $this->statement = $this->pdo->prepare($sql);
        if (!$this->statement->execute()) {
            // TODO throw exception.
            echo 'statement->execute() fail';
        }
        return $this;
//        $this->connection->put($pdo);
    }

    public function setPdo($val)
    {
        $this->setProperty('pdo', $val);
    }

    public function getPdo()
    {
        return $this->properties['pdo'];
    }

    public function setStatement($value)
    {
        $this->setProperty('statement', $value);
    }

    public function getStatement()
    {
        return $this->properties['statement'];
    }

    public function all()
    {
        return $this->statement->fetchAll();
    }

    public function one()
    {
//        Coroutine::defer(function () {
//            echo 'defer';
//        });
        $this->t ++;
        $r = $this->all()[0] ?? [];
        $this->connection->put($this->pdo);
        return $r;
    }
}