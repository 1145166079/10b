<?php
class BaseModel {

    private $db; //存储pdo数据库链接的对象
    private $prefix = ''; //表前缀
    private $table; //表名
    private $field = '*'; //要查询的字段
    private $limit = ''; //limit子语句
    private $order = ''; //排序
    private $where = ''; //条件
    private $group = ''; //条件
    private $join = ''; //条件

    public function __construct($table) {
        $dbData=Yaf_Application::app()->getConfig()->db;
        $dsn = "{$dbData->type}:host={$dbData->host};dbname={$dbData->dbname};charset={$dbData->charset}";
        $this->db = new \PDO($dsn, $dbData->username, $dbData->password);
        $this->table = $dbData->prefix . $table; //设置表名
    }

    public function select() {
        $sql = "select {$this->field} from {$this->table} {$this->join} {$this->where} {$this->group} {$this->order} {$this->limit}";
        $result = $this->db->query($sql);
        if (!$result) {
            $this->error();
        }
        $data = $result->fetchAll(\PDO::FETCH_ASSOC);
        $this->init_param();
        return $data;
    }
    
    public function insert($data) {
        $key = array_keys($data);
        $sql = "insert into {$this->table}(" . '`' . implode('`,`', $key) . '`' . ") value(" . "'" . implode("','", $data) . "'" . ")";
        $res = $this->db->exec($sql);
        if (!$res) {
            $this->error();
        }
        $this->init_param();
        return $this->db->lastInsertId();
    }

    public function insertAll($data) {
        $keys = array_keys(array_slice($data, 0, 1)[0]);
        $sql = "insert into {$this->table}(" . '`' . implode('`,`', $keys) . '`' . ") value";
        $sqlArray = [];
        foreach ($data as $val) {
            $sqlArray[] = $sql . "('" . implode("','", $val) . "')";
        }
        $sqls = implode(';', $sqlArray);
        $res = $this->db->exec($sqls);
        if (!$res) {
            $this->error();
        }
        $this->init_param();
        return $res;
    }

    public function getField($field = '*') {
        $sql = "select {$field} from {$this->table} {$this->join} {$this->where} {$this->group} {$this->order} limit 1";
        $res = $this->db->query($sql);
        if (!$res) {
            $this->error();
        }
        $data = $res->fetch(\PDO::FETCH_ASSOC);
        $this->init_param();
        return $data[$field];
    }
    public function find() {
        $sql = "select {$this->field} from {$this->table} {$this->join} {$this->where} {$this->group} {$this->order} limit 1";

        $res = $this->db->query($sql);
        if (!$res) {
            $this->error();
        }
        $data = $res->fetch(\PDO::FETCH_ASSOC);
        $this->init_param();
        return $data;
    }
    
    public function count() {
        $sql = "select count(*) as num from {$this->table} {$this->join} {$this->where} {$this->group} {$this->order} limit 1";

        $res = $this->db->query($sql);
        if (!$res) {
            $this->error();
        }
        $data = $res->fetch(\PDO::FETCH_ASSOC);
        $this->init_param();
        return $data['num'];
    }

    public function update($data) {
        $update_data = '';
        if (is_array($data)) {
            foreach ($data as $key => &$val) {
                $val = "`$key`='$val'";
            }
            $update_data = implode(',', $data);
        } else {
            $update_data = $data;
        }
        $sql = "update {$this->table} set $update_data {$this->where}";
        $res = $this->db->exec($sql);
        if ($res === false) {
            $this->error();
        }
        $this->init_param();
        return $res;
    }

    public function where($where) {
        if (is_array($where)) {
            foreach ($where as $key => &$value) {
                $value = "`$key`='$value'";
            }
            $this->where = 'where ' . implode(' and ', $where);
        } else {
            $this->where = 'where ' . $where;
        }
        return $this;
    }

    public function field($field = '*') {
        $this->field = $field;
        return $this;
    }

    public function limit($offset, $limit) {
        $this->limit = 'limit' . " {$offset},{$limit}";
        return $this;
    }

    public function order($order_str = '') {
        $this->order = 'order by ' . $order_str;
        return $this;
    }

    public function group($group = '') {
        $this->group = 'group by ' . $group;
        return $this;
    }

    public function join($type, $table, $where) {
        $this->join = "{$type} `{$table}` on {$where}";
        return $this;
    }
    //删除
    public function del() {
        $sql = "delete from {$this->table} {$this->where}";

        $res = $this->db->exec($sql);

        return $res;
    }


    /*
     * 重置参数
     */

    private function init_param() {
        $this->field = '*';
        $this->limit = '';
        $this->order = '';
        $this->where = '';
        $this->join = '';
        $this->group = '';
        return;
    }

    public function beginTransaction() {
        $this->db->beginTransaction();
    }

    public function commit() {
        $this->db->commit();
    }

    public function rollback() {
        $this->db->rollback();
    }

    private function error() {
        $errorInfo = $this->db->errorInfo();
        throw new \Exception($errorInfo[2], $errorInfo[1]);
    }

}
