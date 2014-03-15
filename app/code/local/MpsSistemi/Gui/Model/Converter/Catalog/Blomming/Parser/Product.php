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

class MpsSistemi_Gui_Model_Converter_Catalog_Blomming_Parser_Product
    extends Mage_Catalog_Model_Convert_Parser_Product
{
    
    protected $_myAttributeException = array('final_price');
    
    protected $_cardinality = null;


    /**
     * Override funzione standard
     * Unparse (prepare data) loaded products
     *
     * @return Mage_Catalog_Model_Convert_Parser_Product
     */
    public function unparse()
    {
        $entityIds = $this->getData();

        foreach ($entityIds as $i => $entityId) {
            $product = $this->getProductModel()
                ->setStoreId($this->getStoreId())
                ->load($entityId);
            $this->setProductTypeInstance($product);
            /* @var $product Mage_Catalog_Model_Product */
            
            if ($product->getTypeId() != $this->getVar('filter/type')) {
                
                throw $this->addException(
                    Mage::helper('mpsgui')->__('Invalid type in product ' . $product->getSku()),
                    Varien_Convert_Exception::FATAL
                );
            }
            
            // Simulo il calcolo del catalogo
            // Se lo store è l'admin non funziona
            $product->setFinalPrice($product->getPrice());
            Mage::dispatchEvent('mpsgui_adminhtml_product_get_final_price', array('product' => $product, 'qty' => 1));

            $position = Mage::helper('catalog')->__('Line %d, SKU: %s', ($i+1), $product->getSku());
            $this->setPosition($position);

            $row = array(
                'store'         => $this->getStore()->getCode(),
                'websites'      => '',
                'attribute_set' => $this->getAttributeSetName($product->getEntityTypeId(),
                                        $product->getAttributeSetId()),
                'type'          => $product->getTypeId(),
                'category_ids'  => join(',', $product->getCategoryIds())
            );

            if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
                $websiteCodes = array();
                foreach ($product->getWebsiteIds() as $websiteId) {
                    $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                    $websiteCodes[$websiteCode] = $websiteCode;
                }
                $row['websites'] = join(',', $websiteCodes);
            } else {
                $row['websites'] = $this->getStore()->getWebsite()->getCode();
                if ($this->getVar('url_field')) {
                    $row['url'] = $product->getProductUrl(false);
                }
            }

            foreach ($product->getData() as $field => $value) {
                
                if (in_array($field, $this->_myAttributeException)) {
                    $row[$field] = $value;
                    continue;
                }
                
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }
                
                $attribute = $this->getAttribute($field);

                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }

                $row[$field] = $value;
            }

            
            
            if ($stockItem = $product->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) {
                        continue;
                    }
                    $row[$field] = $value;
                }
            }

            foreach ($this->_imageFields as $field) {
                if (isset($row[$field]) && $row[$field] == 'no_selection') {
                    $row[$field] = null;
                }
            }
            
            //Coreggo la quantità deve essere intera! l'arrotondo per difetto
            $row['qty'] = Zend_Locale_Format::toNumber(floor($row['qty']),array('precision' => 0));
            
            //Correggo i prezzi in formato italiano
            $row['final_price'] = Zend_Locale_Format::toNumber($row['final_price'], array(
                                                                    'locale' => 'it',
                                                                    'precision' => 2));
            $row['price'] = Zend_Locale_Format::toNumber($row['price'], array(
                                                                    'locale' => 'it',
                                                                    'precision' => 2));
            
            
            //Imposto se deve essere pubblicato o meno
            $row['published_blomming'] = ($this->getVar('published')) ? 'yes' : 'no';
            
            //Recupero l'eventuale titolo e descrizione blomming
            $row['name'] = ($product->getData('title_blomming') == '') ? $row['name'] : $product->getData('title_blomming');
            $row['description'] = ($product->getData('description_blomming') == '') ? $row['description'] : $product->getData('description_blomming');
            
            //Shipping_method 
            $row['shipping_profile'] = ($product->getData('shipping_profile_blomming')) == '' ? Mage::getStoreConfig('mpsgui/blomming/product_shipping_profile', $this->getStore()->getId()) : $product->getData('shipping_profile_blomming');
            
            //Categories
            $categories = "";
            $collections = "";
            foreach (explode(',', $row['category_ids']) as $catId) {
                
                $category = "";
                if (strpos(',' . $this->getVar('filter/categories') . ',', ",$catId,") !== false) {
                    $cat = Mage::getModel('catalog/category')->Load($catId);
                    if ($cat->getData('category_blomming') != "") {
                        $category = (($categories != "") ? ";" : "") . $cat->getData('category_blomming');
                    }
                    $collections .= (($collections != "") ? "," : "") . $cat->getName();
                }
                
                $categories .= $category;
            }
            if ($categories == "") {
                $categories = $this->getVar('categories');
            }
            
            if ($categories == "") {
                $categories = Mage::getStoreConfig('mpsgui/blomming/product_category',$this->getStore()->getId());
            }
            
            $row['categories'] = $categories;
            
            $row['collections'] = (Mage::getStoreConfig('mpsgui/blomming/export_collection',$this->getStore()->getId())) ? $collections : '';
            
            //Recupero i tag del prodotto
            $tags = "";
            $model = Mage::getModel('tag/tag')
                ->getResourceCollection()
                ->addPopularity()
                ->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus())
                ->addProductFilter($product->getId())
                ->setFlag('relation', true)
                ->addStoreFilter($this->getStore()->getId())
                ->setActiveFilter()
                ->load();
            if (count($model->getItems()) > 0) {
                
                foreach ($model->getItems() as $tag) {
                    $tags .= (($tags != "") ? "," : "") . $tag->getName();
                }
            }
            $row["tags"] = $tags;
            
            
            // Immagini
            
            $row['img1'] = "";
            $row['img2'] = "";
            $row['img3'] = "";
            
            $i = 0;
            foreach ($product->getMediaGalleryImages() as $image) {
                $i++;
                $row["img$i"] = $image->getUrl();
                if ($i == 3) {
                    break;
                }
            }
            
            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
            
            if ($this->getVar('filter/type') == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                
                $rows = $this->_parseConfigurable($row, $product);
                
                foreach ($rows as $r) {
                    $batchExport = $this->getBatchExportModel()
                    ->setId(null)
                    ->setBatchId($this->getBatchModel()->getId())
                    ->setBatchData($r)
                    ->setStatus(1)
                    ->save();
                }                                
                
            } 
            $product->reset();
        }
        
        if ($this->_cardinality != null) {
            Mage::unregister('_mpsgui_cardinality');
            Mage::register('_mpsgui_cardinality', $this->_cardinality);
        }

        return $this;
    }
    
    // Per ogni configubrabile creo l'elenco dei semplici
    protected function _parseConfigurable($row, $configurable) {
        
        $newRow = array();
        
        $row['sku_group'] = $row['sku'];
        $row['sku'] = '';
        
        $cardinality = $this->_getCardinality($configurable);
        
        $childProducts = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($configurable->getId());

        foreach ($childProducts[0] as $childId) {
            $curRow = $row;
            $simpleProduct = Mage::getModel('catalog/product')->Load($childId);            

            foreach ($cardinality as $field) {
        
                $attribute = $this->getAttribute($field);                
                $value = $simpleProduct->getData($field);                
                if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('catalog')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }
                
                $curRow[$field] = $value;
                
                //Aggiorno la quantity
                $stock = $simpleProduct->getStockItem();
                $curRow['qty'] = $stock->getQty();
                
                //Aggiorno il prezzo @@Todo
                //$row['price'] = $simpleProduct->getPrice();
                //$row['final_price'] = $simpleProduct->getFinalPrice();
            }
            
            $newRow[] = $curRow;
        }
                        
        return $newRow;
    }
    
    protected function _getCardinality($configurable) {

        if ($this->_cardinality == null) {
            $productAttributeOptions = $configurable->getTypeInstance(true)->getConfigurableAttributesAsArray($configurable);
            foreach ($productAttributeOptions as $attr) {
                $this->_cardinality[] = $attr['attribute_code'];
            }
        }
        
        return $this->_cardinality;
            
    }

}
