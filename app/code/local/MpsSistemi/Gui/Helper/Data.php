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

class MpsSistemi_Gui_Helper_Data extends Mage_Core_Helper_Abstract {
    
    
    /**
     * Recupera un configurazione per blomming relativa all'esportazione prodotti
     * @param type $key
     * @return type
     */
    public function getBlommingProductConfig ($key) {

        return $this->getBlommingConfig("product_$key");
        
    }
    
    /**
     * Recupera un configurazione per blomming
     * @param type $key
     * @return type
     */
    public function getBlommingConfig($key) {
        
        return $this->getConfig("blomming/$key");
        
    }
    
    /**
     * Recupero la configurazione
     * @param type $key
     * @return type
     */
    public function getConfig($key) {
        $keyComplete = "mpsgui/$key";
        return Mage::getStoreConfig($keyComplete);
    }
    
    
    public function NormalizeGuiData($data) {
        
        $newData = array();
        
        foreach ($data as $k => $v) {
            
            switch ($k) {
                case "product_blomming":
                    $newData[$k] = Mage::Helper('mpsgui/blomming')->NomalizeProductFilter($v);
                    break;
                default:
                    $newData[$k] = $v;
                        
            }
            
        }

        return $newData;
        
    }
    
    public function ValidateGuiData($data) {
        
        $validate = "";
        
        foreach ($data as $k => $v) {
            
            switch ($k) {
                case "product_blomming":
                    
                    $validate .= Mage::Helper('mpsgui/blomming')->ValidateProductFilter($v);
                    break;
                default:
                    $validate .= "";
                        
            }
            
        }

        return $validate;
        
    }
    
}
