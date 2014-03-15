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

class MpsSistemi_Gui_Model_Converter_Catalog_Blomming_Dataflow_Mapper_Column 
    extends Mage_Dataflow_Model_Convert_Mapper_Column
    implements Mage_Dataflow_Model_Convert_Mapper_Interface
{
    
    public function map() {
        
        $cardinality = Mage::Registry('_mpsgui_cardinality');
        
        $attributesToSelect = array(   
            "sku"               => "sku"               ,
            "image"             => "img"               ,
            "img1"              => "img1"              ,
            "img2"              => "img2"              ,
            "img3"              => "img3"              ,
            "name"              => "title"             ,
            "description"       => "description"       ,
            "qty"               => "quantity"          ,
            "final_price"       => "price"             ,
            "price"             => "original_price"    ,
            "shipping_profile"  => "shipping_profile"  ,
            "categories"        => "category"          ,
            "tags"              => "tags"              ,
            "collections"       => "collections"       ,
            "published_blomming"=> "published"         ,
        );
        
        if ($cardinality != null) {
            $attributesToSelect = array_merge( array("sku_group" => "sku_group") , $attributesToSelect);
            $newField = array();
            foreach ($cardinality as $field) {
                $newField[$field] = $field;                
            }
            $attributesToSelect = array_merge($attributesToSelect, $newField );
        }
        
        $this->setVar('_only_specified', true);
        $this->setVar('map', $attributesToSelect);
        
        parent::map();
    }
    
}
