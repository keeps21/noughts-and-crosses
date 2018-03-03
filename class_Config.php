<?php

class Config {
    
    public $config;
    
    public function __construct() {

        // define db config
        $this->config = [
            'dbhost' => '192.168.10.10',
            'dbuser' => 'homestead',
            'dbpass' => 'secret',
            'dbname' => 'noughtsandcrosses'
        ];
    }
}