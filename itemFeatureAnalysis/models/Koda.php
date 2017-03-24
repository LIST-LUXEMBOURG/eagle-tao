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
use itis\itemFeatureAnalysis\helpers\exceptions\UnexpectedServerResponseException;
use itis\itemFeatureAnalysis\helpers\Http;

abstract class Koda {
	protected static $ontologies = [ 
			'en' => ITEMFEATUREANALYSIS_KODA_WEBSERVICE_ONTOLOGY_EN,
			'fr' => ITEMFEATUREANALYSIS_KODA_WEBSERVICE_ONTOLOGY_FR,
			'de' => ITEMFEATUREANALYSIS_KODA_WEBSERVICE_ONTOLOGY_DE 
	];
	public static function compute($input, $language) {
		$lang = substr ( $language, 0, 2 );
		if (! array_key_exists ( $lang, self::$ontologies )) {
			throw new PreConditionFailureException ( __ ( 'Unsupported language for Koda: ' . $language ) );
		}
		$params = [ 
				'ontology' => self::$ontologies [$lang],
				'text' => $input 
		];
		$requestUrl = ITEMFEATUREANALYSIS_KODA_WEBSERVICE_URL . '?' . http_build_query ( $params );
		
		$data = Http::sendRequest ( $requestUrl, [ 
				Http::OPTION_TIMEOUT => ITEMFEATUREANALYSIS_KODA_WEBSERVICE_TIMEOUT_IN_SECONDS 
		] );
		$lines = preg_split ( "/\r\n|\n|\r/", $data );
		// remove empty line
		$lines = array_filter ( $lines );
		$results = [ ];
		foreach ( $lines as $line ) {
			$pair = explode ( "\t", $line );
			if (count ( $pair ) != 2) {
				throw new UnexpectedServerResponseException ( __ ( 'Unexpected format: ' . var_export ( $pair, true ) ) );
			}
			$results [$pair [0]] = $pair [1];
		}
		return $results;
	}
}