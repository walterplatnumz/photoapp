<?php
    require_once(LIB_PATH.DS."configuration.php");

    class MySQLDatabase {
        private $connection;
        private $real_escape_string_exists;
        private $magic_quotes_active;

        function __construct() {
            $this->openDBConnection();
            $this->magic_quotes_active = get_magic_quotes_gpc();
            $this->real_escape_string_exists = function_exists("mysql_real_escape_string");
        }

        public function openDBConnection() {
            $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS);
            if($this->connection === null) {
                die('Database connection failed!.');
            }else {
                $selected_db = mysqli_select_db($this->connection, DB_NAME);
                if(!$selected_db) {
                    die('Failed to select the database!.' . mysqli_connect_errno());
                }else {
                }
            }
        }

        public function closeDBConnection() {
            if(isset($this->connection)) {
                mysqli_close($this->connection);
                unset($this->connection);
            }
        }

        public function querying($sql) {
            $result = mysqli_query($this->connection, $sql);
            if($result){
                $this->confirmQuery($result);
            }
            return $result;
        }

        public function confirmQuery($result_set) {
            if(!$result_set) {
                $message = "Database Query Failed";
                die($message);
            }
        }

        public function escapeValues($value) {
            if($this->real_escape_string_exists) {
                if($this->magic_quotes_active) {
                    $value = stripcslashes($value);
                }
                $value = mysqli_real_escape_string($this->connection, $value);
            }else {
                if($this->magic_quotes_active) {
                    $value = addslashes($value);
                }
            }
            return $value;
        }

        public function fetchArray($result_set) {
            return mysqli_fetch_array($result_set);
        }

        public function numRows($result_set) {
            return mysqli_num_rows($result_set);
        }

        public function insertID() {
            return mysqli_insert_id($this->connection);
        }

        public function affectedRows() {
            return mysqli_affected_rows($this->connection);
        }
    }
    
    $DBInstance = new MySQLDatabase();
?>