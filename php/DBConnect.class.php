<?php

class DBConnect
{
    private static $instance = null;
    private static $connections = [
        'read' => [],
        'write' => [],
    ];

    private static $dbconfig = [
        0000 => [
            'read' => [
                'host' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxx',
                'port' => 0000,
                'user' => 'xxxxxxxxxxxx',
                'passwd' => 'xxxxxxxxxxx',
                'db' => 'xxxxxxxxxxxx',
            ],
            'write' => [
                'host' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxx',
                'port' => 0000,
                'user' => 'xxxxxxxxxxxx',
                'passwd' => 'xxxxxxxxxxx',
                'db' => 'xxxxxxxxxxxx',
            ],
        ],
    ];

    private function __construct()
    {
    }

    // SELECT SHOW DESCRIBE EXPLAIN
    static function get($port, $sql)
    {
        $mode = 'read';
        $data = [];
        $mysqli = self::getConnect($port, $mode);
        try {
            $result = $mysqli->query($sql);

            if (is_object($result)) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $result->free();
            }

            return $data;
        } catch (Exception $e) {
            echo "SQL: $sql " . $e->getMessage();
        }
    }

    // SELECT SHOW DESCRIBE EXPLAIN
    static function getOne($port, $sql)
    {
        $data = self::get($port, $sql);

        return $data ? $data[0] : [];
    }

    // INSERT REPLACE UPDATE DELETE
    static function write($port, $sql)
    {
        $mode = 'write';
        $mysqli = self::getConnect($port, $mode);
        try {
            $result = $mysqli->query($sql);

            $affect_rows = 0;

            if ($result) {
                $affect_rows = $mysqli->affected_rows;
            }

            return $affect_rows;
        } catch (Exception $e) {
            echo "SQL: $sql " . $e->getMessage() . PHP_EOL;
        }
    }

    static function close($port, $mode = 'read')
    {
        $con = self::$connections[$mode][$port] ?? null;
        if ($con) {
            $con->close();
            self::$connections[$mode][$port] = null;
        }
    }

    /**
     * $whereArr = [['field'=> 'xxx', 'op' => '[>|<|=>]', 'value'=>'xxx', 'vtype'=>'STR|INT'] ....];
     */
    static function makeWhere($whereArr)
    {
        $wherreStr = '';
        $tmpArr = [];
        if (is_array($whereArr)) {
            foreach ($whereArr as $v) {
                if ($v['field'] && $v['op'] && $v['value'] && $v['vtype']) {
                    if ($v['vtype'] == 'STR') {
                        $tmp[] = $v['field'] . $v['op'] . '"' . $v['value'] . '"';
                    } else {
                        $tmp[] = $v['field'] . $v['op'] . $v['value'];
                    }
                }
            }
        }

        if ($tmpArr) {
            $wherreStr = implode(' AND ', $tmpArr);
        }

        return $wherreStr;
    }

    static function setDbName($port, $dbname)
    {
        if ($dbname) {
            if (isset(self::$dbconfig[$port])) {
                if (isset(self::$dbconfig[$port]['read'])) {
                    self::$dbconfig[$port]['read']['db'] = $dbname;
                }

                if (isset(self::$dbconfig[$port]['write'])) {
                    self::$dbconfig[$port]['write']['db'] = $dbname;
                }
            }
        }
    }

    static function getInstance()
    {
        if (is_null(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    private static function getConnect($port, $mode = 'read')
    {
        if (isset(self::$dbconfig[$port]) && isset(self::$dbconfig[$port][$mode])) {
            if (!isset(self::$dbconfig[$port][$mode]['last_init_time']) || (time() - self::$dbconfig[$port][$mode]['last_init_time']) > 30) {
                self::close($port, $mode); // 断开连接，避免连接数过多无法进行连接
                $c = self::$dbconfig[$port][$mode];
                $mysqli = new mysqli($c['host'], $c['user'], $c['passwd'], $c['db'], $c['port']) or die(mysqli_connect_error());
                $mysqli->set_charset('utf8');
                self::$dbconfig[$port][$mode]['last_init_time'] = time();
                self::$connections[$mode][$port] = $mysqli;
            }
        } else {
            throw new Exception("no config file for port $port");
        }

        return self::$connections[$mode][$port];
    }
}
