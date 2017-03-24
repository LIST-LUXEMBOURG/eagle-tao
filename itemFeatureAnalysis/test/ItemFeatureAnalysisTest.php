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

use itis\itemFeatureAnalysis\helpers\Session;
use itis\itemFeatureAnalysis\models\ItemFeatureAnalysisService;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\qti\ImportService;

require_once dirname ( __FILE__ ) . '/../includes/raw_start.php';
/**
 * Unit tests (required phpunit >=4.8.5)
 *
 * @author Olivier Pedretti
 *        
 */
class ItemFeatureAnalysisTest extends TaoPhpUnitTestRunner {
	
	// define services
	protected $itemFeatureAnalysisService = null;
	protected $itemService = null;
	protected $qtiItemImportService = null;
	// define resources to clean up
	protected $toBeCleanedItems = [ ];
	// others
	
	/**
	 * Called by phpunit before running tests
	 */
	protected function setUp() {
		TaoPhpUnitTestRunner::initTest ();
		// error_reporting(E_ALL);
		$this->setServices ();
		
		parent::setUp ();
	}
	/**
	 * Called by phpunit after running tests whatever the test succeeded or failed
	 */
	protected function tearDown() {
		$this->cleanUpResources ();
		
		parent::tearDown ();
	}
	/**
	 * init required services
	 */
	protected function setServices() {
		$this->itemFeatureAnalysisService = ItemFeatureAnalysisService::singleton ();
		$this->assertInstanceOf ( ItemFeatureAnalysisService::class, $this->itemFeatureAnalysisService );
		$this->loadService ( 'taoItems' );
		$this->itemService = \taoItems_models_classes_ItemsService::singleton ();
		$this->assertInstanceOf ( \taoItems_models_classes_ItemsService::class, $this->itemService );
		$this->loadService ( 'taoQtiItem' );
		$this->qtiItemImportService = ImportService::singleton ();
		$this->assertInstanceOf ( ImportService::class, $this->qtiItemImportService );
	}
	/**
	 * Ensure that extension is available
	 *
	 * @param string $id        	
	 */
	protected function loadService($id) {
		$extensionManager = \common_ext_ExtensionsManager::singleton ();
		$this->assertTrue ( $extensionManager->isInstalled ( $id ) );
		$this->assertTrue ( $extensionManager->isEnabled ( $id ) );
		$ext = $extensionManager->getExtensionById ( $id );
		if (! $ext->isLoaded ()) {
			$ext->load ();
		}
	}
	/**
	 * Remove generated resources during test
	 */
	protected function cleanUpResources() {
		if ($this->toBeCleanedItems !== null) {
			foreach ( $this->toBeCleanedItems as $rsc ) {
				$deleteOK = $this->itemService->deleteResource ( $rsc );
				$this->assertTrue ( $deleteOK );
			}
		}
	}
	/**
	 * Run item analysis
	 */
	public function testItemFeatureAnalysis() {
		// config
		$lang = 'en-US';
		$save = false;
		$itemList = $this->prepareItems ( $lang );
		// run analysis
		$report = $this->itemFeatureAnalysisService->analyse ( $itemList, $save, $lang );
		// parse report
		$this->assertInstanceOf ( \common_report_Report::class, $report );
		$this->assertTrue ( ($report->getType () == \common_report_Report::TYPE_SUCCESS) || ($report->getType () == \common_report_Report::TYPE_WARNING) );
		$children = $report->getIterator ();
		foreach ( $children as $child ) {
			// TODO FIXME check data
		}
	}
	protected function prepareItems($lang) {
		$toAnalyse = [ ];
		
		// prepare an item as raw QTI
		$qtiXml = $this->getQtiItem ( 'choiceInteraction.xml' );
		$toAnalyse [] = $qtiXml;
		
		// prepare an item as Resource
		$qtiXml = $this->getQtiItem ( 'inlineChoice.xml' );
		$itemRsc = $this->saveQtiItemInTAO ( $qtiXml, $lang );
		$toAnalyse [] = $itemRsc;
		
		// prepare an item as URI
		$qtiXml = $this->getQtiItem ( 'matchInteraction.xml' );
		$itemRsc = $this->saveQtiItemInTAO ( $qtiXml, $lang );
		$toAnalyse [] = $itemRsc->getUri ();
		
		return $toAnalyse;
	}
	protected function initSession($lang) {
		$session = new Session ( $lang );
		\common_session_SessionManager::startSession ( $session );
	}
	protected function getQtiItem($filename) {
		$qti = file_get_contents ( dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'items' . DIRECTORY_SEPARATOR . $filename );
		$this->assertNotFalse ( $qti );
		return $qti;
	}
	protected function saveQtiItemInTAO($qtiItem, $lang) {
		$this->initSession ( $lang );
		$report = $this->qtiItemImportService->importQTIFile ( $qtiItem, $this->itemService->getRootClass (), true );
		$itemRsc = $this->parseReport ( $report );
		$this->assertInstanceOf ( \core_kernel_classes_Resource::class, $itemRsc );
		$this->toBeCleanedItems [] = $itemRsc;
		return $itemRsc;
	}
	protected function parseReport(\common_report_Report $report) {
		if ($report->containsError ()) {
			throw new \common_exception_Error ( $report->getMessage () );
		}
		return $report->getData ();
	}
}