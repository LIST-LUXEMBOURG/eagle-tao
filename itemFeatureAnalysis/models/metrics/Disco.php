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
use itis\itemFeatureAnalysis\helpers\Http;

abstract class Disco {
	protected static $supportedLanguages = [ 
			'en',
			'fr',
			'de' 
	];
	public static function compute($element1, $element2, $language) {
		$lang = substr ( $language, 0, 2 );
		if (! in_array ( $lang, self::$supportedLanguages )) {
			throw new PreConditionFailureException ( __ ( 'Unsupported language for Disco: ' . $language ) );
		}
		$params = [ 
				'firstWord' => $element1,
				'secondWord' => $element2,
				'language' => $lang 
		];
		$requestUrl = ITEMFEATUREANALYSIS_DISCO_WEBSERVICE_URL . '?' . http_build_query ( $params );
		
		$data = Http::sendRequest ( $requestUrl, [ 
				Http::OPTION_TIMEOUT => ITEMFEATUREANALYSIS_DISCO_WEBSERVICE_TIMEOUT_IN_SECONDS 
		] );
		
		$data = floatval ( $data );
		
		return $data;
	}
}