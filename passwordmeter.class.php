<?php
/*
 * PasswordMeterPHP
 * Copyright (C) 2008 Little Apps (http://www.little-apps.com)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// These scores are >= 0
define('PM_POS_NUM_CHARS', 'number_characters');
define('PM_POS_UC_LETTERS', 'uppercase_letters');
define('PM_POS_LC_LETTERS', 'lowercase_letters');
define('PM_POS_NUMBERS', 'numbers');
define('PM_POS_SYMBOLS', 'symbols');
define('PM_POS_MIDDLE_NUM_SYM', 'middle_numbers_symbols');
define('PM_POS_REQS', 'requirements');

// These scores are <= 0
define('PM_NEG_ONLY_LETTERS', 'letters_only');
define('PM_NEG_ONLY_NUMBERS', 'numbers_only');
define('PM_NEG_REPEAT_CHARS', 'repeat_characters');
define('PM_NEG_CONS_UC_LETTERS', 'consecutive_uppercase_letters');
define('PM_NEG_CONS_LC_LETTERS', 'consecutive_lowercase_letters');
define('PM_NEG_CONS_NUMBERS', 'consecutive_numbers');
define('PM_NEG_SEQ_LETTERS', 'sequential_letters');
define('PM_NEG_SEQ_NUMBERS', 'sequential_numbers');
define('PM_NEG_SEQ_SYMBOLS', 'sequential_symbols');


define('PM_RATING', 'rating');
define('PM_SCORE', 'score');

define('PM_RATING_PASS', 'pass');
define('PM_RATING_EXCEED', 'exceed');
define('PM_RATING_FAIL', 'fail');
define('PM_RATING_WARN', 'warn');
define('PM_RATING_UNKNOWN', 'unknown');

class PasswordMeter {
	private $sPassword = "";
	private $nLength = 0;
	
	private $nAlphaUC = 0; 
	private $nAlphaLC = 0; 
	private $nNumber = 0; 
	private $nSymbol = 0; 
	private $nMidChar = 0; 
	private $nRequirements = 0; 
	private $nAlphasOnly = 0; 
	private $nNumbersOnly = 0; 
	private $nUnqChar = 0; 
	private $nRepChar = 0; 
	private $nRepInc = 0; 
	private $nConsecAlphaUC = 0; 
	private $nConsecAlphaLC = 0; 
	private $nConsecNumber = 0; 
	private $nConsecSymbol = 0; 
	private $nConsecCharType = 0; 
	private $nSeqAlpha = 0; 
	private $nSeqNumber = 0; 
	private $nSeqSymbol = 0; 
	private $nSeqChar = 0; 
	private $nReqChar = 0; 
	private $nMultConsecCharType = 0;
	
	private $nMultRepChar = 1; 
	private $nMultConsecSymbol = 1;
	
	private $nMultMidChar = 2; 
	private $nMultRequirements = 2; 
	private $nMultConsecAlphaUC = 2; 
	private $nMultConsecAlphaLC = 2; 
	private $nMultConsecNumber = 2;
	
	private $nReqCharType = 3; 
	
	private $nMultAlphaUC = 3; 
	private $nMultAlphaLC = 3; 
	private $nMultSeqAlpha = 3; 
	private $nMultSeqNumber = 3; 
	private $nMultSeqSymbol = 3;
	private $nMultLength = 4; 
	private $nMultNumber = 4;
	private $nMultSymbol = 6;
	
	private $nTmpAlphaUC = ""; 
	private $nTmpAlphaLC = ""; 
	private $nTmpNumber = ""; 
	private $nTmpSymbol = "";
	
	private $sAlphas = "abcdefghijklmnopqrstuvwxyz";
	private $sNumerics = "01234567890";
	private $sSymbols = "[]!@#$%^&*()";
	private $sComplexity = "Too Short";
	private $nMinPwdLen = 8;

	private $valid_ratings = array(PM_RATING_PASS, PM_RATING_EXCEED, PM_RATING_FAIL, PM_RATING_WARN, PM_RATING_UNKNOWN);
	private $valid_pos_options = array(PM_POS_NUM_CHARS, PM_POS_UC_LETTERS, PM_POS_LC_LETTERS, PM_POS_NUMBERS, PM_POS_SYMBOLS, PM_POS_MIDDLE_NUM_SYM, PM_POS_REQS);
	private $valid_neg_options = array(PM_NEG_ONLY_LETTERS, PM_NEG_ONLY_NUMBERS, PM_NEG_REPEAT_CHARS, PM_NEG_CONS_UC_LETTERS, PM_NEG_CONS_LC_LETTERS, PM_NEG_CONS_NUMBERS, PM_NEG_SEQ_LETTERS, PM_NEG_SEQ_NUMBERS, PM_NEG_SEQ_SYMBOLS);
	
	private $options = array();
		
	private $total_score = 0;

	/**
	* Constructor for PasswordMeter class
	* @param string $password The password to check (this is passed to PasswordMeter::set_password)
	* 
	* @return
	*/
	function __construct($password) {
		$this->set_password($password);
	}

	/**
	* Sets the password to be checked
	* @param string $password The password to check
	* 
	* @access public
	* 
	* @return
	*/
	public function set_password($password) {
		if (empty($password))
			throw new Exception('Password cannot be empty');
		
		if (!is_string($password))
			throw new Exception('Password must be string');
		
		$this->reset_options();
		
		$this->sPassword = $password;
		
		$this->nLength = strlen($password);
		$this->total_score = $this->nLength * $this->nMultLength;
	}
	
	/**
	* Clears the password info as well as the scores
	* 
	* @access public
	* 
	* @return
	*/
	public function clear() {
		$this->reset_options();
		
		$this->sPassword = '';
		
		$this->nLength = 0;
		$this->total_score = 0;
	}
	
	/**
	* Resets scores to default values
	* 
	* @access private
	* 
	* @return
	*/
	private function reset_options() {
		$this->options = array();
		
		$names = array_merge($this->valid_pos_options, $this->valid_neg_options);
		$default = array(PM_RATING => PM_UNKNOWN, PM_SCORE => 0);
		
		foreach ($names as $name) {
			$this->options[$name] = $default;
		}
	}
	
	/**
	* Checks and scores password 
	* 
	* @access public
	* 
	* @return bool Returns true if password was checked, otherwise, returns false
	*/
	public function check() {
		if (empty($this->sPassword))
			return false;
		
		$sPwdNoSpaces = preg_replace('/\s+/', '', $this->sPassword);
		
		$arrPwd = str_split($sPwdNoSpaces);
	
		$arrPwdLen = count($arrPwd);
		
		$a = 0;
		
		/* Loop through password to check for Symbol, Numeric, Lowercase and Uppercase pattern matches */
		foreach ($arrPwd as $c) {
			if (preg_match('/[A-Z]/', $c)) {
				if ($this->nTmpAlphaUC !== "") { 
					if (($this->nTmpAlphaUC + 1) == $a) { 
						$this->nConsecAlphaUC++; 
						$this->nConsecCharType++; 
					} 
				}
				
				$this->nTmpAlphaUC = $a;
				$this->nAlphaUC++;
			} else if (preg_match('/[a-z]/', $c)) {
				if ($this->nTmpAlphaLC !== "") { 
					if (($this->nTmpAlphaLC + 1) == $a) { 
						$this->nConsecAlphaLC++; 
						$this->nConsecCharType++; 
					} 
				}
				$this->nTmpAlphaLC = $a;
				$this->nAlphaLC++;
			} else if (preg_match('/[0-9]/', $c)) {
				if ($a > 0 && $a < ($arrPwdLen - 1)) 
					$this->nMidChar++; 

				if ($this->nTmpNumber !== "") { 
					if (($this->nTmpNumber + 1) == $a) { 
						$this->nConsecNumber++; 
						$this->nConsecCharType++; 
					} 
				}
				
				$this->nTmpNumber = $a;
				$this->nNumber++;
			} else if (preg_match('/[^a-zA-Z0-9_]/', $c)) {
				if ($a > 0 && $a < ($arrPwdLen - 1)) 
					$this->nMidChar++;
				
				if ($this->nTmpSymbol !== "") { 
					if (($this->nTmpSymbol + 1) == $a) { 
						$this->nConsecSymbol++; 
						$this->nConsecCharType++; 
					} 
				}
				
				$this->nTmpSymbol = $a;
				$this->nSymbol++;
			}
			
			
			/* Internal loop through password to check for repeat characters */
			$bCharExists = false;
			
			for ($b=0; $b < $arrPwdLen; $b++) {
				if ($arrPwd[$a] == $arrPwd[$b] && $a != $b) { 
					/* repeat character exists */
					$bCharExists = true;
					
					/* 
					Calculate increment deduction based on proximity to identical characters
					Deduction is incremented each time a new match is discovered
					Deduction amount is based on total password length divided by the
					difference of distance between currently selected match
					*/
					$this->nRepInc += abs($arrPwdLen/($b-$a));
				}
			}
			
			if ($bCharExists) { 
				$this->nRepChar++; 
				$this->nUnqChar = $arrPwdLen-$this->nRepChar;
				$this->nRepInc = ( $this->nUnqChar ? ceil($this->nRepInc/$this->nUnqChar) : ceil($this->nRepInc) ); 
			}
			
			$a++;
		}
			
		/* Check for sequential alpha string patterns (forward and reverse) */
		for ($s=0; $s < (strlen($this->sAlphas) - 3); $s++) {
			$sFwd = substr($this->sAlphas, $s, intval($s+3));
			$sRev = strrev($sFwd);
			
			if (stripos($this->sPassword, $sFwd) !== false || stripos($this->sPassword, $sRev) !== false) {
				$this->nSeqAlpha++; 
				$this->nSeqChar++;
			}
			
		}
		
		/* Check for sequential numeric string patterns (forward and reverse) */
		for ($s=0; $s < (strlen($this->sNumerics) - 3); $s++) {
			$sFwd = substr($this->sNumerics, $s, intval($s+3));
			$sRev = strrev($sFwd);
			
			if (stripos($this->sPassword, $sFwd) !== false || stripos($this->sPassword, $sRev) !== false) {
				$this->nSeqNumber++; 
				$this->nSeqChar++;
			}
		}
		
		/* Check for sequential symbol string patterns (forward and reverse) */
		for ($s=0; $s < (strlen($this->sSymbols) - 3); $s++) {
			$sFwd = substr($this->sSymbols, $s, intval($s+3));
			$sRev = strrev($sFwd);
			
			if (stripos($this->sPassword, $sFwd) !== false || stripos($this->sPassword, $sRev) !== false) {
				$this->nSeqSymbol++; 
				$this->nSeqChar++;
			}
		}
		
		/* Modify overall score value based on usage vs requirements */

		/* General point assignment */
		if ($this->nAlphaUC > 0 && $this->nAlphaUC < $this->nLength) {	
			$this->set_score(PM_POS_UC_LETTERS, ($this->nLength - $this->nAlphaUC) * 2);
		}
		if ($this->nAlphaLC > 0 && $this->nAlphaLC < $this->nLength) {	
			$this->set_score(PM_POS_LC_LETTERS, ($this->nLength - $this->nAlphaLC) * 2);
		}
		if ($this->nNumber > 0 && $this->nNumber < $this->nLength) {
			$this->set_score(PM_POS_NUMBERS, $this->nNumber * $this->nMultNumber);
		}
		if ($this->nSymbol > 0) {
			$this->set_score(PM_POS_SYMBOLS, $this->nSymbol * $this->nMultSymbol);
		}
		if ($this->nMidChar > 0) {
			$this->set_score(PM_POS_MIDDLE_NUM_SYM, $this->nMidChar * $this->nMultMidChar);
		}
		
		/* Point deductions for poor practices */
		if (($this->nAlphaLC > 0 || $this->nAlphaUC > 0) && $this->nSymbol === 0 && $this->nNumber === 0) {  // Only Letters
			$this->nAlphasOnly = $this->nLength;
			$this->set_score(PM_NEG_ONLY_LETTERS, -$this->nLength);
		}
		if ($this->nAlphaLC === 0 && $this->nAlphaUC === 0 && $this->nSymbol === 0 && $this->nNumber > 0) {  // Only Numbers
			$this->nNumbersOnly = $this->nLength;
			$this->set_score(PM_NEG_ONLY_NUMBERS, -$this->nLength);
		}
		if ($this->nRepChar > 0) {  // Same character exists more than once
			$this->set_score(PM_NEG_REPEAT_CHARS, -$this->nRepInc);
		}
		if ($this->nConsecAlphaUC > 0) {  // Consecutive Uppercase Letters exist
			$this->set_score(PM_NEG_CONS_UC_LETTERS, -($this->nConsecAlphaUC * $this->nMultConsecAlphaUC));
		}
		if ($this->nConsecAlphaLC > 0) {  // Consecutive Lowercase Letters exist
			$this->set_score(PM_NEG_CONS_LC_LETTERS, -($this->nConsecAlphaLC * $this->nMultConsecAlphaLC));
		}
		if ($this->nConsecNumber > 0) {  // Consecutive Numbers exist
			$this->set_score(PM_NEG_CONS_NUMBERS, -($this->nConsecNumber * $this->nMultConsecNumber));
		}
		if ($this->nSeqAlpha > 0) {  // Sequential alpha strings exist (3 characters or more)
			$this->set_score(PM_NEG_SEQ_LETTERS, -($this->nSeqAlpha * $this->nMultSeqAlpha));
		}
		if ($this->nSeqNumber > 0) {  // Sequential numeric strings exist (3 characters or more)
			$this->set_score(PM_NEG_SEQ_NUMBERS, -($this->nSeqNumber * $this->nMultSeqNumber));
		}
		if ($this->nSeqSymbol > 0) {  // Sequential symbol strings exist (3 characters or more)
			$this->set_score(PM_NEG_SEQ_SYMBOLS, -($this->nSeqSymbol * $this->nMultSeqSymbol));
		}

		/* Determine if mandatory requirements have been met and set image indicators accordingly */
		$arrChars = 
			array(
				PM_POS_NUM_CHARS => $this->nLength,
				PM_POS_UC_LETTERS=> $this->nAlphaUC,
				PM_POS_LC_LETTERS => $this->nAlphaLC,
				PM_POS_NUMBERS => $this->nNumber,
				PM_POS_SYMBOLS => $this->nSymbol
			);
		
		foreach ($arrChars as $id => $val) {
			if ($id == PM_POS_NUM_CHARS)
				$minVal = intval($this->nMinPwdLen - 1); 
			else
				$minVal = 0;
			
			if ($val == $minVal + 1) { 
				$this->nReqChar++; 
				$this->set_rating($id, PM_RATING_PASS);
			} else if ($val > $minVal + 1) { 
				$this->nReqChar++; 
				$this->set_rating($id, PM_RATING_EXCEED);
			} else { 
				$this->set_rating($id, PM_RATING_FAIL);
			}
		}
		
		$this->nRequirements = $this->nReqChar;
		if (strlen($this->sPassword) >= $this->nMinPwdLen)
			$nMinReqChars = 3; 
		else
			$nMinReqChars = 4; 
		
		if ($this->nRequirements > $nMinReqChars) // One or more required characters exist
			$this->set_score(PM_POS_REQS, $this->nRequirements * 2);

		/* Determine if additional bonuses need to be applied and set image indicators accordingly */
		$arrChars = 
			array(
				PM_POS_MIDDLE_NUM_SYM => $this->nMidChar,
				PM_POS_REQS => $this->nRequirements
			);
			
		foreach ($arrChars as $id => $val) {
			if ($id == PM_POS_REQS)
				$minVal = $nMinReqChars; 
			else
				$minVal = 0; 
			
			if ($val == $minVal + 1)
				$this->set_rating($id, PM_RATING_PASS);
			else if ($val > $minVal + 1)
				$this->set_rating($id, PM_RATING_EXCEED);
			else
				$this->set_rating($id, PM_RATING_FAIL);
		}

		/* Determine if suggested requirements have been met and set image indicators accordingly */
		
		$arrChars = 
			array(
				PM_NEG_ONLY_LETTERS => $this->nAlphasOnly,
				PM_NEG_ONLY_NUMBERS => $this->nNumbersOnly,
				PM_NEG_REPEAT_CHARS => $this->nRepChar,
				PM_NEG_CONS_UC_LETTERS => $this->nConsecAlphaUC,
				PM_NEG_CONS_LC_LETTERS => $this->nConsecAlphaLC,
				PM_NEG_CONS_NUMBERS => $this->nConsecNumber,
				PM_NEG_SEQ_LETTERS => $this->nSeqAlpha,
				PM_NEG_SEQ_NUMBERS => $this->nSeqNumber,
				PM_NEG_SEQ_SYMBOLS => $this->nSeqSymbol
			);
			
		foreach ($arrChars as $id => $val) {
			if ($val > 0)
				$this->set_rating($id, PM_RATING_WARN);
			else
				$this->set_rating($id, PM_RATING_PASS);
		}
		
		return true;
	}
	
	/**
	* Determine complexity based on overall score
	* 
	* @access public
	* 
	* @return string Returns one of the following: Too Short, Very Weak, Weak, Good, Strong, Very Strong
	*/
	public function get_complexity() {
		$sComplexity = "Too Short";
		
		if ($this->total_score >= 0 && $this->total_score < 20) 
			$sComplexity = "Very Weak";
		else if ($this->total_score >= 20 && $this->total_score < 40) 
			$sComplexity = "Weak";
		else if ($this->total_score >= 40 && $this->total_score < 60) 
			$sComplexity = "Good";
		else if ($this->total_score >= 60 && $this->total_score < 80) 
			$sComplexity = "Strong";
		else if ($this->total_score >= 80 && $this->total_score <= 100) 
			$sComplexity = "Very Strong";
		
		return $sComplexity;
	}
	
	/**
	* Sets score for specified option and (if $add_total is set to true) adds it to total score
	* @param string $name Option name to have score set (one of PM_POS_* or PM_NEG_* defines)
	* @param int $score Score to be set (can be positive or negative)
	* @param bool $add_total If true, adds or substracts score from total score
	* 
	* @access private
	* 
	* @return bool True if score was set
	*/
	private function set_score($name, $score, $add_total = true) {
		if (!in_array($name, array_merge($this->valid_pos_options, $this->valid_neg_options)))
			throw new Exception('Option name is invalid');
		
		if (!is_numeric($score))
			throw new Exception('Score must be a number');
		
		if (!is_int($score))
			$score = intval($score);
			
		if (in_array($name, $this->valid_pos_options) && $score < 0)
			throw new Exception('Score cannot be negative for specified option');
		else if (in_array($name, $this->valid_neg_options) && $score > 0)
			throw new Exception('Score cannot be positive for specified option');
		
		$this->options[$name][PM_SCORE] = $score;
		
		if ($add_total)
			$this->total_score += $score;
		
		return true;
	}
	
	/**
	* Sets score for specified option
	* @param string $name Option name to have rating set (one of PM_POS_* or PM_NEG_* defines)
	* @param string $rating Rating to be set (one of PM_RATING_* defines)
	* 
	* @access private
	* 
	* @return bool True if rating was set
	*/
	private function set_rating($name, $rating) {
		if (!isset($this->options[$name]))
			return false;
		
		if (!in_array($rating, $this->valid_ratings))
			return false;
		
		$this->options[$name][PM_RATING] = $rating;
		
		return true;
	}
	
	/**
	* Gets the total score (between 0 and 100)
	* 
	* @access public
	* 
	* @return int Total score
	*/
	public function get_total_score() {
		if ($this->total_score > 100) 
			$this->total_score = 100; 
		else if ($this->total_score < 0) 
			$this->total_score = 0; 
		
		return $this->total_score;
	}
	
	/**
	* Gets score for option name
	* Score is greater than or equal to 0 if option name is one of PM_POS_* defines, or, lesser than or equal to 0 if option name is one of PM_NEG_* defines.
	* @param string $name Option name (one of PM_POS_* or PM_NEG_* defines)
	* 
	* @access public
	* 
	* @return int Score for specified option 
	*/
	public function get_score($name) {
		if (!isset($this->options[$name]))
			return false;
		
		return $this->options[$name][PM_SCORE];
	}
	
	/**
	* Gets rating for option name
	* @param string $name Option name (one of PM_POS_* or PM_NEG_* defines)
	* 
	* @access public
	* 
	* @return string Rating for specified option (one of PM_RATING_* defines)
	*/
	public function get_rating($name) {
		if (!isset($this->options[$name]))
			return false;
		
		return $this->options[$name][PM_RATING];
	}
	
	/**
	* Gets an array of all the scores
	* 
	* @access public
	* 
	* @return array Array of scores with the key being the option name (one of PM_POS_* or PM_NEG_* defines) and value being the score
	*/
	public function get_all_scores() {
		$scores = array();
		
		foreach (array_merge($this->valid_pos_options, $this->valid_neg_options) as $name) {
			$scores[$name] = $this->options[$name][PM_SCORE];
		}
		
		return $scores;
	}
	
	/**
	* Gets an array of positive scores
	* 
	* @access public
	* 
	* @return array Array of scores with the key being the option name (one of PM_POS_* defines) and value being the score (which is greater than or equal to zero)
	*/
	public function get_pos_scores() {
		$scores = array();
		
		foreach ($this->valid_pos_options as $name) {
			$scores[$name] = $this->options[$name][PM_SCORE];
		}
		
		return $scores;
	}
	
	/**
	* Gets an array of negative scores
	* 
	* @access public
	* 
	* @return array Array of scores with the key being the option name (one of PM_NEG_* defines) and value being the score (which is lesser than or equal to zero)
	*/
	public function get_neg_scores() {
		$scores = array();
		
		foreach ($this->valid_neg_options as $name) {
			$scores[$name] = $this->options[$name][PM_SCORE];
		}
		
		return $scores;
	}
	
	/**
	* Gets an array of all the ratings
	* 
	* @access public
	* 
	* @return array Array of ratings with the key being the option name (one of PM_POS_* or PM_NEG_* defines) and value being the rating (one of PM_RATING_* defines)
	*/
	public function get_all_ratings() {
		$scores = array();
		
		foreach (array_merge($this->valid_pos_options, $this->valid_neg_options) as $name) {
			$scores[$name] = $this->options[$name][PM_RATING];
		}
		
		return $scores;
	}
	
	/**
	* Gets an array of the positive ratings
	* 
	* @access public
	* 
	* @return array Array of ratings with the key being the option name (one of PM_POS_* defines) and value being the rating (one of PM_RATING_* defines)
	*/
	public function get_pos_ratings() {
		$scores = array();
		
		foreach ($this->valid_pos_options as $name) {
			$scores[$name] = $this->options[$name][PM_RATING];
		}
		
		return $scores;
	}
	
	/**
	* Gets an array of the negative ratings
	* 
	* @access public
	* 
	* @return array Array of ratings with the key being the option name (one of PM_NEG_* defines) and value being the rating (one of PM_RATING_* defines)
	*/
	public function get_neg_ratings() {
		$scores = array();
		
		foreach ($this->valid_neg_options as $name) {
			$scores[$name] = $this->options[$name][PM_RATING];
		}
		
		return $scores;
	}
}
