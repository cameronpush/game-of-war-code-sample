<?php

require_once(__DIR__ . '/controllers/main.php');

/**
 * This file start the Game of War program.  It calls the Main controller class statically to kick everything off.
 */

try {

	echo "\nThe Game of War has begun.";

	Main::playGame();

} catch (Exception $e) {
	echo PHP_EOL . $e->getMessage() . PHP_EOL;

} finally {
	echo "\nThe Game of War has ended.\n";
}

?>