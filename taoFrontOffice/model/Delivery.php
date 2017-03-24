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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *               
 * 
 */
namespace oat\taoFrontOffice\model;

use oat\oatbox\user\User;
/**
 * Delivery interface
 *
 * @author Open Assessment Technologies SA
 * @package taoFrontOffice
 * @license GPL-2.0
 *
 */
interface Delivery {
    
    /**
     * Returns the unique id of the delivery
     * 
     * @return string
     */
    public function getId();
    
    /**
     * Returns the test-taker label of the delivery
     * 
     * @return string
     */
    public function getLabel();
    
    /**
     * Returns a description of the delivery
     * 
     * @return string
     */
    public function getDescription();
    
    /**
     * Whenever or not a testtaker can take this delivery
     * 
     * @param User $testTaker
     * @return boolean
     */
    public function isTakeable(User $testTaker);
    
    /**
     * Gets the service call to run this delivery
     *
     * @return tao_models_classes_service_ServiceCall
     */
    public function getRuntime();
}