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

abstract class SemSim {
	protected static $supportedLanguages = [ 
			'en' 
	];
	public static function compute($uri1, $uri2, $language) {
		$lang = substr ( $language, 0, 2 );
		if (! in_array ( $lang, self::$supportedLanguages )) {
			throw new PreConditionFailureException ( __ ( 'Unsupported language for SemSim: ' . $language ) );
		}
		$params = [ 
				'thisUri' => $uri1,
				'thatUri' => $uri2,
				'ontology' => ITEMFEATUREANALYSIS_SEMSIM_WEBSERVICE_ONTOLOGY_EN 
		];
		
		$requestUrl = ITEMFEATUREANALYSIS_SEMSIM_WEBSERVICE_URL . '?' . http_build_query ( $params );
		
		$data = Http::sendRequest ( $requestUrl, [ 
				Http::OPTION_TIMEOUT => ITEMFEATUREANALYSIS_SEMSIM_WEBSERVICE_TIMEOUT_IN_SECONDS 
		] );
		
		if (preg_match ( '/^AS (\d+(?:\.\d+)?) - RS (\d+(?:\.\d+)?) - TS (\d+(?:\.\d+)?) Instance Similarity: (\d+(?:\.\d+)?)$/', $data, $matches ) !== 1) {
			\common_Logger::e ( 'Bad response format: ' . $data );
			throw new UnexpectedServerResponseException ( __ ( 'Bad response format: ' ) . $data );
		}
		$result_as = floatval ( $matches [1] );
		$result_rs = floatval ( $matches [2] );
		$result_ts = floatval ( $matches [3] );
		$result_is = floatval ( $matches [4] );
		
		return $result_is;
	}
}