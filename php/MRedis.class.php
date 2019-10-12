<?php
class MRedis {

    private $redis;

    public function __construct($host, $port=6379, $passwd='') {
        $this->redis = new Redis();
        $this->redis->connect($host, $port);

        if($passwd){
            $this->redis->auth($passwd);
        }

        return $this->redis;
    }

    public function set($key, $value, $expire=0) {
        $retRes = $this->redis->set($key, $value);
        if ($expire > 0){
            $this->redis->expire('$key', $expire);
        }

        return $retRes;
    }

    public function sadd($key, $value){
        return $this->redis->sadd($key,$value);
    }

    public function zadd($key,$value){
        return $this->redis->zadd($key, $value);
    }

    public function smembers($setName){
        return $this->redis->smembers($setName);
    }

    public function lpush($key, $value){
        return $this->redis->lpush($key,$value);
    }

    public function lpop($key){
        return $this->redis->lpop($key);
    }

    public function rpush($key,$value){
        return $this->redis->rpush($key,$value);
    }

    public function rpop($key){
        return $this->redis->rpop($key);
    }

    public function lranges($key, $head, $tail){
        return $this->redis->lrange($key,$head,$tail);
    }

    public function hset($tableName, $field, $value){
        return $this->redis->hset($tableName, $field, $value);
    }

    public function hget($tableName, $field){
        return $this->redis->hget($tableName, $field);
    }

    public function sets($keyArray, $expire) {
        if(is_array($keyArray)) {
            $retRes = $this->redis->mset($keyArray);
            if($expire > 0) {
                foreach($keyArray as $key => $value) {
                    $this->redis->expire($key, $expire);
                }
            }

            return $retRes;
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    public function get($key) {
        $result = $this->redis->get($key);
        return $result;
    }

    /**
     * 同时获取多个值
     * @param ayyay $keyArray 获key数值
     */
    public function gets($keyArray) {
        if (is_array($keyArray)) {
            return $this->redis->mget($keyArray);
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    public function keyAll() {
        return $this->redis->keys('*');
    }

    public function del($key) {
        return $this->redis->delete($key);
    }

    public function dels($keyArray) {
        if(is_array($keyArray)) {
            return $this->redis->del($keyArray);
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    public function increment($key) {
        return $this->redis->incr($key);
    }

    public function decrement($key) {
        return $this->redis->decr($key);
    }

    public function isExists($key){
        return $this->redis->exists($key);
    }

    public function llen($key){
        return $this->redis->llen($key);
    }

    public function updateName($key, $newKey){
        return $this->redis->RENAMENX($key, $newKey);
    }

    public function dataType($key){
        return $this->redis->type($key);
    }

    public function flushAll() {
        return $this->redis->flushAll();
    }

    public function redisOtherMethods() {
        return $this->redis;
    }
}
