<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2014 (update and modification) Open Assessment Technologies SA  
 */
namespace oat\taoSimpleDelivery\test\model;

use oat\tao\test\TaoPhpUnitTestRunner;
use \taoSimpleDelivery_models_classes_SimpleDeliveryService;
use \taoSimpleDelivery_models_classes_DeliveryCompiler;
use \taoQtiTest_models_classes_QtiTestService;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Class;
use \core_kernel_classes_Property;
use \common_ext_ExtensionsManager;
use \tao_models_classes_service_FileStorage;
use \common_report_Report;
use \taoTests_models_classes_TestsService;

class DeliveryCompilerTest extends TaoPhpUnitTestRunner
{

    /**
     *
     * @var taoSimpleDelivery_models_classes_SimpleDeliveryService
     */
    protected $deliveryService = null;

    protected $delivery = null;

    protected $test = null;


    /**
     * tests initialization
     */
    public function setUp()
    {
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        
        TaoPhpUnitTestRunner::initTest();
        $this->deliveryService = taoSimpleDelivery_models_classes_SimpleDeliveryService::singleton();
        $rootClass = $this->deliveryService->getRootClass();
        $testsService = taoTests_models_classes_TestsService::singleton();
        
        $samplesFile = __DIR__ . '/../samples/samples.zip';
        $testService = taoQtiTest_models_classes_QtiTestService::singleton();
        $rootclass = $testService->getRootclass();
        $report = $testService->importMultipleTests($rootclass,$samplesFile);
        
        $this->assertEquals(common_report_Report::TYPE_SUCCESS, $report->getType());
        
        foreach ($report as $rep) {
            $this->test = $rep->getData()->rdfsResource;
        }
        
        $report = $this->deliveryService->create($rootClass, $this->test, 'unitDelivery instance');
        $this->assertEquals(common_report_Report::TYPE_SUCCESS, $report->getType());
        $this->delivery = $report->getData();
        
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    protected function tearDown()
    {
        $this->test->delete();
        $this->assertNotNull($this->delivery);
        $this->delivery->delete();
    }

    /**
     * check delivery compilier compile
     * 
     * @param $deliveryCompiler
     *            
     */
    public function testCompile()
    {
        $storage = tao_models_classes_service_FileStorage::singleton();
        $compiler = new taoSimpleDelivery_models_classes_DeliveryCompiler($this->delivery, $storage);
        $this->assertInstanceOf('taoSimpleDelivery_models_classes_DeliveryCompiler', $compiler);
    }

    
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @expectedException oat\taoDeliveryTemplate\model\EmptyDeliveryException
     */
    public function testEmptyDeliveryCompiler(){
        $this->delivery->removePropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_TEST));
        $storage = tao_models_classes_service_FileStorage::singleton();
        $compiler = new taoSimpleDelivery_models_classes_DeliveryCompiler($this->delivery,$storage);
        $report =  $compiler->compile();
        
    }



}
