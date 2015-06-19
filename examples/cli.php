<?php

require_once('../passwordmeter.class.php');

echo "Enter the password to check: ";
$handle = fopen ("php://stdin","r");
$pwd = trim(fgets($handle));
fclose($handle);

try {
	$pm = new PasswordMeter($pwd);

	$pm->check();
} catch (Exception $e) {
	die('The following exception occurred: ' . $e->getMessage());
}

$pos_options_text = array(
	PM_POS_NUM_CHARS => 'Number of characters',
	PM_POS_UC_LETTERS => 'Uppercase letters',
	PM_POS_LC_LETTERS => 'Lowercase letters',
	PM_POS_NUMBERS => 'Numbers',
	PM_POS_SYMBOLS => 'Symbols',
	PM_POS_MIDDLE_NUM_SYM => 'Middle numbers or symbols',
	PM_POS_REQS => 'Requirements'
);

$neg_options_text = array(
	PM_NEG_ONLY_LETTERS => 'Only letters',
	PM_NEG_ONLY_NUMBERS => 'Only numbers',
	PM_NEG_REPEAT_CHARS => 'Repeat characters',
	PM_NEG_CONS_UC_LETTERS => 'Consecutive uppercase letters',
	PM_NEG_CONS_LC_LETTERS => 'Consecutive lowercase letters',
	PM_NEG_CONS_NUMBERS => 'Consecutive numbers',
	PM_NEG_SEQ_LETTERS => 'Sequential letters',
	PM_NEG_SEQ_NUMBERS => 'Sequential numbers',
	PM_NEG_SEQ_SYMBOLS => 'Sequential symbols'
);

echo 'Total Score: ' . $pm->get_total_score() . ' / 100' . PHP_EOL;

echo 'Positive Scores:' . PHP_EOL . PHP_EOL;

$pos_scores = $pm->get_pos_scores();
$pos_ratings = $pm->get_pos_ratings();

foreach ($pos_options_text as $name => $readable_text) {
	$score = ( $pos_scores[$name] > 0 ? '+' : '' ) . $pos_scores[$name];
	$rating = ucfirst($pos_ratings[$name]);
	
	echo "\t" . $readable_text . ':' . PHP_EOL;
	echo "\t\t" . 'Score: ' . $score . PHP_EOL;
	echo "\t\t" . 'Rating: ' . $rating . PHP_EOL;
}

echo PHP_EOL;

$neg_scores = $pm->get_neg_scores();
$neg_ratings = $pm->get_neg_ratings();

echo 'Negative Scores:' . PHP_EOL . PHP_EOL;

foreach ($neg_options_text as $name => $readable_text) {
	$score = $neg_scores[$name];
	$rating = ucfirst($neg_ratings[$name]);
	
	echo "\t" . $readable_text . ':' . PHP_EOL;
	echo "\t\t" . 'Score: ' . $score . PHP_EOL;
	echo "\t\t" . 'Rating: ' . $rating . PHP_EOL;
}

echo PHP_EOL;