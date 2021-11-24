<?php
namespace App\Service;
use MongoDB\Client as Client;
use Illuminate\Http\Request;

class ConnectionService{    
    
        protected $db;
        protected $connection;
    
        /**
         * constructor to create connection and Database .
         *
         * @return connection and database name
         */
    
        public function __construct() {
            $connection_string="mongodb://localhost:27017/?readPreference=primary&appname=MongoDB%20Compass&directConnection=true&ssl=false";
            $this->conneciton= new Client($connection_string);
            $this->db=$this->conneciton->socialapp;
        }
    
        /**
         * Geter for Database.
         *
         * @return connection
         */
    
    
        public function getConnection(){
            return $this->conneciton;
        }
    
    
        /**
         * Seter for Database.
         *
         * @return connection
         */
    
        public function getdb(){
            return $this->db;
        }
    
}