<?php
namespace app\common\controller;

use think\facade\Config;
use think\cache\driver\Redis as ThinkRedis;

/**
 * redis 连接池类
 */
class Redis extends ThinkRedis
{
    /**
    * @var int
    */
    protected $hash;

    /**
    * @var array
    */
    protected static $instance = [];

    /**
    * Redis constructor.
    * @param $db
    */
    private function __construct($db)
    {
        $options = Config::get('cache.stores.redis');
        $options['select'] = $db;
        $this->hash = $db;
        $this->options = array_merge($this->options, $options);
        parent::__construct();
    }

    private function __clone()
    {
    }

    /**
    * @param int $db
    * @return \Predis\Client|\Redis
    */
    public static function instance($db = 0)
    {
        if (! isset(self::$instance[$db])) {
            self::$instance[$db] = new self($db);
        }
        return self::$instance[$db];
    }

    public function __destruct()
    {
        self::$instance[$this->hash]->close();
        unset(self::$instance[$this->hash]);
    }

    /**
     * 使用方式 
     * use app\common\Redis;
     * $redis = Redis::instance(4);
     * $redis->hSet('user:1', 'userName', 'admin');
     * Redis::instance(1)->hSet('user', 'name', 'admin1');
     * Redis::instance(2)->hSet('user', 'name', 'admin2');
     * Redis::instance(3)->hSet('user', 'name', 'admin3');
     */

}