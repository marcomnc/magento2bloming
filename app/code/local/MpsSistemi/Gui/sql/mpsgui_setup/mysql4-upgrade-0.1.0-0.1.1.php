<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();


/**
 * Creo gli attributi per blomming da attivare eventualmente .....
 *      shipping_metod_blomming spedizione custom per un prodotto
 *      title_blomming 
 *      description_blomming
 */
$installer->addAttribute('catalog_product', 'shipping_profile_blomming',  array(
    'type'     => 'varchar',
    'label'    => 'Shipping Profile per Blomming',
    'input'    => 'text',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => true,
    'default'           => ''
));

$installer->addAttribute('catalog_product', 'title_blomming',  array(
    'type'     => 'varchar',
    'label'    => 'Titolo per Blomming',
    'input'    => 'text',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => true,
    'default'           => ''
));

$installer->addAttribute('catalog_product', 'description_blomming',  array(
    'type'     => 'varchar',
    'label'    => 'Descrizione per Blomming',
    'input'    => 'testarea',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => true,
    'default'           => ''
));


$installer->endSetup();

