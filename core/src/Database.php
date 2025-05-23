<?php

namespace Core;

class Database
{
    private $connection;
    public $get_error = false;
    public $query = '';
    public $table = '';
    public $type = '';
    private $without_quote = ['NULL','TIMESTAMP'];
    private $fields;
    function __construct($conn = false)
    {
        $db = config('database');
        $this->connection = $conn ? $conn : conn();
        $this->type = $db['driver'];

        if($this->type == 'mysqli')
        {
            $this->connection->set_charset('utf8mb4');
        }
    }

    function get_pk()
    {
        $query   = "SHOW KEYS FROM $this->table WHERE Key_name = 'PRIMARY'";
        $pk      = $this->connection->query($query);
        if($this->type == 'PDO')
            $pk      = $pk->fetchObject();
        else
            $pk      = $pk->fetch_object();
        return $pk->Column_name;
    }

    function insert($table, $val)
    {
        $this->table = $table;
        $this->query = "INSERT INTO $table";
        $fields = implode(',',array_keys($val));
        $vals = array_values($val);
        $conn = $this->connection;
        $vals = array_map(function($valss) use ($conn) {
            // $valss = isJson($valss) ? $valss : htmlspecialchars($valss);
            $valss = $conn->real_escape_string($valss);
            return $valss;
        }, $vals);
        $values = "'".implode("','",$vals)."'";
        $this->query .= "($fields)VALUES($values)";
        return $this->exec('insert');
    }

    function update($table, $val, $clause)
    {
        $this->table = $table;
        $values = $this->build_values($val);
        $string = $this->build_clause($clause);
        $this->query = "UPDATE $table SET $values WHERE $string AND last_insert_id(".$this->get_pk().")";
        return $this->exec('update');
    }

    function updateall($table, $val)
    {
        $this->table = $table;
        $values = $this->build_values($val);
        $this->query = "UPDATE $table SET $values";
        return $this->exec('updateall');
    }

    function all($table, $clause = [], $order = [], $limit = 0)
    {
        $this->table = $table;
        $conn = $this->connection;
        $this->query = "SELECT * FROM $table";
        $string = $this->build_clause($clause);
        $string_order = $this->build_order($order);
        if($string)
        {
            $having = strpos($string, "HAVING");
            if($having !== false && $having == 0)
            {
                $this->query .= ' '. $string;
            }
            else
            {
                $this->query .= ' WHERE '.$string;
            }
        }
        
        if($string_order)
            $this->query .= ' ORDER BY '.$string_order;

        if($limit)
            $this->query .= ' LIMIT '.$limit;
        return $this->exec('all');
    }

    function single($table, $clause = [], $order = [])
    {
        $this->table = $table;
        $conn = $this->connection;
        $this->query = "SELECT * FROM $table";
        $string = $this->build_clause($clause);
        $string_order = $this->build_order($order);
        if($string)
        {
            $having = strpos($string, "HAVING");
            if($having !== false && $having == 0)
            {
                $this->query .= ' '. $string;
            }
            else
            {
                $this->query .= ' WHERE '.$string;
            }
        }
        
        if($string_order)
            $this->query .= ' ORDER BY '.$string_order;
        return $this->exec('single');
    }

    function delete($table, $clause = false)
    {
        $this->table = $table;
        $conn = $this->connection;
        $this->query = "DELETE FROM $table";
        if($clause)
        {
            $string = $this->build_clause($clause);
            if($string)
            {
                $having = strpos($string, "HAVING");
                if($having !== false && $having == 0)
                {
                    $this->query .= ' '. $string;
                }
                else
                {
                    $this->query .= ' WHERE '.$string;
                }
            }
        }
        return $this->exec();
    }

    function exists($table, $clause = [], $order = [])
    {
        $this->table = $table;
        $conn = $this->connection;
        $this->query = "SELECT * FROM $table";
        $string = $this->build_clause($clause);
        $string_order = $this->build_order($order);
        if($string)
        {
            $having = strpos($string, "HAVING");
            if($having !== false && $having == 0)
            {
                $this->query .= ' '. $string;
            }
            else
            {
                $this->query .= ' WHERE '.$string;
            }
        }
        
        if($string_order)
            $this->query .= ' ORDER BY '.$string_order;
            
        return $this->exec('exists');
    }

    function truncate($table)
    {
        $this->table = $table;
        $conn = $this->connection;
        $this->query = "TRUNCATE TABLE $table";

        return $this->exec();
    }

