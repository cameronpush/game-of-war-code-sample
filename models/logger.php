<?php

require_once(__DIR__ . '/../models/game_round.php');
require_once(__DIR__ . '/../models/game_state.php');

/**
 * This class is responsible for logging all output for the program
 */

class Logger
{
	/**
	 * This is the file handle of the output path
	 * @var File
	 */
	private $_file = null;

	/**
	 * This should be constructed with a valid output path
	 * @param string $output_path
	 */
	public function __construct($output_path)
	{
		$this->_file = fopen($output_path, 'w+');
	}

	/**
	 * This closes our file handle on destruct
	 */
	public function __destruct()
	{
		fclose($this->_file);
	}

	/**
	 * This will take in a GameRound object and log the result of that particular round
	 * @param  GameRound $game_round
	 * @throws Exception If the output file can't be written to
	 */
	public function logRound(GameRound $game_round)
	{
		$success = fwrite($this->_file, $game_round);

		if (!$success) {
			throw new Exception('The output file could not be written to.  Please verify that you have write priveleges for the game_output folder.');
		}
	}

 	/**
 	 * This will log the results of a GameState
 	 * @param  GameState $game_state
	 * @throws Exception If the output file can't be written to
 	 */
	public function logResults(GameState $game_state)
	{
		$success = fwrite($this->_file, $game_state);

		if (!$success) {
			throw new Exception('The output file could not be written to.  Please verify that you have write priveleges for the game_output folder.');
		}
	}
}

?>