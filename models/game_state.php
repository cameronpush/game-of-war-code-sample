<?php

require_once(__DIR__ . '/deck.php');

/**
 * This class handles the current game state.  It maintains player decks and keeps track if someone has won.
 */

class GameState
{
	/**
	 * @var int These constants detail the current victory state.
	 */
	const VICTORY_CONDITION_PLAYING  = 0;
	const VICTORY_CONDITION_PLAYER_1 = 1;
	const VICTORY_CONDITION_PLAYER_2 = 2;
	const VICTORY_CONDITION_DRAW     = 3;

	/**
	 * @var int These constants are used throughout the system to keep track of the players' identities
	 */
	const PLAYER_1 = 1;
	const PLAYER_2 = 2;

	/**
	 * This array holds the players' decks
	 * @var array
	 * @example The decks array is in the following format:
	 * [
	 * 		PLAYER_1 => <Deck Object>,
	 * 		PLAYER_2 => <Deck Object>,
	 * ]
	 */
	private $_decks = [];

	/**
	 * This will hold one of the victory condition constants.  It is used to tell what the current victory status is.
	 * @var int
	 */
	private $_victory_status = self::VICTORY_CONDITION_PLAYING;

	/**
	 * The constructor will setup the player decks by creating a new deck of cards and then simulating dealing to the players.
	 */
	public function __construct()
	{
		// Shuffle the deck and deal to the players
		$deck = new Deck();
		$this->_decks[self::PLAYER_1] = new Deck([]);
		$this->_decks[self::PLAYER_2] = new Deck([]);

		while ($deck->getDeckSize()) {
			$this->_decks[self::PLAYER_1]->addCard($deck->drawCard());
			$this->_decks[self::PLAYER_2]->addCard($deck->drawCard());
		}
	}

	/**
	 * This will be used by the logger class to display the current state of the game in string form.
	 * @return string
	 */
	public function __toString()
	{
		$output_string = "\n";

		if ($this->_victory_status === self::VICTORY_CONDITION_PLAYING) {
			$player_one_size = count($this->_decks[self::PLAYER_1]);
			$player_two_size = count($this->_decks[self::PLAYER_2]);
			$output_string .= "\nThe game is still ongoing. Player 1 has $player_one_size cards.  Player 2 has $player_two_size cards.\n";

		} elseif ($this->_victory_status === self::VICTORY_CONDITION_DRAW) {
			$output_string .= "\nThe game has ended.  The game ended in a rare draw.\n";

		} else {
			$output_string .= "\nThe game has ended.  The game was won by Player " . $this->_victory_status . ".\n";
		}

		return $output_string;
	}

	/**
	 * This will set the Victory status.
	 * @param int $status This integer should be one of the VICTORY_CONDITION* constants
	 */
	public function setVictoryStatus($status)
	{
		$this->_victory_status = $status;
	}

	/**
	 * This will check the player decks to see if the game should continue or if a player is declared the winner.  It also checks for the rare condition of a draw.
	 * @return boolean True if the game should continue, false if it should cease.
	 */
	public function verifyPlayerDecks()
	{
		// If we have a draw declared stop playing
		if ($this->_victory_status === self::VICTORY_CONDITION_DRAW) {
			return false;
		}

		// If player 1 is out of cards, player 2 wins
		if ($this->_decks[self::PLAYER_1]->getDeckSize() === 0) {
			$this->_victory_status = self::VICTORY_CONDITION_PLAYER_2;
			return false;
		}

		// If player 2 is out of cards, player 1 wins
		if ($this->_decks[self::PLAYER_2]->getDeckSize() === 0) {
			$this->_victory_status = self::VICTORY_CONDITION_PLAYER_1;
			return false;
		}

		return true;
	}

	/**
	 * This will add a card to the specified player's deck
	 * @param int $player This is a player constant
	 * @param Card $card This is the card you would like added to the deck
	 * @throws Exception If an incorrect player int is passed in
	 */
	public function addPlayerCard($player, Card $card)
	{
		if ($player === self::PLAYER_1 || $player === self::PLAYER_2) {
			$this->_decks[$player]->addCard($card);

		} else {
			throw new Exception('There was an internal error.  Attempted to add a card to an invalid player.');
		}
	}

	/**
	 * This will draw a card from the specified players deck.
	 * @param  int $player This is a player constant
	 * @return Card
	 * @throws Exception If an incorrect player int is passed in
	 */
	public function drawPlayerCard($player)
	{
		if ($player === self::PLAYER_1 || $player === self::PLAYER_2) {
			return $this->_decks[$player]->drawCard();

		} else {
			throw new Exception('There was an internal error.  Attempted to draw a card from an invalid player.');
		}
	}

	/**
	 * This will return the count of the players deck size
	 * @param  int $player This is a player constant
	 * @return int
	 * @throws Exception If an incorrect player int is passed in
	 */
	public function getPlayerDeckSize($player)
	{
		if ($player === self::PLAYER_1 || $player === self::PLAYER_2) {
			return $this->_decks[$player]->getDeckSize();

		} else {
			throw new Exception('There was an internal error.  Attempted to draw a card from an invalid player.');
		}
	}
}

?>