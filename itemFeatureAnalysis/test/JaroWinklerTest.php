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
namespace itis\itemFeatureAnalysis\test;

use itis\itemFeatureAnalysis\models\ItemFeatureAnalysisService;
use oat\tao\test\TaoPhpUnitTestRunner;
use itis\itemFeatureAnalysis\models\metrics\JaroWinkler;

require_once dirname ( __FILE__ ) . '/../includes/raw_start.php';
/**
 * Unit tests (required phpunit >=4.8.5)
 *
 * @author Olivier Pedretti
 *        
 */
class JaroWinklerTest extends TaoPhpUnitTestRunner {
	
	// define services
	protected $itemFeatureAnalysisService = null;
	// others
	
	/**
	 * Called by phpunit before running tests
	 */
	protected function setUp() {
		TaoPhpUnitTestRunner::initTest ();
		$this->setServices ();
		
		parent::setUp ();
	}
	/**
	 * Called by phpunit after running tests whatever the test succeeded or failed
	 */
	protected function tearDown() {
		parent::tearDown ();
	}
	/**
	 * init required services
	 */
	protected function setServices() {
		$this->itemFeatureAnalysisService = ItemFeatureAnalysisService::singleton ();
		$this->assertInstanceOf ( ItemFeatureAnalysisService::class, $this->itemFeatureAnalysisService );
	}
	public function testJaroWinkler() {
		$newYork = 'NewYork';
		$london = 'London';
		$miami = 'Miami';
		$this->assertEquals ( 0.43650793650794, JaroWinkler::compute ( $newYork, $london ) );
		$this->assertEquals ( 0, JaroWinkler::compute ( $newYork, $miami ) );
		$this->assertEquals ( 0, JaroWinkler::compute ( $london, $miami ) );
	}
}



