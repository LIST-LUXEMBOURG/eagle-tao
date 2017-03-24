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

use itis\itemFeatureAnalysis\helpers\Stats;

abstract class Stats {
	/**
	 *
	 * @param number[] $elements        	
	 * @return number
	 */
	public static function average($elements = []) {
		return array_sum ( $elements ) / count ( $elements );
	}
	/**
	 *
	 * @param number[] $elements        	
	 * @return number
	 */
	public static function standardDeviation($elements = []) {
		$stdDeviation = 0;
		$nbOfElements = count ( $elements );
		for($i = 0; $i < $nbOfElements; $i ++) {
			$stdDeviation += pow ( $elements [$i] - self::average ( $elements ), 2 );
		}
		$stdDeviation = sqrt ( $stdDeviation / $nbOfElements );
		return $stdDeviation;
	}
}