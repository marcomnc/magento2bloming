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

class MpsSistemi_Gui_Model_Converter_Catalog_Blomming_Adapter_Product extends Mage_Catalog_Model_Convert_Adapter_Product
{
    
    /**
     * Recupero i dati applicando manualmente i filtri @@ todo
     */
    public function load() {
    
        
        parent::load();
       
    }
    
    /**
     * Retrieve not loaded collection
     *
     * @param string $entityType
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _getCollectionForLoad($entityType)
    {
        $collection = Mage::getResourceModel($entityType.'_collection')
            ->setStoreId($this->getStoreId())
            ->addStoreFilter($this->getStoreId());
        
        $category = $this->getVar('filter/category', '');
        
        if ($category != '') {
            $collection->joinField('category_id',
                                   'catalog/category_product',
                                   'category_id',
                                   'product_id=entity_id',
                                   null,
                                   'left')
                       ->addAttributeToFilter('category_id', array('in' => $category));
            $collection->getSelect()->group('e.entity_id');
        }
        
                
        return $collection;
    }
}
