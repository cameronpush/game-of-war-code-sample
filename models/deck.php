<?php

require_once(__DIR__ . '/card.php');

/**
 * This simulates a deck of cards.  This class maintains a list of all cards in the deck and provides a way to add or remove cards.  It can also shuffle up a new deck (on construct)
 */

class Deck
{

	/**
	 * This is an array of Card objects
	 * @var array
	 */
	private $_card_array = [];


	/**
	 * This will create a deck based on the cards passed in or shuffle a new deck if none are passed.
	 * @param array $card_array Array of Card objects
	 */
	public function __construct($card_array = null)
	{
		// If null we need to 'shuffle' a new deck.  Otherwise the cards passed in are our deck.
		if ($card_array === null) {
			$this->_dealNewDeck();

		} else {
			$this->_verifyDeck();
			$this->_card_array = $card_array;
		}
	}

	/**
	 * This will return a count of how many cards are in the deck
	 * @return int
	 */
	public function getDeckSize()
	{
		return count($this->_card_array);
	}

	/**
	 * This will add a card to the bottom of the current deck
	 * @param Card $card
	 */
	public function addCard(Card $card)
	{
		array_push($this->_card_array, $card);
	}

	/**
	 * This will draw a card off the top of the deck
	 * @return Card
	 * @throws Exception If the deck is empty
	 */
	public function drawCard()
	{
		if (empty($this->_card_array)) {
			throw new Exception('There was an internal error.  Attempted to draw a card from an empty deck.');

		} else {

			return array_shift($this->_card_array);
		}
	}

	/**
	 * This will shuffle up a new deck.
	 */
	private function _dealNewDeck()
	{
		// These are the suits we want in a fresh deck
		$suits = [
			Card::SUIT_SPADE,
			Card::SUIT_HEART,
			Card::SUIT_DIAMOND,
			Card::SUIT_CLUB
		];

		// Loop through all suit and card value combinations to build a new deck
		foreach ($suits AS $suit) {
			foreach (Card::getCardValueMap() AS $name => $value) {
				array_push($this->_card_array, new Card($suit, $name));
			}
		}

		shuffle($this->_card_array);
	}

	/**
	 * This will verify that all cards in the deck are valid Card objects
	 * @throws Exception If some items in the _card_array are invalid
	 */
	private function _verifyDeck()
	{
		// Make sure we have Card objects only
		foreach ($this->_card_array AS $card) {
			if (!$card instanceof Card) {
				throw new Exception('There was an internal error.  Attempted to create a deck without Card objects.');
			}
		}
	}
}

?>