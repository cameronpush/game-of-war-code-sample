<?php

/**
 * This class keep track of a single round of play.  It takes in a GameState object and simulates one round of play.
 */

class GameRound
{
	/**
	 * This is the current GameState as passed in by the controller
	 * @var GameState
	 */
	private $_game_state = null;

	/**
	 * This array holds each players' cards that were played in the given round
	 * @var array
	 * @example
	 * [
	 * 		PLAYER_1 => [<Card Object>, <Card Object>],
	 * 		PLAYER_2 => [<Card Object>, <Card Object>],
	 * ]
	 */
	private $_player_cards = [];

	/**
	 * This flag is set to true if a draw is ever reached.  (Very unlikely)
	 * @var boolean
	 */
	private $_draw_reached = false;

	/**
	 * This will hold an int showing which player won this round.  GameState player constants should be used.
	 * @var int
	 */
	private $_round_winner = null;

	/**
	 * This will create the new GameRound object using a passed in GameState object
	 * @param GameState $game_state
	 */
	public function __construct(GameState $game_state)
	{
		$this->_game_state = $game_state;
		$this->_player_cards[GameState::PLAYER_1] = [];
		$this->_player_cards[GameState::PLAYER_2] = [];
	}

	/**
	 * This will convert the current rounds action into a string for easy logging
	 * @return string
	 */
	public function __toString()
	{
		$output_string = "\n";
		$output_string .= "\nPlayer 1 played the following cards this round:" . implode(', ', $this->_player_cards[GameState::PLAYER_1]);
		$output_string .= "\nPlayer 2 played the following cards this round:" . implode(', ', $this->_player_cards[GameState::PLAYER_2]);

		if ($this->_draw_reached) {
			$output_string .= "\nA draw was declared this round";

		} else {
			$output_string .= PHP_EOL . 'Player ' . $this->_round_winner . ' won this round.';
		}

		$output_string .= PHP_EOL . 'Player 1 deck count:' . $this->_game_state->getPlayerDeckSize(1);
		$output_string .= PHP_EOL . 'Player 2 deck count:' . $this->_game_state->getPlayerDeckSize(2);

		return $output_string;
	}

	/**
	 * This will run through one round of action including game play and card cleanup.
	 */
	public function playRound()
	{
		// Draw one card from each player
		$this->_player_cards[GameState::PLAYER_1][] = $this->_game_state->drawPlayerCard(GameState::PLAYER_1);
		$this->_player_cards[GameState::PLAYER_2][] = $this->_game_state->drawPlayerCard(GameState::PLAYER_2);

		// Check if we have a round winner.
		$this->_checkRoundWinner();
	}

	/**
	 * This will check to see if there is a winner given the current cards each player has.  If there is not a winner (or a game draw) it will call _haveWar to decide a winner
	 */
	private function _checkRoundWinner()
	{
		$player_one_card = $this->_player_cards[GameState::PLAYER_1][count($this->_player_cards[GameState::PLAYER_1]) - 1];
		$player_two_card = $this->_player_cards[GameState::PLAYER_2][count($this->_player_cards[GameState::PLAYER_2]) - 1];

		if ($player_one_card->getValue() > $player_two_card->getValue()) {

			// Player 1 is the winner
			$this->_round_winner = GameState::PLAYER_1;
			$this->_cleanUpCards(GameState::PLAYER_1);

		} elseif ($player_one_card->getValue() < $player_two_card->getValue()) {

			// Player 2 is the winner
			$this->_round_winner = GameState::PLAYER_2;
			$this->_cleanUpCards(GameState::PLAYER_2);

		} elseif (!$this->_draw_reached) {

			// No winner and no draw.  Time for war
			$this->_haveWar();

		} else {

			// We hit the rare draw scenario
			$this->_game_state->setVictoryStatus(GameState::VICTORY_CONDITION_DRAW);

		}
	}

	/**
	 * This will simulate game event known as a war.
	 */
	private function _haveWar()
	{
		$player_one_size = $this->_game_state->getPlayerDeckSize(GameState::PLAYER_1);
		$player_two_size = $this->_game_state->getPlayerDeckSize(GameState::PLAYER_2);

		if ($player_one_size > 2 && $player_two_size > 2) {

			// Both players have at least 2 cards so we can have a regular war.  Draw two cards for each of them.
			for ($i = 0; $i < 2; $i++) {
				$this->_player_cards[GameState::PLAYER_1][] = $this->_game_state->drawPlayerCard(GameState::PLAYER_1);
				$this->_player_cards[GameState::PLAYER_2][] = $this->_game_state->drawPlayerCard(GameState::PLAYER_2);
			}

			$this->_checkRoundWinner();

		} elseif ($player_one_size < 1 || $player_two_size < 1) {

			// We have at least one player that has run out of cards
			if ($player_one_size === 0 && $player_two_size === 0) {

				// Both players are out of cards.  We have a draw
				$this->_draw_reached = true;

			} elseif ($player_one_size > 0) {

				//Player one has a card and therefore wins
				$this->_round_winner = GameState::PLAYER_1;
				$this->_cleanUpCards(GameState::PLAYER_1);

			} elseif ($player_two_size > 0) {

				//Player two has a card and therefore wins
				$this->_round_winner = GameState::PLAYER_2;
				$this->_cleanUpCards(GameState::PLAYER_2);
			}

		} else {

			// Both players have cards but not necessarily enough.
			// Process player one's cards
			if ($player_one_size > 1) {
				$this->_player_cards[GameState::PLAYER_1][] = $this->_game_state->drawPlayerCard(GameState::PLAYER_1);
			}

			$this->_player_cards[GameState::PLAYER_1][] = $this->_game_state->drawPlayerCard(GameState::PLAYER_1);

			// Process player two's cards
			if ($player_two_size > 1) {
				$this->_player_cards[GameState::PLAYER_2][] = $this->_game_state->drawPlayerCard(GameState::PLAYER_2);
			}

			$this->_player_cards[GameState::PLAYER_2][] = $this->_game_state->drawPlayerCard(GameState::PLAYER_2);

			$this->_checkRoundWinner();
		}
	}

	/**
	 * Once a victor has been declared this function should be called.  It will pick the cards up and shuffle them onto the bottom of the winners deck.
	 * @param  int $round_winner This is a player constant
	 */
	private function _cleanUpCards($round_winner)
	{
		$output_cards = [];

		// Loop through all the cards played and put them on the bottom of the winning player deck
		foreach ($this->_player_cards AS $player_hand) {
			$output_cards = array_merge($output_cards, $player_hand);
		}

		// We'll put them back on the deck in random order
		shuffle($output_cards);

		foreach ($output_cards AS $card) {
			$this->_game_state->addPlayerCard($round_winner, $card);
		}

	}
}

?>