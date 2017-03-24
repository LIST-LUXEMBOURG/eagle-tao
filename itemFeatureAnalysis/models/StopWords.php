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
use itis\itemFeatureAnalysis\helpers\Text;

abstract class StopWords {
	protected static $ontologyFileNames = [ 
			'en' => ITEMFEATUREANALYSIS_STOPWORDS_EN_FILENAME,
			'fr' => ITEMFEATUREANALYSIS_STOPWORDS_FR_FILENAME,
			'de' => ITEMFEATUREANALYSIS_STOPWORDS_DE_FILENAME 
	];
	protected static $loadedOntologies = [ ];
	public static function filter($words, $language) {
		// set encoding due to multi-bytes string manipulation
		Text::setEncoding ();
		
		$lang = substr ( $language, 0, 2 );
		if (! array_key_exists ( $lang, self::$ontologyFileNames )) {
			throw new PreConditionFailureException ( __ ( 'Unsupported language for StopWords: ' . $language ) );
		}
		
		$ontology = self::getOntology ( $lang );
		
		$results = [ ];
		foreach ( $words as $word ) {
			if (! in_array ( mb_strtolower ( $word ), $ontology )) {
				$results [] = $word;
			}
		}
		
		return $results;
	}
	protected static function getOntology($lang) {
		if (array_key_exists ( $lang, self::$loadedOntologies )) {
			return self::$loadedOntologies [$lang];
		}
		$ontologyFileName = self::$ontologyFileNames [$lang];
		$data = file_get_contents ( $ontologyFileName, FILE_TEXT );
		if ($data === false) {
			throw new PreConditionFailureException ( __ ( 'cannot get file content: ' ) . $ontologyFileName );
		}
		
		$words = preg_split ( "/\r\n|\n|\r/", $data );
		
		// register ontology
		self::$loadedOntologies [$lang] = $words;
		
		return $words;
	}
}