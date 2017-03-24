<?php

/**
 * @copyright Luxembourg Institute of Science and Technology, 2016. All rights reserved. If you wish
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
 * @author Olivier Pedretti olivier.pedretti@list.lu
 */
namespace itis\itemFeatureAnalysis\models\metrics;

use itis\itemFeatureAnalysis\helpers\exceptions\PreConditionFailureException;
use itis\itemFeatureAnalysis\helpers\exceptions\UnexpectedServerResponseException;
use itis\itemFeatureAnalysis\helpers\Http;

abstract class TextDifficulty {
	const NUMBER_OF_SENTENCES = 'Number of sentences';
	const NUMBER_OF_WORDS = 'Number of words';
	const NUMBER_OF_SYLLABLES = 'Number of syllables';
	const NUMBER_OF_PROPOSITIONS = 'Number of propositions';
	const AVERAGE_SENTENCE_LENGTH = 'Average Sentence Length';
	const AVERAGE_WORD_LENGTH = 'Average Word Length';
	const AVERAGE_NUMBER_OF_SYLLABLES_PER_WORD = 'Average #Syllables per word';
	const AVERAGE_NUMBER_OF_PROPOSITIONS = 'Average #Propositions';
	const TYPE_TOKEN_RATIO = 'Type Token Ratio';
	const REFERENCES = 'References';
	const IDENTIFICATIONS = 'Identifications';
	const FLESCH_KINCAID = 'fleschKincaid';
	const DALE = 'dale';
	const DICTIONARY_COCA = 'DictionaryCoca';
	const AMSTAD = 'amstad';
	const FINAL_TEXT_DIFFICULTY = 'Final Text difficulty';
	private static $RESULT_TYPE__NUMBER = "(\d+(?:.\d+)?)";
	private static $RESULT_TYPE__LEVEL_WORDS = "(\w+(?: - \w+)?)";
	protected static $supportedLanguages = [ 
			'en',
			'de',
			'fr' 
	];
	public static function compute($text, $language) {
		$metrics = [ ];
		
		$lang = substr ( $language, 0, 2 );
		if (! in_array ( $lang, self::$supportedLanguages )) {
			throw new PreConditionFailureException ( __ ( 'Unsupported language for TextDifficulty: ' . $language ) );
		}
		$params = [ 
				'text' => $text,
				'language' => $lang 
		];
		$requestUrl = ITEMFEATUREANALYSIS_TEXTDIFFICULTY_WEBSERVICE_URL . '?' . http_build_query ( $params );
		$data = Http::sendRequest ( $requestUrl, [ 
				Http::OPTION_ACCEPT => 'text/html',
				Http::OPTION_TIMEOUT => ITEMFEATUREANALYSIS_TEXTDIFFICULTY_WEBSERVICE_TIMEOUT_IN_SECONDS 
		] );
		
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::NUMBER_OF_SENTENCES );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::NUMBER_OF_WORDS );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::NUMBER_OF_SYLLABLES );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::NUMBER_OF_PROPOSITIONS );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::AVERAGE_SENTENCE_LENGTH );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::AVERAGE_WORD_LENGTH );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::AVERAGE_NUMBER_OF_SYLLABLES_PER_WORD );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::AVERAGE_NUMBER_OF_PROPOSITIONS );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::TYPE_TOKEN_RATIO );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::REFERENCES );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__NUMBER, self::IDENTIFICATIONS );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__LEVEL_WORDS, self::FLESCH_KINCAID );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__LEVEL_WORDS, self::DALE );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__LEVEL_WORDS, self::DICTIONARY_COCA );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__LEVEL_WORDS, self::AMSTAD );
		self::setMetric ( $metrics, $data, self::$RESULT_TYPE__LEVEL_WORDS, self::FINAL_TEXT_DIFFICULTY );
		
		return $metrics;
	}
	protected static function setMetric(&$metrics, $metricsFromServer, $resultType, $metricName) {
		if (preg_match ( "/" . $metricName . ": " . $resultType . "/", $metricsFromServer, $matches ) !== 1) {
			throw new UnexpectedServerResponseException ( __ ( 'unexpected format in: ' ) . $data . __ ( ' for: ' ) . $metricName );
		}
		$metrics [$metricName] = $matches [1];
	}
}