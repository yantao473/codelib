<?php

class DB
{
    private $dbh = null;
    private $stmt = null;

    public function __construct() {}

    public function getOne($sql, $data=[]){
        return $this->get($sql, $data);
    }

    public function getAll($sql, $data=[]){
        return $this->get($sql, $data, false);
    }

    public function writedb($sql, $data=[]){
        $this->dbh = $this->connect('w');
        try{
            return $this->pdoExec($sql, $data);
        }catch(Exception $e){
            echo "pdoExec exception: ". $e->getMessage();
        }finally{
            $this->free();
        }
    }

    private function connect($mode='r') {
        $dbconfig = array(
            'r' => array('dsn' =>'mysql:host='.SAE_MYSQL_HOST_S.':'.SAE_MYSQL_PORT.';dbname='.SAE_MYSQL_DB),
            'w' => array('dsn' =>'mysql:host='.SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT.';dbname='.SAE_MYSQL_DB)
        );
        $config = array_merge($dbconfig[$mode], array(
            'user' => SAE_MYSQL_USER,
            'password' => SAE_MYSQL_PASS
        ));

        //$dbconfig = array(
        //    'r' => array('dsn' =>'mysql:host=localhost;dbname=app_financing'),
        //    'w' => array('dsn' =>'mysql:host=localhost;dbname=app_financing')
        //);

        //$config = array_merge($dbconfig[$mode], array(
        //    'user' =>'profits',
        //    'password' =>'f47nosucgztl'
        //));

        try{
            return new PDO($config['dsn'], $config['user'], $config['password'], array( PDO::ATTR_PERSISTENT => true));
        }catch(PDOException $e){
            echo 'Connnection failed: '. $e->getMessage();
        }
    }

    private function get($sql, $data, $one=true){
        if (!is_array($data) || empty($sql) || !is_string($sql)){
            return false;
        }

        $this->dbh = $this->connect('r');
        try{
            $this->pdoExec($sql, $data);
            if($one){
                $data = $this->stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $data = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            return $data;
        }catch(Exception $e){
            echo "pdoExec exception: ". $e->getMessage();
        }finally{
            $this->free();
        }
    }

    private function pdoExec($sql, $data){
        try{
            $this->stmt = $this->dbh->prepare($sql);
        }catch(PDOException $e){
            echo 'pdo prepare exception: '. $e->getMessage();
        }

        if (false === $this->stmt){
            return false;
        }

        if (!empty($data)) {
            foreach($data as $k=>&$v){
                $this->stmt->bindParam($k, $v);
            }
        }

        $res = $this->stmt->execute();

        if (!$res) {
            throw new Exception('sql:'.$sql.'<====>where:'.json_encode($data).'<====>error:'.json_encode($this->stmt->errorInfo()));
        }else{
            return $res;
        }
    }

    private function free(){
        if($this->dbh){
            $this->dbh = null;
        }

        if($this->stmt){
            $this->stmt->closeCursor();
            $this->stmt = null;
        }
    }
}
