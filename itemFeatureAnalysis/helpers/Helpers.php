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

use itis\itemFeatureAnalysis\helpers\Helpers;

class Helpers {
	/**
	 * get a value in array if exists
	 *
	 * @param mixed[] $array
	 *        	associative array
	 * @param string $key        	
	 * @param mixed $default
	 *        	default value
	 * @return mixed the array value for corresponding key or $default value
	 */
	public static function getValueIfIsset($array, $key, $default = null) {
		return isset ( $array [$key] ) ? $array [$key] : $default;
	}
	public static function getFormatedException(\Exception $e) {
		return $e->getMessage () . PHP_EOL . $e->getTraceAsString ();
	}
	/**
	 *
	 * @param mixed[] $array
	 *        	array on which elements to call the callback function
	 * @param callable $cb        	
	 * @param mixed[] $params        	
	 * @return mixed[]
	 */
	public static function callFuncOnArray($array, $cb, $params = []) {
		$results = [ ];
		foreach ( $array as $element ) {
			$results [] = call_user_func_array ( $cb, array_merge ( $element, $params ) );
		}
		return $results;
	}
}
