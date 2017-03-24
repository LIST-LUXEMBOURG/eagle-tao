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
namespace itis\itemFeatureAnalysis\helpers;

use itis\itemFeatureAnalysis\helpers\Combinations;
use itis\itemFeatureAnalysis\helpers\exceptions\PreConditionFailureException;

abstract class Combinations {
	/**
	 * Build all combinations from a non associative array
	 *
	 * @param array $elements
	 *        	a non associative array
	 * @throws PreConditionFailureException
	 * @return [][]
	 */
	public static function getAllPairs($elements = []) {
		$pairs = [ ];
		$nbOfElements = count ( $elements );
		if ($nbOfElements < 2) {
			throw new PreConditionFailureException ( __ ( 'need at least 2 elements, found: ' . $nbOfElements ) );
		}
		// compare all elements together
		// for each element except the last one
		for($i = 0; $i < $nbOfElements - 1; $i ++) {
			// for each word to the right of previous one
			for($j = $i + 1; $j < $nbOfElements; $j ++) {
				$pairs [] = [ 
						$elements [$i],
						$elements [$j] 
				];
			}
		}
		return $pairs;
	}
	/**
	 * Build all combinations from two arrays
	 *
	 * @param array $array1        	
	 * @param array $array2        	
	 * @return [][]
	 */
	public static function getAllPairsBetween2Arrays($array1 = [], $array2 = []) {
		$pairs = [ ];
		foreach ( $array1 as $element1 ) {
			foreach ( $array2 as $element2 ) {
				$pairs [] = [ 
						$element1,
						$element2 
				];
			}
		}
		return $pairs;
	}
}