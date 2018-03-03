<?php

// singleton database instance

class Database {
    
	private static $instance;

	private function __construct (string $host, string $user, string $pass, string $db)
	{
	    // create instance of class
		try {
			self::$instance = new PDO("mysql:host={$host};dbname={$db};", $user, $pass);
		} catch(PDOException $e) {
			throw new PDOException ($e->getMessage());
		}
	}

	private function __clone ()
	{
		throw new Exception('Cannot clone database');
	}

	private function __wakeup ()
	{
		throw new Exception('Cannot wake up database');
	}

	public static function make ($config)
	{
	    // our public 'constructor', returns an instance if it exists, otherwise creates a new instance
		if(self::$instance) {
			return self::$instance;
		}
        
		return $instance = new Database($config['dbhost'], $config['dbuser'], $config['dbpass'], $config['dbname']);
	}

	public function fetchAll(string $table)
	{
	    // fetch data
		$sql = "SELECT * FROM {$table}";

		$stmt = self::$instance->query($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row = $stmt->fetch()) {
			$data[] = $row;
		}
		
		return $data;
	}

	public function getCount(string $table, string $groupBy)
	{
	    // get count
		$sql = "SELECT {$groupBy}, count(*) FROM {$table} GROUP BY {$groupBy}";

		$stmt = self::$instance->query($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($row = $stmt->fetch()) {
			$data[] = $row;
		}
		
		return $data;
	}

	public function insert(string $table, array $data)
	{
 		// initialise variables
	    $fieldString = '';
 		$valueString = '';
 		
 		// build strings to use in query
        foreach($data as $field => $value) {
            $fieldString .= $field . ',';
            $valueString .= ':' . $field . ',';
        }
        
        // remove last comma
        $fieldString = rtrim($fieldString, ',');
        $valueString = rtrim($valueString, ',');
        
        // prepare query
        $stmt = self::$instance->prepare("INSERT INTO {$table} ({$fieldString}) VALUES ($valueString)");

        //bind query values
        foreach($data as $field => $value) {
            $stmt->bindValue(":{$field}", $value);
        }
            
        // execute query
        $stmt->execute();        
 			
	}

}
