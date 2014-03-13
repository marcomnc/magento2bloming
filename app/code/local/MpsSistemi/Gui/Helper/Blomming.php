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

class MpsSistemi_Gui_Helper_Blomming extends MpsSistemi_Gui_Helper_Data {
    
    
    /**
     * Normalizzo il filtro prodottiper blomming
     * @param array $filter
     */
    public function NomalizePropductFilter($filter) {
        
        $fCat = '';
        if (isset($filter['filter']['category'])) {
            $fCat = implode(',', $filter['filter']['category']);            
        }
        
        $filter['filter']['category'] = $fCat;
     
        return $filter;
    }
    
}
