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
namespace itis\itemFeatureAnalysis\models\interactionAnalysers;

use itis\itemFeatureAnalysis\helpers\Combinations;
use itis\itemFeatureAnalysis\helpers\exceptions\PreConditionFailureException;
use itis\itemFeatureAnalysis\helpers\Helpers;
use itis\itemFeatureAnalysis\helpers\QtiItem;
use itis\itemFeatureAnalysis\helpers\Stats;
use itis\itemFeatureAnalysis\helpers\Text;
use itis\itemFeatureAnalysis\models\Koda;
use itis\itemFeatureAnalysis\models\metrics\Disco;
use itis\itemFeatureAnalysis\models\metrics\JaroWinkler;
use itis\itemFeatureAnalysis\models\metrics\SemSim;
use itis\itemFeatureAnalysis\models\metrics\TextDifficulty;
use itis\itemFeatureAnalysis\models\metrics\WordNet;
use itis\itemFeatureAnalysis\models\StopWords;

abstract class InteractionAnalyser {
	protected $interaction;
	protected $assessmentItem;
	/**
	 *
	 * @param string $lang
	 *        	ISO 639
	 * @param \common_report_Report $report        	
	 * @return [] metrics, associative array
	 */
	abstract public function analyse($lang, \common_report_Report $report);
	public function __construct(\DOMElement $interaction, \DOMElement $assessmentItem) {
		$this->isSupportedInteraction ( $interaction );
		$this->interaction = $interaction;
		$this->assessmentItem = $assessmentItem;
	}
	abstract public function getSupportedInteractionType();
	public function isSupportedInteraction(\DOMElement $interaction) {
		if (strcmp ( $interaction->nodeName, $this->getSupportedInteractionType () ) !== 0) {
			throw new PreConditionFailureException ( __ ( 'not a supported type, expected: ' . $this->getSupportedInteractionType () . __ ( ' found: ' ) . $interaction->nodeName ) );
		}
	}
	protected static function getJaroWinklerMetrics($pairs = [], &$metrics, \common_report_Report $report) {
		$distances = Helpers::callFuncOnArray ( $pairs, [ 
				JaroWinkler::class,
				'compute' 
		], [ ] );
		$metrics [ITEMFEATUREANALYSIS_DISTRACTORS_JAROWINKLER_AVERAGE] = Stats::average ( $distances );
		$metrics [ITEMFEATUREANALYSIS_DISTRACTORS_JAROWINKLER_STD_DEV] = Stats::standardDeviation ( $distances );
	}
	protected static function getDiscoMetrics($pairs = [], &$metrics, $lang, \common_report_Report $report) {
		$average = null;
		$stdDev = null;
		try {
			$distances = Helpers::callFuncOnArray ( $pairs, [ 
					Disco::class,
					'compute' 
			], [ 
					$lang 
			] );
			$average = Stats::average ( $distances );
			$stdDev = Stats::standardDeviation ( $distances );
		} catch ( \Exception $e ) {
			\common_Logger::w ( 'failed to analyse with Disco: ' . var_export ( $pairs, true ) . ' Exception: ' . Helpers::getFormatedException ( $e ) );
			$report->add ( new \common_report_Report ( \common_report_Report::TYPE_ERROR, __ ( 'Item Analysis Failed with Disco' ), Helpers::getFormatedException ( $e ) ) );
		}
		if (! is_null ( $average )) {
			$metrics [ITEMFEATUREANALYSIS_DISTRACTORS_DISCO_AVERAGE] = $average;
			$metrics [ITEMFEATUREANALYSIS_DISTRACTORS_DISCO_STD_DEV] = $stdDev;
		}
	}
	protected static function getSemSimMetrics($pairs = [], &$metrics, $lang, \common_report_Report $report) {
		$average = null;
		$stdDev = null;
		try {
			$distances = Helpers::callFuncOnArray ( $pairs, [ 
					SemSim::class,
					'compute' 
			], [ 
					$lang 
			] );
			$average = Stats::average ( $distances );
			$stdDev = Stats::standardDeviation ( $distances );
		} catch ( \Exception $e ) {
			\common_Logger::w ( 'failed to analyse with SemSim: ' . var_export ( $pairs, true ) . ' Exception: ' . Helpers::getFormatedException ( $e ) );
			$report->add ( new \common_report_Report ( \common_report_Report::TYPE_ERROR, __ ( 'Item Analysis Failed with SemSim' ), Helpers::getFormatedException ( $e ) ) );
		}
		if (! is_null ( $average )) {
			$metrics [ITEMFEATUREANALYSIS_DISTRACTORS_SEMSIM_AVERAGE] = $average;
			$metrics [ITEMFEATUREANALYSIS_DISTRACTORS_SEMSIM_STD_DEV] = $stdDev;
		}
	}
	protected static function getWordNetMetrics($pairs = [], &$metrics, $lang, \common_report_Report $report) {
		$average = null;
		$stdDev = null;
		try {
			$distances = Helpers::callFuncOnArray ( $pairs, [ 
					WordNet::class,
					'compute' 
			], [ 
					$lang 
			] );
			$average = Stats::average ( $distances );
			$stdDev = Stats::standardDeviation ( $distances );
		} catch ( \Exception $e ) {
			\common_Logger::w ( 'failed to analyse with WordNet: ' . var_export ( $pairs, true ) . ' Exception: ' . Helpers::getFormatedException ( $e ) );
			$report->add ( new \common_report_Report ( \common_report_Report::TYPE_ERROR, __ ( 'Item Analysis Failed with WordNet' ), Helpers::getFormatedException ( $e ) ) );
		}
		if (! is_null ( $average )) {
			$metrics [ITEMFEATUREANALYSIS_DISTRACTORS_WORDNET_AVERAGE] = $average;
			$metrics [ITEMFEATUREANALYSIS_DISTRACTORS_WORDNET_STD_DEV] = $stdDev;
		}
	}
	protected static function getTextDifficultyMetrics($text, &$metrics, $lang, \common_report_Report $report) {
		$textDifficultyMetrics = [ ];
		try {
			$textDifficultyMetrics = TextDifficulty::compute ( $text, $lang );
		} catch ( \Exception $e ) {
			\common_Logger::w ( 'failed to analyse with TextDifficulty: ' . $text . ' Exception: ' . Helpers::getFormatedException ( $e ) );
			$report->add ( new \common_report_Report ( \common_report_Report::TYPE_ERROR, __ ( 'Item Analysis Failed with TextDifficulty' ), Helpers::getFormatedException ( $e ) ) );
		}
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_NUMBER_OF_SENTENCES] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::NUMBER_OF_SENTENCES );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_NUMBER_OF_WORDS] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::NUMBER_OF_WORDS );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_NUMBER_OF_SYLLABLES] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::NUMBER_OF_SYLLABLES );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_NUMBER_OF_PROPOSITIONS] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::NUMBER_OF_PROPOSITIONS );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_AVERAGE_SENTENCE_LENGTH] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::AVERAGE_SENTENCE_LENGTH );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_AVERAGE_WORD_LENGTH] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::AVERAGE_WORD_LENGTH );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_AVERAGE_NUMBER_OF_SYLLABLES_PER_WORD] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::AVERAGE_NUMBER_OF_SYLLABLES_PER_WORD );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_AVERAGE_NUMBER_OF_PROPOSITIONS] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::AVERAGE_NUMBER_OF_PROPOSITIONS );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_TYPE_TOKEN_RATIO] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::TYPE_TOKEN_RATIO );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_REFERENCES] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::REFERENCES );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_IDENTIFICATIONS] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::IDENTIFICATIONS );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_FLESCH_KINCAID] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::FLESCH_KINCAID );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_DALE] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::DALE );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_DICTIONARY_COCA] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::DICTIONARY_COCA );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_AMSTAD] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::AMSTAD );
		$metrics [ITEMFEATUREANALYSIS_ITEM_TEXT_DIFFICULTY_FINAL_TEXT_DIFFICULTY] = Helpers::getValueIfIsset ( $textDifficultyMetrics, TextDifficulty::FINAL_TEXT_DIFFICULTY );
	}
	protected static function getKodaUris($text, $lang, \common_report_Report $report) {
		$pairs = [ ];
		try {
			$pairs = Koda::compute ( $text, $lang );
		} catch ( \Exception $e ) {
			\common_Logger::w ( 'failed to call Koda: ' . var_export ( $pairs, true ) . ' Exception: ' . Helpers::getFormatedException ( $e ) );
			$report->add ( new \common_report_Report ( \common_report_Report::TYPE_ERROR, __ ( 'Item Analysis Failed with Koda' ), Helpers::getFormatedException ( $e ) ) );
		}
		return $pairs;
	}
	protected static function serializeCorrectResponses($correctResponses) {
		$correctResponsesStrings = [ ];
		foreach ( $correctResponses as $correctResponseValues ) {
			$correctResponsesStrings [] = implode ( ' ', $correctResponseValues );
		}
		return implode ( ' ', $correctResponsesStrings );
	}
	/**
	 *
	 * @param string[] $concepts        	
	 * @param ['term'=>'uri'] $termUriPairs        	
	 * @return string[]
	 */
	protected static function getCommonConcept($concepts, $termUriPairs) {
		$uris = [ ];
		foreach ( $concepts as $concept ) {
			if (key_exists ( $concept, $termUriPairs )) {
				$uris [] = $termUriPairs [$concept];
			}
		}
		return $uris;
	}
	/**
	 *
	 * @param \DOMNode $node
	 *        	node to search in some text values
	 * @param string $computedValue
	 *        	the string in which to append values
	 * @param array $forbiddenNodes
	 *        	nodes that are not append
	 * @param string $separator
	 *        	separator of values
	 * @return string
	 */
	protected static function getNodesValue(\DOMNode $node, &$computedValue, $forbiddenNodes = [], $separator = ' ') {
		if (! is_string ( $computedValue )) {
			// create new string
			$computedValue = '';
		}
		// no separator for first add
		$theSeparator = (empty ( $computedValue ) ? '' : $separator);
		switch ($node->nodeType) {
			case XML_TEXT_NODE :
				$computedValue .= $theSeparator . $node->wholeText;
				break;
			case XML_CDATA_SECTION_NODE :
				$computedValue .= $theSeparator . $node->data;
				break;
			default :
				// add nothing
				break;
		}
		if ($node->hasChildNodes ()) {
			foreach ( $node->childNodes as $childNode ) {
				if (! in_array ( $childNode, $forbiddenNodes, true )) {
					self::getNodesValue ( $childNode, $computedValue, $forbiddenNodes, $separator );
				}
			}
		}
	}
	/**
	 *
	 * @param string $choiceType        	
	 * @return string
	 */
	protected function getItemBodyWithCorrectReponses($choiceType) {
		$distractorsNodes = QtiItem::getDistractorsNodes ( $this->interaction, $choiceType );
		
		$correctDistratorsNodes = QtiItem::getCorrectDistractorsNodes ( $this->assessmentItem, $this->interaction );
		// flattern array
		$correctDistratorsNodesFlat = [ ];
		foreach ( $correctDistratorsNodes as $correctDistratorNodes ) {
			foreach ( $correctDistratorNodes as $correctDistratorNode ) {
				$correctDistratorsNodesFlat [] = $correctDistratorNode;
			}
		}
		// make diff
		$incorrectDistratorNodes = [ ];
		foreach ( $distractorsNodes as $distractorsNode ) {
			if (! in_array ( $distractorsNode, $correctDistratorsNodesFlat, true )) {
				$incorrectDistratorNodes [] = $distractorsNode;
			}
		}
		
		$itemBodyNode = QtiItem::getItemBody ( $this->assessmentItem );
		
		self::getNodesValue ( $itemBodyNode, $computedValue, $incorrectDistratorNodes );
		
		return $computedValue;
	}
	protected static function buildCleanKodaResults($kodaPairs, $lang) {
		$result = [ ];
		foreach ( $kodaPairs as $concept => $uri ) {
			$wordsInConcept = Text::splitSentenceInWords ( $concept );
			$wordsInConceptWithoutStopWords = StopWords::filter ( $wordsInConcept, $lang );
			$conceptWithoutStopWords = implode ( ' ', $wordsInConceptWithoutStopWords );
			$result [$conceptWithoutStopWords] = $uri;
		}
		return $result;
	}
	/**
	 * Build an associative array where keys are distractors and values are array of corresponding uris
	 *
	 * @param string[] $concepts        	
	 * @param ['term'=>'uri'] $termUriPairs        	
	 * @return string[]
	 */
	protected static function buildDistratorsMap($concepts, $termUriPairs, $lang) {
		$map = [ ];
		$termUriPairsNoStopWords = null;
		foreach ( $concepts as $concept ) {
			$uris = [ ];
			// check if concept exists
			if (key_exists ( $concept, $termUriPairs )) {
				$uris [] = $termUriPairs [$concept];
			} else {
				// check without stop-words
				$wordsInConcept = Text::splitSentenceInWords ( $concept );
				$wordsInConceptWithoutStopWords = StopWords::filter ( $wordsInConcept, $lang );
				// rebuild concept without stop-words
				$conceptWithoutStopWords = implode ( ' ', $wordsInConceptWithoutStopWords );
				// check concept without stop-words
				if (key_exists ( $conceptWithoutStopWords, $termUriPairs )) {
					$uris [] = $termUriPairs [$conceptWithoutStopWords];
				} else {
					// search uri for one or more words in concept
					foreach ( $wordsInConceptWithoutStopWords as $wordInConceptWithoutStopWords ) {
						if (key_exists ( $wordInConceptWithoutStopWords, $termUriPairs )) {
							$uris [] = $termUriPairs [$wordInConceptWithoutStopWords];
						} else {
							// search if one or more word exists in koda results
							// split koda pairs in words
							if (! $termUriPairsNoStopWords) {
								$termUriPairsNoStopWords = self::buildCleanKodaResults ( $termUriPairs, $lang );
							}
							if (key_exists ( $wordInConceptWithoutStopWords, $termUriPairsNoStopWords )) {
								$uris [] = $termUriPairsNoStopWords [$wordInConceptWithoutStopWords];
							}
						}
					}
				}
			}
			$map [] = [ 
					'distractor' => $concept,
					'uris' => $uris 
			];
		}
		return $map;
	}
	/**
	 *
	 * @param
	 *        	[0=>'concept', 1=>'uri'] $kodaPairs
	 * @param string[] $distractorsCleaned        	
	 * @param \common_report_Repor $report        	
	 * @return [0=>'string, 1=>'string'] uri pairs
	 */
	protected function buildUriPairs($kodaPairs, $distractorsCleaned, $lang, \common_report_Report $report) {
		$uriPairs = [ ];
		
		$distractorsMap = self::buildDistratorsMap ( $distractorsCleaned, $kodaPairs, $lang );
		
		// check if all distractors have at least one uri
		$uriForEachDistractor = true;
		$distractorsUris = array_column ( $distractorsMap, 'uris' );
		foreach ( $distractorsUris as $distractorUris ) {
			if (empty ( $distractorUris )) {
				$uriForEachDistractor = false;
				break;
			}
		}
		if (! $uriForEachDistractor) {
			\common_Logger::w ( 'URI not found for some distractors: ' . var_export ( $distractorsMap, true ) );
			$report->add ( new \common_report_Report ( \common_report_Report::TYPE_WARNING, __ ( 'URI not found for some distractors' ), $distractorsMap ) );
		} else {
			$distractorsUrisPairs = Combinations::getAllPairs ( $distractorsUris );
			foreach ( $distractorsUrisPairs as $distractorsUrisPair ) {
				$left = $distractorsUrisPair [0];
				$right = $distractorsUrisPair [1];
				$uriPairs = array_merge ( $uriPairs, Combinations::getAllPairsBetween2Arrays ( $left, $right ) );
			}
		}
		return $uriPairs;
	}
}