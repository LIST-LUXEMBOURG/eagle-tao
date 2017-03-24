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
namespace itis\itemFeatureAnalysis\controller;

use itis\itemFeatureAnalysis\models\ItemFeatureAnalysisService;

class ItemFeatureAnalysis extends \tao_actions_TaoModule {
	public function __construct() {
		parent::__construct ();
		$this->service = ItemFeatureAnalysisService::singleton ();
		$this->defaultData ();
	}
	public function index() {
		// not supposed to be here
		\common_Logger::w ( 'not supposed to be here' );
	}
	/**
	 * Display a \common_report_Report
	 *
	 * @throws \common_Exception
	 */
	public function analyseItem() {
		if (! \tao_helpers_Request::isAjax ()) {
			throw new \common_Exception ( "wrong request mode" );
		}
		$rsc = $this->getCurrentInstance ();
		$report = $this->service->analyse ( [ 
				$rsc 
		], $save = true );
		
		$this->setData ( 'formTitle', __ ( 'Edit Instance' ) );
		$this->setData ( 'report', $report );
		$this->setView ( 'report.tpl', 'tao' );
	}
	protected function getRootClass() {
		return $this->service->getRootClass ();
	}
	protected function checkIfAjax() {
		if (! \tao_helpers_Request::isAjax ()) {
			throw new \common_Exception ( "wrong request mode" );
		}
	}
}
		