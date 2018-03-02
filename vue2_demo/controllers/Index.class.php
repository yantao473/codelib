<?php
class Index{
    private $db = null;

    public function __construct(){
        $this->db = new DB();
    }

    public function getData(){
        $sql = 'SELECT `id`, `categories`, `profits`, `optime` FROM `profits_stat` ORDER BY `categories`';
        $data =  $this->db->getAll($sql);
        return $data;
    }

    public function addData(){
        $params = array(
            'categories' => $_REQUEST['categories'],
            'profits' => $_REQUEST['profits'],
            'optime' => $_REQUEST['optime'],
        );
        $sql = 'INSERT INTO `profits_stat`(`categories`, `profits`, `optime`) VALUES(:categories, :profits, :optime)';
        $res = $this->db->writedb($sql, $params);
        return $this->mkresponse($res);
    }

    public function updateData(){
        $params = array(
            'id' => intval($_REQUEST['id']),
            'categories' => $_REQUEST['categories'],
            'profits' => $_REQUEST['profits'],
            'optime' => $_REQUEST['optime'],
        );
        $sql = 'UPDATE `profits_stat` SET `categories`=:categories, `profits`=:profits, `optime`=:optime WHERE `id`=:id';
        $res = $this->db->writedb($sql, $params);
        return $this->mkresponse($res);
    }

    public function deleteData(){
        $params = array(
            'id' => intval($_REQUEST['id'])
        );
        $sql = 'DELETE FROM `profits_stat` WHERE `id`=:id';
        $res = $this->db->writedb($sql, $params);
        return $this->mkresponse($res);
    }

    public function getCategories(){
        $sql = 'SELECT `id`, `name`, `notes` FROM `categories`';
        $data =  $this->db->getAll($sql);
        return $data;
    }

    public function addCategory(){
        $params = array(
            'name' => $_REQUEST['name'],
            'notes' => $_REQUEST['notes'],
        );
        $sql = 'INSERT INTO `categories`(`name`, `notes`) VALUES(:name, :notes)';
        $res = $this->db->writedb($sql, $params);
        return $this->mkresponse($res);
    }

    public function updateCategory(){
        $params = array(
            'id' => intval($_REQUEST['id']),
            'name' => $_REQUEST['name'],
            'notes' => $_REQUEST['notes'],
        );

        $sql = 'UPDATE `categories` SET name=:name, notes=:notes WHERE id=:id';
        $res = $db = $this->db->writedb($sql, $params);
        return $this->mkresponse($res);
    }

    public function deleteCategory(){
        $params = array(
            'id' => intval($_REQUEST['id'])
        );
        $sql = 'DELETE FROM `categories` WHERE `id`=:id';
        $res = $this->db->writedb($sql, $params);
        return $this->mkresponse($res);
    }

    private function mkresponse($res){
        if($res){
            return array('status' => 'OK');
        }else{
            return array('status' => 'Failed');
        }
    }
}
