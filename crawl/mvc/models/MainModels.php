<?php 
class MainModels extends Database{
    function select_array($data = '*',$where = NULL,$orderby = NULL,$start = NULL,$limit = NULL){
        $sql ="SELECT $data FROM $this->table";
        if (isset($where) && $where != NULL) {
            $fields = array_keys($where);
            $fields_list = implode("",$fields);
            $values = array_values($where);
            $isFields = true;
            $stringWhere = 'where';
            $string_character = '=';
            for ($i=0; $i < count($fields); $i++) { 
                preg_match('/<=|>=|!=|<|>/', $fields[$i], $matches, PREG_OFFSET_CAPTURE);
                if ($matches != NULL) {
                   $string_character = $matches[0][0];
                }
                if (!$isFields) {
                  $sql .= " and ";
                  $stringWhere = '';
                }
               $isFields = false;
               $sql .= "  ".$stringWhere." ".preg_replace('/<=|>=|!=|<|>/','',$fields[$i])." ".$string_character."  ? ";
            }
            if ($limit != NULL) {
                $sql .= " LIMIT ".$start." , ".$limit."";
            }
            if ($orderby !='' && $orderby != NULL) {
                $sql .= " ORDER BY ".$orderby."";
            }
            $query = $this->conn->prepare($sql);
            $query->execute($values);
        }
        else{
            if ($orderby !='' && $orderby != NULL) {
                $sql .= " ORDER BY ".$orderby."";
            }
            if ($limit != NULL) {
                $sql .= " LIMIT ".$start." , ".$limit."";
            }
            $query = $this->conn->prepare($sql);
            $query->execute();
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    function select_row($data='*',$where){
        $sql ="SELECT $data FROM $this->table ";
        if ($where != NULL) {
            $where_array = array_keys($where);
            $value_where = array_values($where);
            $isFields_where = true;
            $stringWhere = 'where';
            for ($i=0; $i < count($where_array); $i++) { 
                if (!$isFields_where) {
                    $sql .= " and ";
                    $stringWhere = '';
                }
                $isFields_where = false;
                $sql .= "" .$stringWhere." ".$where_array[$i]." = ?";
            }
            $query = $this->conn->prepare($sql);
            $query->execute($value_where);
            return $query->fetch(PDO::FETCH_ASSOC);
        }
    }
    // 
    function add($data = NULL){
        $fields = array_keys($data);
        $fields_list = implode(",",$fields);
        $values = array_values($data);
        $qr = str_repeat("?,",count($fields) - 1);
        $sql = "INSERT INTO `".$this->table."`(".$fields_list.") VALUES (${qr}?)";
        $query = $this->conn->prepare($sql);
        if ($query->execute($values)) {
            return array(
                'type'      => 'sucessFully',
                'Message'   => 'Insert data success',
                'id'        => $this->conn->lastInsertId()
            );
        }
        else{
            return array(
                'type'      => 'fails',
                'Message'   => 'Insert data fails',
            );
        }
    }
    function insertMultiple($data = NULL){
        if ($data != NULL)
        {
            $fields = array_keys($data[0]);
            $fields_list = implode(",",$fields);
            $qr = str_repeat("?,", count($fields) - 1);
            $sql = "INSERT INTO `".$this->table."` (".$fields_list.") VALUES";
            $values = [];
            foreach($data as $key => $val){
                $fields_for = array_keys($val);
                $fields_list_for = implode(",",$fields_for);
                $qr_for = str_repeat("?,", count($fields_for) - 1);
                if (count($data) - 1 > $key) {
                    $sql .= " (${qr_for}?),";
                }
                else
                {
                    $sql .= " (${qr_for}?) ";
                }
                $values = array_merge($values, array_values($val));
            }
    
            $query = $this->conn->prepare($sql);
            if ($query->execute($values)) {
                return 
                array(
                    'type'      => 'sucessFully',
                    'Message'   => 'Insert data success',
                );
            }
            else{
                return 
                array(
                    'type'      => 'fails',
                    'Message'   => 'Insert data fails',
                );
            }
        }
    }
    function delete($where = NULL){
        $sql = "DELETE FROM  $this->table ";
        if ($where != NULL) {
        $where_array = array_keys($where);
        $value_where = array_values($where);
        $isFields_where = true;
        $stringWhere = 'where';
        $string_Caculator = '=';
            for ($i=0; $i < count($where_array); $i++) { 
                preg_match('/<=|>=|<|>|!=/',$where_array[$i],$matches,PREG_OFFSET_CAPTURE);
                if ($matches != null) {
                    $string_Caculator = $matches[0][0];
                }
                if (!$isFields_where) {
                    $sql .= " and ";
                    $stringWhere = '';
                }
                $isFields_where = false;
                $sql .= "" .$stringWhere." ".preg_replace('/<=|>=|<|>|!=/','',$where_array[$i])." ".$string_Caculator." ?";
            }
            $query = $this->conn->prepare($sql);
            if ($query->execute($value_where)) {
               return array(
                    'type'      => 'sucessFully',
                    'Message'   => 'Delete data success',
                );
            }
            else{
                return array(
                    'type'      => 'fails',
                    'Message'   => 'Delete data fails',
                );
            }
        }
    }
}