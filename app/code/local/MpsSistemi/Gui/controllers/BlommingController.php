<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *  
 *
 * @category    
 * @package        
 * @copyright   Copyright (c) 2013 Mps Sistemi (http://www.mps-sistemi.it)
 * @author      MPS Sistemi S.a.s - Marco Mancinelli <marco.mancinelli@mps-sistemi.it>
 *
 */

class MpsSistemi_Gui_BlommingController extends Mage_Adminhtml_Controller_Action
{
    
    public function autogeneratecategoryAction() {
        
        
        $m = Mage::getModel('catalog/resource_eav_attribute')
                ->loadByCode('catalog_category','category_blomming');
        
        
        if ($m->getId() === null) {

            $installer = Mage::getModel('eav/entity_setup', 'core_setup');

            try {
                $installer->startSetup();
                $entityTypeId     = $installer->getEntityTypeId('catalog_category');
                $attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
                $attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);
                $installer->addAttribute('catalog_category', 'category_blomming',  array(
                    'type'     => 'varchar',
                    'label'    => 'Blomming Category',
                    'input'    => 'text',
                    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => ''
                    ));
                $installer->addAttributeToGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $attributeGroupId,
                    'category_blomming',
                    '15'                    //last Magento's attribute position in General tab is 10
                );

                $installer->endSetup();
                Mage::getSingleton('adminhtml/session')->addSuccess('Attribute created. Please Reindex data!');        
            } catch (Exception $ex) {
                Mage::LogException($ex);
                Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());        
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addSuccess('Attribute alredy exists!');        
        }
        
        $this->_redirectReferer();
        
    }
}
    
