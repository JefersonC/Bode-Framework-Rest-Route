<?php

namespace models;

class model {

    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $bd;
    private $db = 'root';
    private $sql;
    private $table;
    private $values = [];

    function __construct() {
        try {
            $this->bd = new \PDO('mysql:host=' . $this->host . ';charset=utf8', $this->user, $this->pass);
            $this->bd->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            debug($e);
        }
    }

    /**
     * Método utilizado para setar a tabela das consultas
     * @method setTable
     * @param string $table
     * @return this;
     * EXEMPLO
     * $this->setTable('tabela');
     * // resultaria em SELECT * FROM `tabela`, DELETE FROM `tabela`...
     */
    public function setTable($table, $database = null) {
        
        $db = $this->db;
        
        if($database){
            $db = $database;
        }
        
        $this->table = '`' . $db . '`.`' . $table . '`';
    }
    
    public function setDatabase($idCustomer){
        $this->db = 'BD_' . encodeByHash($idCustomer);
    }

    public function getTable() {
        return $this->table;
    }
    /**
     * Método para deletar registros.
     * @method delete
     * @pre setTable()
     * @pos where()
     * @param void
     * @return this
     */
    public function delete() {
        $this->sql = 'DELETE FROM ' . $this->table;
        return $this;
    }

    /**
     * Método para inserir registros na base.
     * Utiliza array onde o indice é a coluna é a coluna da tabela
     * e o valor é o valor a ser inserido.
     * @method insert
     * @pre setTable()
     * @param array $args
     * @return this
     * EXEMPLO
     * $i = array(
     *      'col1' => 'valor1',
     *      'col2' => 'valor2',
     *      'col3' => 'valor3'
     * );
     * $this->insert($i)->debug();
     * // resultaria em: INSERT INTO tabela(`col1`, `col2`, `col3`) VALUES('valor1', 'valor2', 'valor3');
     */
    public function insert($args = array()) {
        $values = array_values($args);
        $args = array_keys($args);
        $indices = '';
        $valores = '';
        for ($x = 0; $x < count($values); $x++) {
            $this->values[] = trim($values[$x]);
            $indices .= '´' . $args[$x] . '´, ';
            $valores .= '?, ';
        }

        $this->sql = 'INSERT INTO `' . $this->table . '` (' . substr($indices, -2) . ') VALUES(' . substr($valores, -2) . ')';

        return $this;
    }

    public function replace($args = array()) {
        $values = array_values($args);
        $args = array_keys($args);
        $indices = '';
        $valores = '';
        for ($x = 0; $x < count($values); $x++) {
            $this->values[] = trim($values[$x]);
            $indices .= '´' . $args[$x] . '´, ';
            $valores .= '?, ';
        }

        $this->sql = 'REPLACE INTO `' . $this->table . '` (' . substr($indices, -2) . ') VALUES(' . substr($valores, -2) . ')';

        return $this;
    }

    /**
     * @method select
     * @var $args recebe as colunas para trazer do banco
     * @pre setTable()
     * @param string $args
     * @return this
     * EXEMPLO
     * $this->select()->debug();
     * // resultaria em: SELECT * FROM `banco`.`tabela`
     *
     * $i = array(
     *      'col1',
     *      'col2',
     *      'col3'
     * );
     * $this->select($i)->debug();
     * // resultaria em: SELECT `col1`, `col2`, `col3` FROM `banco`.`tabela`
     */
    public function select($args = null) {

        if ($args == null) {
            $s = '*';
        } else {

            if (is_array($args)) {
                $s = '`' . implode('`, `', $args) . '`';
            } else {
                $s = $args;
            }
        }

        $this->sql = 'SELECT ' . $s . ' FROM ' . $this->table;

        return $this;
    }

    /**
     * Método para atualizar registros.
     * @method update
     * @pre setTable()
     * @pos where()
     * @param array @args
     * @return this
     * EXEMPLO
     * $u = array(
     *      'col1' => 'valor1',
     *      'col2' => 'valor2',
     *      'col3' => 'valor3'
     * );
     * $w = array(
     *      "id = '?'" => 1
     * );
     * $this->update($u)->where->($w)->debug();
     * // resultaria em: UPDATE `table` SET `col1` = 'valor1', `col2` = 'valor2', `col3` = 'valor3' WHERE id = '1'
     */
    public function update($args = array()) {
        
        foreach ($args as $key => $value) {
            $sql = '`' . $key . '` = ?, ';
            $this->values[] = $value;
        }
        
        $this->sql = 'UPDATE ' . $this->table . ' SET ' . substr($sql, 0, -2);;
        return $this;
    }

