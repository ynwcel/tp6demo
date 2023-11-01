<?php
namespace app\common\lib;

class RedisPool{
    private static $connections = array(); //定义一个对象池
    private static $servers = array(); //定义redis配置文件

    public static function addServer($conf) //定义添加redis配置方法
    {
        foreach ($conf as $alias => $data){
            self::$servers[$alias]=$data;
        }
    }

    public static function getRedis($alias,$select = 0)//两个参数要连接的服务器KEY,要选择的库
    {
        if(!array_key_exists($alias,self::$connections)){  //判断连接池中是否存在
            $redis = new \Redis();
            $redis->connect(self::$servers[$alias][0],self::$servers[$alias][1]);
            self::$connections[$alias]=$redis;
            if(isset(self::$servers[$alias][2]) && self::$servers[$alias][2]!=""){
                self::$connections[$alias]->auth(self::$servers[$alias][2]);
            }
        }
        self::$connections[$alias]->select($select);
        return self::$connections[$alias];
    }


    //使用方式 连接redis
    function connect_to_redis()
    {
        global $CONFIG;
        //使用redis连接池
        $conf = array(
            'RA' => array($CONFIG['REDIS']['HOST'],$CONFIG['REDIS']['PORT'],$CONFIG['REDIS']['PASSWORD'])   //定义Redis配置
        );
        RedisPool::addServer($conf); //添加Redis配置
        $redis = RedisPool::getRedis('RA',1); //连接RA，使用默认0库
        return $redis;
    }


}