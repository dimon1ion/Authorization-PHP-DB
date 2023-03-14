<?php

    class DataBase {
        private $config;

        public function __construct($config)
        {
            $this->config = $config;
        }

        public function dbConnect() {
            $conn = mysqli_connect($this->config["host"], $this->config["username"], $this->config["password"], $this->config["dbname"]);

            if ($conn == false){
                return print("Произошла огибка при выполнеии запроса!".mysqli_connect_error());
            } else {
                return $conn;
            }
        }

        public function query($sql){
            return mysqli_query($this->dbConnect(), $sql);
        }

        public function errorMessage($result){
            if ($result === false) {
                return print("Произошла ошибка при выполнении запроса!" . mysqli_error($this->dbConnect($this->config)));
            }
        }
        public function close(){
            mysqli_close($this->dbConnect());
        }
    }
    

?>