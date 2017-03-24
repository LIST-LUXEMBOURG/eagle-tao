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
 *         based on Hassane Seoud work
 */
namespace itis\itemFeatureAnalysis\models\metrics;

class JaroWinkler {
	/**
	 *
	 * @author Hassane Seoud
	 *        
	 * @param string $st1        	
	 * @param string $st2        	
	 * @return float Jaro Winkler Distance
	 */
	public static function compute($st1, $st2) {
		$s1 = 0;
		$s2 = 0;
		$m = 0;
		$t = 0;
		
		$ch2 = $st1;
		$ch1 = $st2;
		$s1 = strlen ( $ch1 );
		$s2 = strlen ( $ch2 );
		$E = $s1 + $s2 + abs ( $s1 - $s2 );
		$E = $E / 4;
		$E = $E - 1;
		$c1 = [ ];
		$c2 = [ ];
		if ($s1 < $s2) {
			$aux = $ch1;
			$ch1 = $ch2;
			$ch2 = $aux;
			$aux2 = $s1;
			$s1 = $s2;
			$s2 = $aux2;
		}
		
		for($j = 0; $j < $s2; $j ++) {
			for($i = 0; $i < $s1; $i ++) {
				if (($ch1 [$i] == $ch2 [$j]) && (abs ( $i - $j ) <= $E)) {
					$c1 [] = $i;
					$c2 [] = $j;
					$m ++;
				}
			}
		}
		if ($m == 0)
			return 0;
		
		$cc1 = [ ];
		$cc2 = [ ];
		for($i = 0; $i < $s1; $i ++) {
			if (in_array ( $i, $c1 ))
				$cc1 [] = $ch1 [$i];
		}
		for($i = 0; $i < $s2; $i ++) {
			if (in_array ( $i, $c2 ))
				$cc2 [] = $ch2 [$i];
		}
		$t1 = count ( $cc1 );
		$t2 = count ( $cc2 );
		if ($t1 < $t2) {
			$aux = $cc1;
			$cc1 = $cc2;
			$cc1 = $aux;
			$aux2 = $t1;
			$t1 = $t2;
			$t2 = $aux2;
		}
		$t = $t1 - $t2;
		
		for($i = 0; $i < $t2; $i ++) {
			if ($cc1 [$i] != $cc2 [$i])
				$t ++;
		}
		
		$t = $t / 2;
		
		$dj = ($m / $s1) + ($m / $s2) + ($m - $t) / $m;
		$dj = $dj / 3;
		
		$l = 0;
		for($i = 0; $i < 4; $i ++) {
			if ($ch1 [$i] != $ch2 [$i]) {
				break;
			} else {
				$l ++;
			}
		}
		
		$dw = $dj + ($l * 0.1 * (1 - $dj));
		
		return $dw;
	}
}