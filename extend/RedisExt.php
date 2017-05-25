<?php
class RedisExt {
    protected static $instence;
    public $handler;
    public $conn;
    public $select;
    public $config = [
        'host'=>'192.168.2.227',
        'port'=>6379
    ];
    protected function __construct($option,$select)
    {
        if(empty($this->config)){
            $this->config = is_array($option)?$option:[];
        }
        $this->handler = new \Redis();
        $this->conn = $this->handler->connect($this->config['host'],$this->config['port']);
        $this->select = $this->handler->select(is_int($select)?$select:0);
    }
    public static function instence($options=[],int $select=0){
        if(is_null(self::$instence)){
            self::$instence = new static($options,$select);
        }
        return self::$instence;
    }

    protected function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function set($key,$value){
        return $this->handler->set($key,$value);
    }

    public function get($field){
        return $this->handler->get($field);
    }
    public function hGetAll($field){
        return $this->handler->hGetAll($field);
    }
}