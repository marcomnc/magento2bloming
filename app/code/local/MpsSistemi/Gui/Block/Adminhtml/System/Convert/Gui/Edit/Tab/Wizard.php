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

class MpsSistemi_Gui_Block_Adminhtml_System_Convert_Gui_Edit_Tab_Wizard extends Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_Wizard {
    
    /**
     * Override della funzione standard per prendere ul phtml custom
     * @return type
     */
    public function _toHtml() {
        ob_start();
        include __DIR__ .'/wizard.phtml';
        $html = ob_get_clean();
        return $html;
    }
    
    /**
     * Ritorna la lista delle categorie per la selezione
     */
    public function getSelectCategoryList($id = 'product_blomming') {
    
        $coll = Mage::getModel('catalog/category')->getCollection() 
                        ->setStoreId(0)
                        ->addAttributeToSelect('entity_id') 
                        ->addAttributeToSelect('name');
        
        $myCat = array();
        foreach ($coll as $c) {
            
            if ($c->getLevel() > 1)
                $myCat[$c->getId()] = $c->getName();
            
        }
        $field = New Varien_Data_Form_Element_Custommultiselect( array(
                'id'        => 'category',
                'label'     => Mage::helper('catalog')->__('Category'),
                'title'     => Mage::helper('catalog')->__('Category'),
                'name'      => "gui_data[$id][filter][category]",
                'values'    => $myCat,
                'value'     => $this->getData('gui_data/product_blomming/filter/category'),
            ));
        $form = new Varien_Data_Form();
        return $field->setForm($form->setHtmlIdPrefix('product_filter_category'))->toHtml();
        
    }     
        
}