    /**
     * Método para consultar em duas ou mais tabelas.
     * @method join
     * @pre select()
     * @pos run()
     * @param string $table Tabela para buscar as informações do join 
     * @param string $cond Condição para efetuar o join
     * @return this
     */
    public function join($cond = array()) {
        foreach ($cond as $r) {
            $this->sql .= "LEFT JOIN `" . $r['table'] . "` ON " . $r['cond'] . " ";
        }
        return $this;
    }

    /**
     * Método auxiliar where.
     * @method where
     * @pre select(), update() or delete()
     * @pos orderby(), limit(), offset() or run()
     * @param array $args
     * @return this
     * EXEMPLO
     * $w = array(
     *      "id = '?'" => 1,
     *      "NOT id = '?'" => 2,
     *      "name LIKE '%?%'" => 'a'
     * );
     * $this->select()->where($w)->debug();
     * // SELECT * FROM tabela WHERE id = '1' AND NOT id = '10' AND name LIKE '%a%'
     */
    public function where($args = array(), $type = 'AND') {

        if ($args == NULL) {
            return $this;
        }

        $sql = '';
        
        foreach ($args as $key => $value) {
            if(strpos($key, '?') === false){
                throw new \Exception('Index ' . $key . ' is an invalid condition');
            }
            $sql .= $key . ' ' . $type . ' ';
            $this->values[] = $value;
        }
        $start = (strlen($type) + 2) * -1;
        $this->sql .= ' WHERE ' . substr($sql, 0, $start);

        return $this;
    }

    /**
     * Método auxiliar para unir registros.
     * @method groupby
     * @pre where()
     * @pos limit(), ofset() or run()
     * @param string $args
     * @return this
     */
    protected function groupby($args) {
        $this->sql .= "GROUP BY $args ";
        return $this;
    }

    /**
     * Método auxiliar para ordenar.
     * @method orderby
     * @pre where()
     * @pos limit(), ofset() or run()
     * @param string $args
     * @return this
     */
    protected function orderby($args) {
        $this->sql .= "ORDER BY $args ";
        return $this;
    }

    /**
     * Método auxiliar para limitar a consulta
     * @method limit
     * @pre select()
     * @param int $args Quantidade de Registros
     * @return $this
     */
    public function limit($args) {
        if (!empty($args)) {
            $this->sql .= "LIMIT $args ";
        }

        return $this;
    }

    /**
     * Método auxiliar para ignorar registros
     * @method offset
     * @pre limit()
     * @param int $args Quantidade de registros a serem ignorados
     * @return $this
     */
    protected function offset($args) {
        $this->sql .= "OFFSET $args ";
        return $this;
    }

    /**
     * Método auxiliar para printar a query em seu estado atual
     * @method debug
     * @param void
     * @return $sql
     */
    public function debug() {
//        echo $this->sql;
        
        $string = $this->sql;
        $data = $this->values;

        $indexed = $data == array_values($data);
        foreach ($data as $k => $v) {
            
            if(is_null($v)){
                $v = 'null';
            }else if(!is_numeric($v)){
                $v = "'$v'";
            }
            if ($indexed) {
                $string = preg_replace('/\?/', $v, $string, 1);
            } else {
                $string = str_replace(":$k", $v, $string);
            }
        }
        echo $string;
        
        exit;
    }

    public function r() {
        return $this->sql;
    }

    public function query($query) {
        $this->sql = $query;
        return $this;
    }

    /**
     * Método para executar a query no banco
     * @method run
     * @param string $type Define o tipo de fetch (nulo, FETCH_ALL para multiplas linhas ou FETCH para uma linha só)
     * @return $sql
     */
    public function run($type = null) {
        $qry = $this->bd->prepare($this->sql);
        $qry->execute($this->values);

        $rs = new \stdClass();

        switch ($type) {
            case "FETCH":
                $rs->data = $qry->fetch(\PDO::FETCH_OBJ);
                break;
            case "FETCH_ALL":
                $rs->data = $qry->fetchAll(\PDO::FETCH_OBJ);
                break;
            default:
                $rs->data = null;
                break;
        }
        $qry->closeCursor();

        $rs->rowCount = $qry->rowCount();
        $rs->lastId = $this->bd->lastInsertId();
        
        $this->values = null;
        $this->sql = '';
        
        return $rs;
    }

}
