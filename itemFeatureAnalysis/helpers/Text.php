<?php

/**
 * @copyright Luxembourg Institute of Science and Technology, 2015. All rights reserved. If you wish
 * to use this code for any purpose, please contact the author(s).
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
/**
 *
 * @author Olivier Pedretti
 *        
 */
namespace itis\itemFeatureAnalysis\helpers;

use itis\itemFeatureAnalysis\helpers\Combinations;
use itis\itemFeatureAnalysis\helpers\Text;

class Text {
	/**
	 * Punctuation and symbols
	 *
	 * @var string[]
	 */
	const PUNCTUATION_FILTER = [ 
			'.',
			'!',
			'?',
			',',
			':',
			';',
			'-',
			'[',
			']',
			'(',
			')',
			'{',
			'}',
			'"',
			// '\'', // don't remove Apostrophe
			'`',
			'…', // horizontal Ellipsis
			'⋮ ', // vertical Ellipsis
			'€',
			'$',
			'£',
			'%',
			'&',
			'#',
			'*',
			'+',
			'=',
			'<',
			'>',
			'@',
			'^',
			'_',
			'|',
			'~',
			'/',
			'\\' 
	];
	/**
	 * Regex
	 *
	 * @var string
	 */
	const SENTENCE_MULTIPLE_SPACE_PATTERN = "\s+";
	/**
	 *
	 * @param mixed $sentences
	 *        	string[] or string
	 * @return mixed string[] if input is string[], string otherwise
	 */
	public static function splitSentenceInWords($sentences) {
		$sentencesCleaned = self::cleanSentence($sentences);
		// split
		if (is_array ( $sentencesCleaned )) {
			$result = [ ];
			foreach ( $sentencesCleaned as $sentenceCleaned ) {
				$result [] = explode ( ' ', $sentenceCleaned );
			}
		} else {
			$result = explode ( ' ', $sentencesCleaned );
		}
		return $result;
	}
	/**
	 *
	 * @param mixed $sentences
	 *        	string[] or string
	 * @return mixed string[] if input is string[], string otherwise
	 */
	public static function cleanSentence($sentences){
		// replace punctuation
		$sentencesNoPunctuation = self::replacePunctuation ( $sentences );
		// replace multiple spaces
		$sentencesCleaned = self::removeMultipleSpaces ( $sentencesNoPunctuation );
		// remove end and last space
		$sentencesCleaned = self::trim($sentencesCleaned);
		return $sentencesCleaned;
	}
	/**
	 *
	 * @param mixed $sentences
	 *        	string[] or string
	 * @return mixed string[] if input is string[], string otherwise
	 */
	public static function trim($sentences) {
		if (is_array ( $sentences )) {
			$result = [ ];
			foreach ( $sentences as $sentence ) {
				$result [] = trim ( $sentence );
			}
		} else {
			$result = trim ( $sentences );
		}
		return $result;
	}
	/**
	 * build all word pairs between two sentences
	 *
	 * @param string[][] $sentencePairs        	
	 * @return string[][] word pairs
	 */
	public static function buildWordPairsFromSentencePairs($sentencePairs) {
		$wordPairs = [ ];
		foreach ( $sentencePairs as $sentencePair ) {
			$left = Text::splitSentenceInWords ( $sentencePair [0] );
			$right = Text::splitSentenceInWords ( $sentencePair [1] );
			$wordPairs = array_merge ( $wordPairs, Combinations::getAllPairsBetween2Arrays ( $left, $right ) );
		}
		return $wordPairs;
	}
	/**
	 *
	 * @param mixed $sentences
	 *        	string[] or string
	 * @return mixed string[] if input is string[], string otherwise
	 */
	public static function replacePunctuation($sentences, $replace = ' ') {
		$filtered = str_replace ( self::PUNCTUATION_FILTER, $replace, $sentences );
		return $filtered;
	}
	/**
	 *
	 * @param mixed $sentences
	 *        	string[] or string
	 * @return mixed string[] if input is string[], string otherwise
	 */
	public static function removeMultipleSpaces($sentences, $replace = ' ') {
		self::setEncoding ();
		if (is_array ( $sentences )) {
			$result = [ ];
			foreach ( $sentences as $sentence ) {
				$result [] = mb_ereg_replace ( self::SENTENCE_MULTIPLE_SPACE_PATTERN, $replace, $sentence );
			}
		} else {
			$result = mb_ereg_replace ( self::SENTENCE_MULTIPLE_SPACE_PATTERN, $replace, $sentences );
		}
		return $result;
	}
	public static function setEncoding() {
		setlocale ( LC_COLLATE, 'en_US.utf8' );
		mb_internal_encoding ( 'UTF-8' );
	}
}
