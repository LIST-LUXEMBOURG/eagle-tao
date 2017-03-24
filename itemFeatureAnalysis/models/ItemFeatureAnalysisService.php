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
namespace itis\itemFeatureAnalysis\models;

use itis\itemFeatureAnalysis\helpers\exceptions\PreConditionFailureException;
use itis\itemFeatureAnalysis\helpers\Helpers;
use itis\itemFeatureAnalysis\helpers\QtiItem;
use itis\itemFeatureAnalysis\helpers\Session;
use itis\itemFeatureAnalysis\models\interactionAnalysers\InlineChoiceInteractionAnalyser;
use itis\itemFeatureAnalysis\models\interactionAnalysers\MatchInteractionAnalyser;
use itis\itemFeatureAnalysis\models\interactionAnalysers\SimpleChoiceInteractionAnalyser;

class ItemFeatureAnalysisService extends \tao_models_classes_ClassService {
	/**
	 * used to compute the global time limit for the script
	 *
	 * @var int seconds
	 */
	const TIME_LIMIT__AVERAGE_ANALYSIS_PER_ITEM_IN_SECONDS = 20 * 60;
	protected $defaultClass = null;
	protected static $supportedInteractions = [ 
			'choiceInteraction',
			'inlineChoiceInteraction',
			'matchInteraction' 
	];
	protected static $salvagableInputTypes = [ 
			InputType::RESOURCE,
			InputType::URI 
	];
	protected function __construct() {
		parent::__construct ();
		\common_ext_ExtensionsManager::singleton ()->getExtensionById ( EXT_NAME )->load ();
		$this->defaultClass = new \core_kernel_classes_Class ( TAO_ITEM_CLASS );
	}
	public function getRootClass() {
		return $this->defaultClass;
	}
	protected static function analyseItems($items, $save = false, \common_report_Report $report) {
		$lang = \common_session_SessionManager::getSession ()->getDataLanguage ();
		
		foreach ( $items as $item ) {
			$itemReport = new \common_report_Report ( \common_report_Report::TYPE_INFO, __ ( 'Item Analysis Successful' ), [ 
					'input-item' => $item 
			] );
			$report->add ( $itemReport );
			try {
				$metrics = self::analyseItem ( $item, $save, $itemReport, $lang );
				$data = $itemReport->getData ();
				$data ['metrics'] = $metrics;
				$itemReport->setData ( $data );
			} catch ( \Exception $e ) {
				\common_Logger::w ( 'failed to analyse item: ' . var_export ( $item, true ) . ' Exception: ' . Helpers::getFormatedException ( $e ) );
				$itemReport->setMessage ( __ ( 'Item Analysis Failed' ) );
				$itemReport->setType ( \common_report_Report::TYPE_ERROR );
				$itemReport->setData ( Helpers::getFormatedException ( $e ) );
			}
			\common_Logger::i ( json_encode ( $itemReport, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) );
		}
	}
	public function analyse($items = [], $save = false, $lang = '') {
		// check input
		if (! is_bool ( $save )) {
			throw new PreConditionFailureException ( __ ( 'not a boolean' ) );
		}
		if (! is_array ( $items )) {
			throw new PreConditionFailureException ( __ ( 'not an array' ) );
		}
		if (empty ( $items )) {
			throw new PreConditionFailureException ( __ ( 'empty array' ) );
		}
		// build default report
		$report = \common_report_Report::createSuccess ( __ ( 'Analysis Successful' ) );
		// has to force language
		$previousSession = null;
		if (! empty ( $lang )) {
			$previousSession = \common_session_SessionManager::getSession ();
			// create dummy session to handle language (replace the current one!)
			\common_session_SessionManager::startSession ( new Session ( $lang ) );
		}
		// analyse items
		self::analyseItems ( $items, $save, $report );
		// restore previous session
		if ($previousSession) {
			\common_session_SessionManager::startSession ( $previousSession );
		}
		// prepare report
		// if error somewhere
		if ($report->containsError ()) {
			$report->setMessage ( __ ( 'Analysis successed with warnings' ) );
			$report->setType ( \common_report_Report::TYPE_WARNING );
			// if all report of first level are errors
			if (count ( $report->getErrors () ) == count ( $report->getIterator () )) {
				$report->setMessage ( __ ( 'Analysis failed' ) );
				$report->setType ( \common_report_Report::TYPE_ERROR );
			}
		}
		
		// log report
		\common_Logger::d ( json_encode ( $report, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) );
		
		return $report;
	}
	protected static function analyseItem($item, $save, \common_report_Report $report, $lang) {
		$metrics = [ ];
		if (empty ( $item )) {
			throw new PreConditionFailureException ( __ ( 'Empty data' ) );
		}
		// get input type
		$inputType = self::getInputType ( $item );
		$report->add ( new \common_report_Report ( \common_report_Report::TYPE_INFO, __ ( 'Input type: ' ) . $inputType ) );
		// get QTI
		$doc = self::getQtiWithInputType ( $item, $inputType );
		// analyse QTI
		$metrics = self::getMetrics ( $doc, $inputType, $report, $lang );
		// save metrics
		if ($save && in_array ( $inputType, self::$salvagableInputTypes )) {
			self::saveMetricsWithInputType ( $inputType, $item, $metrics );
			$report->add ( new \common_report_Report ( \common_report_Report::TYPE_INFO, __ ( 'Metrics saved' ), $metrics ) );
		}
		return $metrics;
	}
	protected static function getInteraction(\DOMElement $body, \common_report_Report $report) {
		$interactions = QtiItem::getElementsByTagNames ( $body, self::$supportedInteractions );
		if (empty ( $interactions )) {
			throw new PreConditionFailureException ( __ ( 'supported interaction not found' ) );
		}
		if (count ( $interactions ) > 1) {
			$report->add ( new \common_report_Report ( \common_report_Report::TYPE_WARNING, __ ( 'Found multiple interactions, handle the first one ' ) ) );
		}
		// use first interaction
		$interaction = reset ( $interactions );
		return $interaction;
	}
	protected static function getMetrics(\DOMDocument $doc, $inputType, \common_report_Report $report, $lang) {
		$assessmentItem = QtiItem::getAssessmentItem ( $doc );
		
		$itemBody = QtiItem::getItemBody ( $assessmentItem );
		
		$interaction = self::getInteraction ( $itemBody, $report );
		
		switch ($interaction->nodeName) {
			case 'choiceInteraction' :
				$interactionAnalyser = new SimpleChoiceInteractionAnalyser ( $interaction, $assessmentItem );
				break;
			case 'inlineChoiceInteraction' :
				$interactionAnalyser = new InlineChoiceInteractionAnalyser ( $interaction, $assessmentItem );
				break;
			case 'matchInteraction' :
				$interactionAnalyser = new MatchInteractionAnalyser ( $interaction, $assessmentItem );
				break;
			default :
				throw new PreConditionFailureException ( __ ( 'interaction type not supported: ' ) . $body->nodeName );
		}
		$metrics = $interactionAnalyser->analyse ( $lang, $report );
		
		return $metrics;
	}
	protected static function saveMetricsWithInputType($type, $item, $metrics = []) {
		$itemRsc = null;
		switch ($type) {
			case InputType::URI :
				$itemRsc = new \core_kernel_classes_Resource ( $item );
				break;
			case InputType::RESOURCE :
				$itemRsc = $item;
				break;
			default :
				throw new \UnexpectedValueException ( __ ( 'unsupported input type: ' ) . $type );
		}
		self::saveMetrics ( $itemRsc, $metrics );
	}
	protected static function saveMetrics(\core_kernel_classes_Resource $item, $metrics = []) {
		foreach ( $metrics as $metric => $value ) {
			$success = $item->editPropertyValues ( new \core_kernel_classes_Property ( $metric ), $value );
			if (! $success) {
				throw new \common_exception_Error ( __ ( 'failed to set property: ' ) . $metric );
			}
		}
	}
	protected static function getQtiWithInputType($item, $type) {
		switch ($type) {
			case InputType::URI :
				$itemRsc = new \core_kernel_classes_Resource ( $item );
				$doc = QtiItem::getQti ( $itemRsc );
				break;
			case InputType::RESOURCE :
				$doc = QtiItem::getQti ( $item );
				break;
			case InputType::RAW :
				$doc = QtiItem::getDocumentFromXml ( $item );
				break;
			default :
				throw new \UnexpectedValueException ( __ ( 'unsupported input type: ' ) . $type );
		}
		return $doc;
	}
	protected static function getInputType($item) {
		if (\common_Utils::isUri ( $item )) {
			return InputType::URI;
		}
		if ($item instanceof \core_kernel_classes_Resource) {
			return InputType::RESOURCE;
		}
		if (is_string ( $item )) {
			return InputType::RAW;
		}
		throw new PreConditionFailureException ( __ ( 'cannot define type of item' ) );
	}
}
	
	