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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author lionel
 * @license GPLv2
 * @package package_name
 * @subpackage 
 *
 */
namespace oat\taoSimpleDelivery\test\model;

use oat\tao\test\TaoPhpUnitTestRunner;
use \common_ext_ExtensionsManager;
use \taoSimpleDelivery_models_classes_ContentModel;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Property;
use \taoTests_models_classes_TestsService;

/**
 * Test ContentModel
 *
 * @author Lionel Lecaque, lionel@taotesting.com
 */
class ContentModelTest extends TaoPhpUnitTestRunner
{

    protected $contentModel;

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function setUp()
    {
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        TaoPhpUnitTestRunner::initTest();
        $this->contentModel = new taoSimpleDelivery_models_classes_ContentModel();
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testCreateContent()
    {
        $content = $this->contentModel->createContent(array());
        $this->assertInstanceOf('core_kernel_classes_Resource', $content);
        $types = $content->getTypes();
        $this->assertInstanceOf('core_kernel_classes_Resource', current($types));
        $this->assertEquals(CLASS_SIMPLE_DELIVERYCONTENT, current($types)->getUri());
        $content->delete();
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testAddTest()
    {
        $content = $this->contentModel->createContent(array());
        $testsService = taoTests_models_classes_TestsService::singleton();
        $test = $testsService->createInstance($testsService->getRootclass(), 'deliveryUnitCompilerTest');
        
        $this->assertTrue($this->contentModel->addTest($content, $test));
        $value = $content->getPropertiesValues(array(
            new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_TEST)
        ));
        $this->assertInstanceOf('core_kernel_classes_Resource', current($value[PROPERTY_DELIVERYCONTENT_TEST]));
        $this->assertEquals($test->getUri(), current($value[PROPERTY_DELIVERYCONTENT_TEST])->getUri());
        
        $content->delete();
        $test->delete();
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testCloneContent()
    {
        $content = $this->contentModel->createContent(array());
        $testsService = taoTests_models_classes_TestsService::singleton();
        $test = $testsService->createInstance($testsService->getRootclass(), 'deliveryUnitCompilerTest');
        
        $this->assertTrue($this->contentModel->addTest($content, $test));
        
        $result = $this->contentModel->cloneContent($content);
        $this->assertInstanceOf('core_kernel_classes_Resource', $result);
        $this->assertNotEquals($content->getUri(), $result->getUri());
        $contentType = $content->getTypes();
        $resultType = $result->getTypes();
        $this->assertInstanceOf('core_kernel_classes_Resource', current($contentType));
        $this->assertInstanceOf('core_kernel_classes_Resource', current($resultType));
        
        $this->assertEquals(current($contentType)->getUri(), current($resultType)->getUri());
        
        // check if testprop is also clone
        $value = $result->getPropertiesValues(array(
            new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_TEST)
        ));
        $this->assertInstanceOf('core_kernel_classes_Resource', current($value[PROPERTY_DELIVERYCONTENT_TEST]));
        $this->assertEquals($test->getUri(), current($value[PROPERTY_DELIVERYCONTENT_TEST])->getUri());
        
        $result->delete();
        $content->delete();
        $test->delete();
    }
}