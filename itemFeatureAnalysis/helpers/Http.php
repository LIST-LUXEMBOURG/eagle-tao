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

use itis\itemFeatureAnalysis\helpers\exceptions\ConnectFailedException;
use itis\itemFeatureAnalysis\helpers\exceptions\UnexpectedServerResponseException;

class Http {
	const OPTION_ACCEPT = 'accept';
	const OPTION_TIMEOUT = 'timeout';
	const OPTION_OK_HTTP_CODE = 'OkHttpCode';
	/**
	 *
	 * @param string $requestUrl        	
	 * @param [] $options
	 *        	supported value are OPTION_ACCEPT, OPTION_TIMEOUT, OPTION_OK_HTTP_CODE
	 * @throws ConnectFailedException
	 * @throws UnexpectedServerResponseException
	 * @return string Server response
	 */
	public static function sendRequest($requestUrl, $options = []) {
		\common_Logger::t ( __METHOD__ . ' ' . $requestUrl );
		$session = curl_init ( $requestUrl );
		if ($session === false) {
			throw new ConnectFailedException ( __ ( 'curl_init failed' ) );
		}
		
		// get options
		$accept = Helpers::getValueIfIsset ( $options, self::OPTION_ACCEPT, 'text/plain' );
		$timeout = Helpers::getValueIfIsset ( $options, self::OPTION_TIMEOUT, 0 );
		$OkHttpCode = Helpers::getValueIfIsset ( $options, self::OPTION_OK_HTTP_CODE, 200 );
		
		$returnValue = curl_setopt_array ( $session, array (
				// post method
				CURLOPT_HTTPGET => 1,
				CURLOPT_TIMEOUT => $timeout,
				CURLOPT_HTTPHEADER => [ 
						'Accept: ' . $accept 
				],
				// return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
				CURLOPT_RETURNTRANSFER => 1 
		) );
		if ($returnValue === false) {
			throw new ConnectFailedException ( __ ( 'cannot set curl options' ) );
		}
		
		// send request to server
		$returnedData = curl_exec ( $session );
		if (curl_errno ( $session )) {
			throw new ConnectFailedException ( __ ( 'session error: ' ) . curl_errno ( $session ) . ':' . curl_error ( $session ) );
		}
		
		// check the http code returned
		$httpCode = curl_getinfo ( $session, CURLINFO_HTTP_CODE );
		curl_close ( $session );
		if ($httpCode != $OkHttpCode) {
			throw new UnexpectedServerResponseException ( 'HTTP CODE: ' . $httpCode . ' request:' . $requestUrl . ' data: ' . $returnedData );
		}
		return $returnedData;
	}
}
