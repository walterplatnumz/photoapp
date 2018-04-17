<?php
    require_once(LIB_PATH.DS."dbdml.php");
    require_once(LIB_PATH.DS."configuration.php");
    require_once(LIB_PATH.DS."autoload.php");

    class DatabaseMANI {
        protected static $table_name = "";
        protected static $db_fields = array();

        function __construct() {
            
        }

        public static function getAll() {
            global $DBInstance;
            return static::getBySQL("SELECT * FROM ".static::$table_name);
        }

        public static function getByID($id = 0) {
            global $DBInstance;
            $result_array = static::getBySQL("SELECT * FROM ".static::$table_name." WHERE id =".$DBInstance->escapeValues($id)." LIMIT 1");
            if(!empty($result_array)) {
                return array_shift($result_array);
            }
            return false;
        }

        public static function getBySQL($sql = "") {
            global $DBInstance;
            $result_set = $DBInstance->querying($sql);
            $object_array = array();
            while($row = $DBInstance->fetchArray($result_set)) {
                $object_array[] = static::instantiate($row);
            }
            return $object_array;
        }

        public static function getCountAll() {
            global $DBInstance;
            $sql =  "SELECT COUNT(*) FROM ". static::$table_name;
            $result_set = $DBInstance->querying($sql);
            $row = $DBInstance->fetchArray($result_set);
            return array_shift($row);
        }

        private static function instantiate($record) {
            $class_name = get_called_class();
            $object = new $class_name;

            foreach($record as $attribute=>$value) {
                if($object->has_attribute($attribute)) {
                    $object->$attribute = $value;
                }
            }
            return $object;
        }

        private function has_attribute($attribute) {
            $object_vars = $this->attribute();
            return array_key_exists($attribute, $object_vars);
        }

        protected function attribute() {
            $object_property_attributes = array();
            if(!empty(static::$db_fields)){
                foreach(static::$db_fields as $field) {
                    if(property_exists($this, $field)) {
                        $object_property_attributes[$field] = $this->$field;
                    }
                }
            }
            return $object_property_attributes;
        }

        protected function sanitized_attributes() {
            global $DBInstance;

            $clean_escaped_attributes = array();
            foreach($this->attribute() as $key => $value) {
                $clean_escaped_attributes[$key] = $DBInstance->escapeValues($value);
            }
            return $clean_escaped_attributes;
        }

        public function save() {
            if(isset($this->id)) {
                $this->update();
            }else if(!isset($this->id)){
                $this->create();
            }
        }

        public function create() {
            global $DBInstance;
            $class_attributes = $this->sanitized_attributes();
            $sql = "INSERT INTO ".static::$table_name." (";
            $sql .= join(", ", array_keys($class_attributes));
            $sql .= ") VALUES ('";
            $sql .= join("', '", array_values($class_attributes));
            $sql .= "')";

            if($DBInstance->querying($sql)){
                $this->id = $DBInstance->insertID();
                return true;
            }else {
                return false;
            }
        }

        public function update() {
            global $DBInstance;

            $class_attributes = $this->sanitized_attributes();
            $attribute_pairs = array();

            foreach($class_attributes as $key => $value) {
                $attribute_pairs[] = "{$key}='{$value}'";
            }

            $sql = "UPDATE ".static::$table_name." SET ";
            $sql .= join(", ", $attribute_pairs);
            $sql .= " WHERE id=". $DBInstance->escapeValues($this->id);

            if($DBInstance->querying($sql)) {
                if($DBInstance->affectedRows() == 1) {
                    return true;
                }else {
                    return false;
                }
            }
        }

        public function delete() {
            global $DBInstance;

            $sql = "DELETE from ".static::$table_name." ";
            $sql .= "WHERE id=".$DBInstance->escapeValues($this->id);
            $sql .= " LIMIT 1";

            if($DBInstance->querying($sql)) {
                if($DBInstance->affected_rows() == 1) {
                    return true;
                }else {
                    return false;
                }
            }
        }
    }
?>