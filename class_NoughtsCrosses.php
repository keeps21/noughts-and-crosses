<?php

require_once('class_Config.php');
require_once('class_Database.php');


class NoughtsCrosses {

	private $winning_indexes;
	private $games;
	private $scores;
	private $db;

	public function __construct()
	{
	    
	    // config - for connecting to db
	    $config = new Config;
	    
		// db connection instance for logging to db
	    $this->db = Database::make($config->config);

		// define the indexes of all winning positions in the grid
		/* 
		 *  e.g.
		 *   0 | 1 | 2
		 *   3 | 4 | 5
		 *   6 | 7 | 8
		*/
	    
		$this->winning_indexes = [
			[0,1,2],
			[0,3,6],
			[0,4,8],
			[1,4,7],
			[2,5,8],
			[3,4,5],
			[6,7,8],
			[2,4,6]
		];

		// initialise scores variable
		$this->scores = [
			'x' => 0,
			'o' => 0, 
			'draw' => 0
		];

	}

	public function get_aggregate_results()
	{
	    // get count of scores from the db
		$count = $this->db->getCount('games', 'winner');

		// sort them as per the example (x, o , draw)
		rsort($count);
        
		// print all time scores
		foreach($count as $c) {
		    if($c['winner'] == 'draw') {
		        echo " Draws: " . $c['count(*)'] . "\n";

		    } else {
		        echo strtoupper($c['winner']) . " Wins: " . $c['count(*)'] . "\n";
		    }
		}
	    
	}

	public function get_results()
	{
        
	    // print scores of this round
	    foreach($this->scores as $key => $val) {
            if($key != 'draw') {
                echo strtoupper($key) . " Wins: " . $val;
            } else {
                echo " Draws: " . $val;	           
            }
            echo "\n";
	    
	    }
	    	
	}

	public function calculate_winners(string $stdin)
	{
		// break up the input into individual games
		$this->games = explode("\n\n\n", $stdin);

		foreach($this->games as $game) {
			
			// make the game input a string so we can split to an array of characters to test for a winning line
			$gameAsString = str_replace("\n", "", $game);
			
			// run the check to find a line
			$result = $this->find_line($gameAsString);
            
			
			// we have a result
			if($result != '') {

			    // increment the scores variable - saves querying the db to get the scores for this round
			    if($result != 'draw') {
					$this->scores[$result] += 1;
				} else {
					$this->scores['draw'] += 1;
				}
				
				// build data array for use in query 
				$data = [
				    'game' => $game,
				    'winner' => $result
				];
				
				// insert game and result to database
				$this->db->insert('games', $data);
				
			}
			
			
		}
		
	}

	public function find_line(string $gameAsString)
	{	
		// split to array, so we can check each index against the predefined winning 'routes / lines'
		$gameAsArray = str_split(strtolower($gameAsString));

		if(count($gameAsArray) == 9) { // the game was a entered properly

			// foreach possible set of winning indexes, check if all in those indexes match - e.g. xxx or ooo
			foreach($this->winning_indexes as $wi) {

				// if we have a line of x, or we have a line of o
				if( ($gameAsArray[$wi[0]] == 'x'  
						&& $gameAsArray[$wi[1]] == 'x' 
						&& $gameAsArray[$wi[2]] == 'x')
					|| 
					($gameAsArray[$wi[0]] == 'o' 
						&& $gameAsArray[$wi[1]] == 'o' 
						&& $gameAsArray[$wi[2]] == 'o')

					 ) {
						// we have a winner, return x or o (contained in variable)
						return $gameAsArray[$wi[0]]; 
				} 

			}
			return 'draw';

		}	 	

	}


}