<?php
use oat\taoDeliveryTemplate\rdf\DeliveryContent;
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

/**
 * Create a form from a  resource of your ontology. 
 * Each property will be a field, regarding it's widget.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class taoSimpleDelivery_actions_form_ContentForm
    extends tao_actions_form_Instance
{
    public function __construct($class, $content) {
        parent::__construct($class, $content, array(
            'topClazz' => DeliveryContent::CLASS_URI,
            'excludedProperties' => array(RDFS_LABEL)
        ));
    }
    
    protected function initForm()
    {
        parent::initForm();
        $this->form->setActions(array(), 'top');
        $this->form->setActions(array(), 'bottom');
    }
}