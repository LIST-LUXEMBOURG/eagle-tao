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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

class taoSimpleDelivery_actions_Authoring extends tao_actions_TaoModule
{
	
    private $contentModel;
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function __construct(){
        $this->contentModel = new taoSimpleDelivery_models_classes_ContentModel();
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_actions_TaoModule::getRootClass()
     */
    protected function getRootClass() {
	    
		return $model->getClass();
	}
    
	
	/**
	 * 
	 * @author Lionel Lecaque, lionel@taotesting.com
	 */
    public function wizard()
    {
        $this->defaultData();
        try {
            $formContainer = new \taoSimpleDelivery_actions_form_WizardForm(array('class' => $this->getCurrentClass()));
            $myForm = $formContainer->getForm();
             
            if ($myForm->isValid() && $myForm->isSubmited()) {
                $label = $myForm->getValue('label');
                $test = new core_kernel_classes_Resource($myForm->getValue('test'));
                $label = __("Delivery of %s", $test->getLabel());
                $deliveryClass = new core_kernel_classes_Class($myForm->getValue('classUri'));
                $report = taoSimpleDelivery_models_classes_SimpleDeliveryService::singleton()->create($deliveryClass, $test, $label);
                if ($report->getType() == common_report_Report::TYPE_SUCCESS) {
                    $assembly = $report->getdata();
                    $this->setData("selectNode", tao_helpers_Uri::encode($assembly->getUri()));
                    $this->setData('reload', true);
                    $this->setData('message', __('Delivery created'));
                    $this->setData('formTitle', __('Create a new delivery'));
                    $this->setView('form.tpl', 'tao');
                } else {
                    $this->setData('report', $report);
                    $this->setData('title', __('Error'));
                    $this->setView('report.tpl', 'tao');
                }
            } else {
                $this->setData('myForm', $myForm->render());
                $this->setData('formTitle', __('Create a new delivery'));
                $this->setView('form.tpl', 'tao');
            }
            
        } catch (taoSimpleDelivery_actions_form_NoTestsException $e) {
            $this->setView('wizard_error.tpl');
        }
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
	public function save()
    {
        $saved = false;
         
        $instance = $this->getCurrentInstance();
        $testUri = tao_helpers_Uri::decode($this->getRequestParameter(tao_helpers_Uri::encode(PROPERTY_DELIVERYCONTENT_TEST)));
    
        $saved = $this->contentModel->addTest($instance, new core_kernel_classes_Resource($testUri));
        //$saved = $instance->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_TEST ), $testUri);
         
        echo json_encode(array(
            'saved' => $saved
        ));
    }
}
