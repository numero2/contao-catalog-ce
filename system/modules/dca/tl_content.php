<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  numero2 - Agentur für Internetdienstleistungen <www.numero2.de>
 * @author     Benny Born <benny.born@numero2.de>
 * @package    Catalog
 * @license    LGPL
 * @filesource
 */


/**
 * Table tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['catalog'] = '{type_legend},type;{include_legend},catalog,catalog_entry,catalog_template;{protected_legend:hide},protected;{expert_legend:hide},guests,invisible,cssID,space';


$GLOBALS['TL_DCA']['tl_content']['fields']['catalog'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['catalog']
,	'exclude'                 => true
,	'inputType'               => 'select'
,	'options_callback'        => array( 'tl_content_catalog', 'getCatalogs' )
,	'eval'                    => array( 'mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50' )
);

$GLOBALS['TL_DCA']['tl_content']['fields']['catalog_entry'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['catalog_entry']
,	'exclude'                 => true
,	'inputType'               => 'select'
,	'options_callback'        => array( 'tl_content_catalog', 'getCatalogEntries' )
,	'eval'                    => array( 'mandatory'=>true, 'chosen'=>true, 'tl_class'=>'w50' )
);

$GLOBALS['TL_DCA']['tl_content']['fields']['catalog_template'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['catalog_template']
,	'exclude'                 => true
,	'inputType'               => 'select'
,	'options_callback'        => array( 'tl_content_catalog', 'getCatalogTemplates' )
,	'eval'                    => array( 'chosen'=>true, 'tl_class'=>'' )
);


class tl_content_catalog extends Backend {


	public function getCatalogs() {
	
		$oCatalogs = NULL;
		$oCatalogs = $this->Database->prepare(" SELECT name, tableName FROM `tl_catalog_types` ")->execute();
		
		$aCatalogs = array();
		
		while( $oCatalogs->next() ) {
			$aCatalogs[ $oCatalogs->tableName ] = $oCatalogs->name;
		}
		
		return $aCatalogs;
	}
	
	public function getCatalogEntries( DataContainer $dc ) {
	
		if( !$dc->activeRecord->catalog )
			return false;

		$catalogID = NULL;
		$catalogID = $this->Database->prepare(" SELECT id FROM `tl_catalog_types` WHERE tableName = ? ")->limit(1)->execute($dc->activeRecord->catalog)->id;
			
		$oCatalogFields = NULL;
		$oCatalogFields = $this->Database->prepare(" SELECT colName FROM `tl_catalog_fields` WHERE pid = ? AND type = 'text' AND titleField = 1 ORDER BY sorting DESC ")->limit(1)->execute($catalogID);
		
		$catalogTitleCol = NULL;
		$catalogTitleCol = NULL;
			
		$oEntries = NULL;
		$oEntries = $this->Database->prepare(" SELECT id, ".$oCatalogFields->colName." AS title FROM `".$dc->activeRecord->catalog."` ORDER BY id DESC ")->execute();
	
		$aEntries = array();
		
		while( $oEntries->next() ) {
			$aEntries[ $oEntries->id ] = $oEntries->title.' (ID: '.$oEntries->id.')';
		}
	
		return $aEntries;
	}
	
	public function getCatalogTemplates( DataContainer $dc ) {
	
		// fix issue #70 - template selector shall only show relevant templates.
		if( version_compare(VERSION.'.'.BUILD, '2.9.0', '>=') ) {
			return $this->getTemplateGroup('catalog_', $dc->activeRecord->pid);
		} else {
			return $this->getTemplateGroup('catalog_');
		}
	}
}

?>