<?php

/**
 * This class handles all details about individual cards.  It keeps track of the suit, the card name (2, K, A, etc), and a value that can be used for comparing cards against each other.
 */
class Card
{
	/**
	 * @var string These constants can be used to keep track of the suit of a card
	 */
	const SUIT_SPADE   = '♠';
	const SUIT_HEART   = '♥';
	const SUIT_DIAMOND = '♦';
	const SUIT_CLUB    = '♣';

	/**
	 * This is an array that keeps track of the name of the card and the cooresponding sorting value
	 * @var array
	 */
	private static $_card_value_map = [
		'2'  => 2,
		'3'  => 3,
		'4'  => 4,
		'5'  => 5,
		'6'  => 6,
		'7'  => 7,
		'8'  => 8,
		'9'  => 9,
		'10' => 10,
		'J'  => 11,
		'Q'  => 12,
		'K'  => 13,
		'A'  => 14
	];

	/**
	 * The suit that this card belongs to.  Should use the suit constants to set
	 * @var string
	 */
	private $_suit  = null;

	/**
	 * This is the value for this card that can be used for sorting
	 * @var int
	 * @example 'A' would hold a 14 here
	 */
	private $_value = null;

	/**
	 * This is the name of the card
	 * @var string
	 */
	private $_name  = null;

	/**
	 * This constructor will take in a suite and a card name and generate a card object.
	 * @param string $suit Should use the suite constants
	 * @param string $name This is the name of the card
	 */
	public function __construct ($suit, $name)
	{
		// Validate the card name
		if (!isset(self::$_card_value_map[$name])) {
			throw new Exception('There was an internal error.  Attempted to create a card using an invalid name.');
		}

		// Set our cards data
		$this->_suit  = $suit;
		$this->_name  = $name;
		$this->_value = self::$_card_value_map[$name];

	}

	/**
	 * This allows for easily printing out the card into the logs
	 * @return string
	 * @example ♠A
	 */
	public function __toString()
	{
		return $this->_suit . $this->_name;
	}

	/**
	 * This will return the sortable value of this card
	 * @return int
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * This will return the card map.
	 * @return array
	 */
	public static function getCardValueMap()
	{
		return self::$_card_value_map;
	}
}

?>