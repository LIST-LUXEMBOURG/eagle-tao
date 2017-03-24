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
use itis\itemFeatureAnalysis\helpers\QtiItem;
use itis\itemFeatureAnalysis\helpers\Text;

class MatchInteractionAnalyser extends InteractionAnalyser {
	const INTERACTION_TYPE = 'matchInteraction';
	public function analyse($lang, \common_report_Report $report) {
		$metrics = [ ];
		$columNode = QtiItem::getSecondMatchSet ( $this->interaction );
		$distractors = QtiItem::getDistractors ( $columNode, 'simpleAssociableChoice' );
		$distractorsCleaned = Text::cleanSentence ( $distractors );
		// JaroWinkler
		$distractorPairs = Combinations::getAllPairs ( $distractorsCleaned );
		self::getJaroWinklerMetrics ( $distractorPairs, $metrics, $report );
		// Disco
		$wordPairs = Text::buildWordPairsFromSentencePairs ( $distractorPairs );
		self::getDiscoMetrics ( $wordPairs, $metrics, $lang, $report );
		// Wordnet
		self::getWordNetMetrics ( $wordPairs, $metrics, $lang, $report );
		// TextDifficulty
		// get body without distractors
		$bodyNoIncorrectDistrators = $this->getItemBodyWithCorrectReponses ( 'simpleAssociableChoice' );
		// clean double spaces
		$bodyNoIncorrectDistrators = Text::removeMultipleSpaces ( $bodyNoIncorrectDistrators );
		self::getTextDifficultyMetrics ( $bodyNoIncorrectDistrators, $metrics, $lang, $report );
		// SemSim
		// Koda on body and all distractors
		$itemBodyNode = QtiItem::getItemBody ( $this->assessmentItem );
		self::getNodesValue ( $itemBodyNode, $textForKoda );
		$kodaPairs = self::getKodaUris ( $textForKoda, $lang, $report );
		$uriPairs = self::buildUriPairs ( $kodaPairs, $distractorsCleaned, $lang, $report );
		if (! empty ( $uriPairs )) {
			self::getSemSimMetrics ( $uriPairs, $metrics, $lang, $report );
		}
		return $metrics;
	}
	public function getSupportedInteractionType() {
		return $this::INTERACTION_TYPE;
	}
}