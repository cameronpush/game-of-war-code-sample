<?php

require_once(__DIR__ . '/../models/game_round.php');
require_once(__DIR__ . '/../models/game_state.php');
require_once(__DIR__ . '/../models/logger.php');

/**
 * This is the main functionality of the Game of War program.  All game logic is contained within this file.
 */

class Main
{
	/**
	 * This is the main game loop.  Creation of primary objects, looping through the game, and logging is handled here.
	 * @throws Exception If Errors are found within the methods.  Primarily this will be thrown if there is a write error for the logs
	 */
	public static function playGame()
	{
		// This is our output path for logging
		$output_path = __DIR__ . '/../game_output/game_of_war_results.txt';

		// Tell the user where to find the output
		echo "\nThe game output can be found here:\n$output_path";

		// Open a logger for use as we play our game
		$logger = new Logger($output_path);

		// Start a new game state.  This will shuffle the cards and deal them out to the players
		$game_state = new GameState();

		// Make sure that the players both have cards
		while ($game_state->verifyPlayerDecks()) {

			// Play a round
			$game_round = new GameRound($game_state);
			$game_round->playRound();

			// Log the round
			$logger->logRound($game_round);
		}

		// Log the final result
		$logger->logResults($game_state);
	}
}

?>