    function exec($type = false, $params = [])
    {
        if($this->type == 'PDO')
        {
            if(in_array($type,['insert','update']))
            {
                $query_result = $this->connection->exec($this->query);
                // $last_id = $this->connection->insert_id;
                $last_id = $this->connection->lastInsertId();
                $pk = $this->get_pk();
                return $this->single($this->table,[$pk=>$last_id]);
            }
            else
            {
                $query_result = $this->connection->query($this->query);
                if($type == 'all')
                    return $query_result->fetchAll(PDO::FETCH_OBJ);
                if($type == 'single')
                    return $query_result->fetchObject();
                if($type == 'exists')
                    return count($query_result->fetchAll(PDO::FETCH_OBJ));
            }
        }
        else
        {
            if($type == 'multi_query')
            {
                $res = $this->connection->multi_query($this->query);
                while ($this->connection->next_result()) {;}
                return $res;
            }
            else
            {
                try {
                    //code...
                    if(count($params))
                    {
                        $stmt = $this->connection->prepare($this->query);
                        foreach($params as $param)
                        {
                            $stmt->bind_param("s", $param);
                        }

                        $stmt->execute();
                    }
                    else
                    {
                        $query_result = $this->connection->query($this->query);
                        if($query_result)
                        {
                            if($type == 'all')
                            {
                                while ($field = $query_result->fetch_field()) {
                                    $this->fields[] = $field->name;
                                }
                                return json_decode(json_encode($query_result->fetch_all(MYSQLI_ASSOC)));
                            }
                            if($type == 'single')
                                return $query_result->fetch_object();
                            if($type == 'exists')
                                return $query_result->num_rows;
                            if(in_array($type,['insert','update']))
                            {
                                $last_id = $this->connection->insert_id;
                                $pk = $this->get_pk();
                                return $this->single($this->table,[$pk=>$last_id]);
                            }
                        }
    
                        return $query_result;
                    }
                } catch (\mysqli_sql_exception $e) {
                    if($this->get_error) return $e->getMessage();
                    echo $e->getMessage();
                    return;
                }
            }
        }
    }

    function build_clause($clause)
    {
        if(is_string($clause))
        {
            $clause = trim($clause);
            $pos = strpos($clause, "WHERE");
            if ($pos !== false && $pos == 0) {
                return substr_replace($clause, "", $pos, strlen("WHERE"));
            }

            return $clause;
        }
        $logic = "AND";
        $count_clause = count($clause);
        $string = "";
        if($count_clause > 0)
        {
            foreach($clause as $key => $value)
            {
                $operator = "=";
                $val = $value;
                if(is_array($value))
                {
                    $operator = $value[0];
                    $val = $value[1];
                }
                if(!in_array(strtoupper($operator),['NOT IN','IN']))
                {
                    // $val = isJson($val) ? $val : htmlspecialchars($val);
                    $val = $this->connection->real_escape_string($val);
                }
                if(in_array($val,$this->without_quote) || in_array(strtoupper($operator),['NOT IN','IN']))
                $string .= "$key $operator $val";
                else
                $string .= "$key $operator '$val'";
                $last_iteration = !(--$count_clause);
                if(!$last_iteration)
                    $string .= ' '.$logic.' ';
            }
        }

        return $string;
    }

    function build_values($values)
    {
        $count_values = count($values);
        $string = "";
        if($count_values > 0)
        {
            foreach($values as $key => $value)
            {
                // $value = isJson($value) ? $value : htmlspecialchars($value);
                $value = $this->connection->real_escape_string($value);
                if(in_array($value,$this->without_quote))
                $string .= "$key=$value";
                else
                $string .= "$key='$value'";
                $last_iteration = !(--$count_values);
                if(!$last_iteration)
                    $string .= ', ';
            }
        }

        return $string;
    }

    function build_order($order)
    {
        $count_order = count($order);
        $string = "";
        if($count_order > 0)
        {
            foreach($order as $key => $value)
            {
                // $value = isJson($value) ? $value : htmlspecialchars($value);
                $value = $this->connection->real_escape_string($value);
                $string .= "$key $value";
                $last_iteration = !(--$count_order);
                if(!$last_iteration)
                    $string .= ', ';
            }
        }

        return $string;
    }

    public function getFields()
    {
        return $this->fields;
    }
}
