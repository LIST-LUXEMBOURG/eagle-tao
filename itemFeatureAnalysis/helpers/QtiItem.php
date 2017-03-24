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

use itis\itemFeatureAnalysis\helpers\exceptions\PreConditionFailureException;

abstract class QtiItem {
	/**
	 *
	 * @param \DOMElement $assessmentItem        	
	 * @param \DOMElement $interaction        	
	 * @throws PreConditionFailureException
	 * @return string[][]
	 */
	public static function getCorrectResponses(\DOMElement $assessmentItem, \DOMElement $interaction) {
		$correctDistractorsNodes = self::getCorrectDistractorsNodes ( $assessmentItem, $interaction );
		
		$correctDistractorsNodesValues = array_map ( function ($array) {
			return array_map ( function ($node) {
				return $node->nodeValue;
			}, $array );
		}, $correctDistractorsNodes );
		
		return $correctDistractorsNodesValues;
	}
	public static function getCorrectResponsesIds(\DOMElement $assessmentItem, \DOMElement $interaction) {
		$responseDeclaration = self::getResponseDeclaration ( $assessmentItem, $interaction );
		
		$correctResponseEls = $responseDeclaration->getElementsByTagName ( 'correctResponse' );
		if ($correctResponseEls->length !== 1) {
			throw new PreConditionFailureException ( __ ( 'Bad number of correct response declaration, found: ' . $correctResponseEls->length ) );
		}
		$firstCorrectResponseEl = $correctResponseEls->item ( 0 );
		$values = $firstCorrectResponseEl->getElementsByTagName ( 'value' );
		$correctResponsesIds = [ ];
		foreach ( $values as $value ) {
			$correctResponsesIds [] = explode ( ' ', $value->nodeValue );
		}
		return $correctResponsesIds;
	}
	/**
	 *
	 * @param string[][] $correctResponsesIds        	
	 * @param ['responseId'=>'responseValue'] $responseMap        	
	 * @return string[][]
	 */
	protected static function buildCorrectResponseArray($correctResponsesIds, $responseMap) {
		$responses = [ ];
		foreach ( $correctResponsesIds as $correctResponseIds ) {
			$response = [ ];
			foreach ( $correctResponseIds as $correctResponseId ) {
				if (! isset ( $responseMap [$correctResponseId] )) {
					throw new PreConditionFailureException ( __ ( 'Correct distrator not found with id: ' ) . $correctResponseId );
				}
				$response [] = $responseMap [$correctResponseId];
			}
			$responses [] = $response;
		}
		return $responses;
	}
	/**
	 *
	 * @param string[][] $correctResponsesIds        	
	 * @param ['responseId'=>'responseNode'] $responseMap        	
	 * @return \DOMElement[][]
	 */
	protected static function buildCorrectResponseNodesArray($correctResponsesIds, $responseMap) {
		$responses = [ ];
		foreach ( $correctResponsesIds as $correctResponseIds ) {
			$response = [ ];
			foreach ( $correctResponseIds as $correctResponseId ) {
				if (! isset ( $responseMap [$correctResponseId] )) {
					throw new PreConditionFailureException ( __ ( 'Correct distrator not found with id: ' ) . $correctResponseId );
				}
				$response [] = $responseMap [$correctResponseId];
			}
			$responses [] = $response;
		}
		return $responses;
	}
	/**
	 *
	 * @param \DOMElement $node        	
	 * @throws PreConditionFailureException
	 * @return \DOMElement[]
	 */
	protected static function getResponseDeclarations(\DOMElement $node) {
		$responseDeclarations = [ ];
		$responseDeclarationNodes = $node->getElementsByTagName ( 'responseDeclaration' );
		if ($responseDeclarationNodes->length === 0) {
			throw new PreConditionFailureException ( __ ( 'responseDeclaration not found' ) );
		}
		foreach ( $responseDeclarationNodes as $responseDeclarationNode ) {
			$responseDeclarations [$responseDeclarationNode->getAttribute ( 'identifier' )] = $responseDeclarationNode;
		}
		return $responseDeclarations;
	}
	/**
	 *
	 * @param \DOMElement $assessmentItem        	
	 * @param \DOMElement $interaction        	
	 * @throws PreConditionFailureException
	 * @return \DOMElement
	 */
	protected static function getResponseDeclaration(\DOMElement $assessmentItem, \DOMElement $interaction) {
		// get interaction responseIdentifier
		$activityResponseDeclarationIdentifier = $interaction->getAttribute ( 'responseIdentifier' );
		if (! $activityResponseDeclarationIdentifier) {
			throw new PreConditionFailureException ( __ ( 'No responseIdentifier found for the interaction' ) );
		}
		$responseDeclarations = self::getResponseDeclarations ( $assessmentItem );
		if (! isset ( $responseDeclarations [$activityResponseDeclarationIdentifier] )) {
			throw new PreConditionFailureException ( __ ( 'Response declaration not found with id: ' ) . $activityResponseDeclarationIdentifier );
		}
		return $responseDeclarations [$activityResponseDeclarationIdentifier];
	}
	/**
	 *
	 * @param \DOMElement $node        	
	 * @param [] $target        	
	 * @param string $typeOfChoice        	
	 */
	protected static function addChoiceValues(\DOMElement $node, &$target, $typeOfChoice) {
		$choices = $node->getElementsByTagName ( $typeOfChoice );
		foreach ( $choices as $choice ) {
			$target [$choice->getAttribute ( 'identifier' )] = $choice->nodeValue;
		}
	}
	protected static function addChoiceNodes(\DOMElement $node, &$target, $typeOfChoice) {
		$choices = $node->getElementsByTagName ( $typeOfChoice );
		foreach ( $choices as $choice ) {
			$target [$choice->getAttribute ( 'identifier' )] = $choice;
		}
	}
	/**
	 *
	 * @param \DOMElement $interaction        	
	 * @return string[][]
	 */
	protected static function getResponseMap(\DOMElement $interaction) {
		$typesOfChoice = [ 
				'simpleAssociableChoice',
				'associableChoice',
				'inlineChoice',
				'simpleChoice' 
		];
		$responseMap = [ ];
		foreach ( $typesOfChoice as $typeOfChoice ) {
			self::addChoiceValues ( $interaction, $responseMap, $typeOfChoice );
		}
		return $responseMap;
	}
	protected static function getResponseMapNodes(\DOMElement $interaction) {
		$typesOfChoice = [ 
				'simpleAssociableChoice',
				'associableChoice',
				'inlineChoice',
				'simpleChoice' 
		];
		$responseMap = [ ];
		foreach ( $typesOfChoice as $typeOfChoice ) {
			self::addChoiceNodes ( $interaction, $responseMap, $typeOfChoice );
		}
		return $responseMap;
	}
	/**
	 *
	 * @param \DOMElement $node        	
	 * @throws PreConditionFailureException
	 * @return \DOMElement
	 */
	public static function getSecondMatchSet(\DOMElement $node) {
		$sets = self::getMatchSets ( $node );
		if (count ( $sets ) < 2) {
			throw new PreConditionFailureException ( __ ( 'second simpleMatchSet not found, found: ' ) . count ( $sets ) );
		}
		return end ( $sets );
	}
	/**
	 *
	 * @param \DOMElement $node        	
	 * @return \DOMElement[]
	 */
	public static function getMatchSets(\DOMElement $node) {
		$results = self::getElementsByTagNames ( $node, [ 
				'simpleMatchSet' 
		] );
		return $results;
	}
	/**
	 *
	 * @param \DOMElement $node        	
	 * @param [] $names        	
	 * @throws PreConditionFailureException
	 * @return \DOMElement[]
	 */
	public static function getElementsByTagNames(\DOMElement $node, $names = []) {
		if (! is_array ( $names )) {
			throw new PreConditionFailureException ( __ ( 'not an array: ' ) . var_export ( $names, true ) );
		}
		$elements = [ ];
		foreach ( $names as $name ) {
			$nodes = $node->getElementsByTagName ( $name );
			foreach ( $nodes as $node ) {
				$elements [] = $node;
			}
		}
		return $elements;
	}
	/**
	 *
	 * @param \DOMElement $node        	
	 * @throws PreConditionFailureException
	 * @return \DOMElement
	 */
	public static function getPrompt(\DOMElement $node) {
		$prompts = $node->getElementsByTagName ( 'prompt' );
		if ($prompts->length === 0) {
			throw new PreConditionFailureException ( __ ( 'no prompt in item' ) );
		}
		$prompt = $prompts->item ( 0 );
		return $prompt;
	}
	/**
	 *
	 * @param \DOMElement $node        	
	 * @throws PreConditionFailureException
	 * @return \DOMElement
	 */
	public static function getItemBody(\DOMElement $node) {
		$itemBodies = $node->getElementsByTagName ( 'itemBody' );
		if ($itemBodies->length === 0) {
			throw new PreConditionFailureException ( __ ( 'no body in item' ) );
		}
		$itemBody = $itemBodies->item ( 0 );
		return $itemBody;
	}
	/**
	 *
	 * @param \DOMDocument $doc        	
	 * @throws PreConditionFailureException
	 * @return \DOMDocument
	 */
	public static function getAssessmentItem(\DOMDocument $doc) {
		$assessmentItem = $doc->documentElement;
		if (strcmp ( $assessmentItem->nodeName, 'assessmentItem' ) !== 0) {
			throw new PreConditionFailureException ( __ ( 'not an assessmentItem, found: ' ) . $assessmentItem->nodeName );
		}
		return $assessmentItem;
	}
	/**
	 *
	 * @param string $xmlString        	
	 * @throws PreConditionFailureException
	 * @return \DOMDocument
	 */
	public static function getDocumentFromXml($xmlString) {
		$doc = new \DOMDocument ();
		$doc->preserveWhiteSpace = false;
		if ($doc->loadXML ( $xmlString ) === false) {
			throw new PreConditionFailureException ( __ ( 'Failed to load XML' ) );
		}
		return $doc;
	}
	public static function getCorrectDistractorsNodes(\DOMElement $assessmentItem, \DOMElement $interaction) {
		$responseMapNodes = self::getResponseMapNodes ( $interaction );
		$correctResponsesIds = self::getCorrectResponsesIds ( $assessmentItem, $interaction );
		return self::buildCorrectResponseNodesArray ( $correctResponsesIds, $responseMapNodes );
	}
	/**
	 *
	 * @param \DOMElement $interaction        	
	 * @param string $choiceType        	
	 * @return string[]
	 */
	public static function getDistractors(\DOMElement $interaction, $choiceType) {
		$distractors = [ ];
		$distractorsNodes = self::getDistractorsNodes ( $interaction, $choiceType );
		foreach ( $distractorsNodes as $distractorsNode ) {
			$distractors [] = $distractorsNode->nodeValue;
		}
		return $distractors;
	}
	/**
	 *
	 * @param \DOMElement $interaction        	
	 * @param string $choiceType        	
	 * @return \DOMElement[]
	 */
	public static function getDistractorsNodes(\DOMElement $interaction, $choiceType) {
		$distractors = [ ];
		$choices = $interaction->getElementsByTagName ( $choiceType );
		foreach ( $choices as $choice ) {
			$distractors [] = $choice;
		}
		return $distractors;
	}
	/**
	 *
	 * @param \core_kernel_classes_Resource $item        	
	 * @throws PreConditionFailureException
	 * @return \DOMDocument
	 */
	public static function getQti(\core_kernel_classes_Resource $item) {
		$itemService = \taoItems_models_classes_ItemsService::singleton ();
		// is an item?
		if (! $item->isInstanceOf ( $itemService->getRootClass () )) {
			throw new PreConditionFailureException ( __ ( 'Not an instance of: ' ) . $itemService->getRootClass () );
		}
		// has item model?
		$itemModel = $itemService->getItemModel ( $item );
		if (! ($itemModel instanceof \core_kernel_classes_Resource)) {
			throw new PreConditionFailureException ( __ ( 'No item model found' ) );
		}
		// is QTI
		if (strcmp ( $itemModel->getUri (), TAO_ITEM_MODEL_QTI ) !== 0) {
			throw new PreConditionFailureException ( __ ( 'Not a QTI item' ) );
		}
		// has content
		if (! $itemService->hasItemContent ( $item )) {
			throw new PreConditionFailureException ( __ ( 'No item content' ) );
		}
		// get content
		$qti = $itemService->getItemContent ( $item );
		if (empty ( $qti )) {
			throw new PreConditionFailureException ( __ ( 'No data found' ) );
		}
		// load content as DOM
		$doc = self::getDocumentFromXml ( $qti );
		return $doc;
	}
}
