<?php
declare (strict_types=1);

namespace app\common\lib;

/**
 * Notes：redis 链接池
 * {2023/11/10}
 * Class RedisConnectionPool
 * @package app\common\lib
 */
class RedisConnectionPool
{
    private $pool;
    private $maxConnections;
    private $host;
    private $port;
    private $password;
    private $database;

    public function __construct($config = [])
    {
        $host = $config['host'];
        $port = $config['$port'];
        $password = $config['password'];
        $maxConnections = isset($config['maxConnections']) ? $config['maxConnections'] : 10;
        $database = isset($config['database']) ? $config['database'] : 1;

        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->maxConnections = $maxConnections;
        $this->database = $database;
        $this->pool = new \SplQueue();
    }


    public function getConnection()
    {
        if (!$this->pool->isEmpty()) {
            return $this->pool->dequeue();
        }

        if ($this->countConnections() < $this->maxConnections) {

            try {
                $redis = new \Redis();
                $success = $redis->connect($this->host, $this->port);

                if ($success) {
                    if ($this->password) {
                        $redis->auth($this->password);
                    }
                    $redis->select($this->database);

                    return $redis;
                }
            } catch (\Exception $e) {
                throw new \Exception('Failed to connect to Redis server'.$e->getMessage());
            }
        }

        throw new \Exception('Connection pool limit reached');
    }

    public function releaseConnection($redis)
    {
        $this->pool->enqueue($redis);
    }

    public function countConnections()
    {
        return $this->pool->count();
    }

}