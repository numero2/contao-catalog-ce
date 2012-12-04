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


class ContentCatalog extends ModuleCatalogReader {

	
	/**
	 * Parse the template
	 * @return string
	 */
	public function generate() {

		$this->catalogTableName = $this->catalog;
		$this->catalogID = $this->getCatalogID();
	
		if( TL_MODE == 'BE' ) {

			$return = '<pre>';

			$aEntry = array();
			$aEntry = $this->getCatalogEntry();

			foreach( $aEntry as $fieldName => $fieldVal ) {

				if( in_array( $fieldName, array('pid','sorting','tstamp') ) )
					continue;

				$return .= $fieldName.': '.$fieldVal."\n";
			}

			$return .= '</pre>';

			return $return;
		}
		
		$this->catalog = $this->catalogID;

		return parent::generate();
	}


	/**
	 * Generate the content element
	 */
	protected function compile() {

		$aEntry = array();
		$aEntry = $this->getCatalogEntry();
	
		// prepare data for "normal" reader module
		$this->strTable = $this->catalogTableName;
		$this->catalog = $this->catalogID;
		$this->catalog_visible = $this->_getCatalogFields();
		$this->catalog_goback_disable = true;
		
		// set alias or id depending on if alias field is defined
		$aliasField = $this->getCatalogAliasField();
		
		if( $this->getCatalogAliasField() )
			$this->Input->setGet( 'items', $aEntry[ $aliasField ] );
		else
			$this->Input->setGet( 'items', $aEntry['id'] );
		
		parent::compile();
	}
	

	/**
	 * Get field from catalog entry
	 */	
	private function getCatalogEntry() {

		$oEntry = NULL;
		$oEntry = $this->Database->prepare(" SELECT * FROM ".$this->catalogTableName." WHERE id = ? ")->limit(1)->execute( $this->catalog_entry );
		
		return $oEntry->fetchAssoc();
	}
	
	
	/**
	 * Get if of selected catalog
	 */	
	private function getCatalogID() {
	
		$oEntry = NULL;
		$oEntry = $this->Database->prepare(" SELECT id FROM `tl_catalog_types` WHERE tableName = ? ")->limit(1)->execute( $this->catalogTableName );
		
		return $oEntry->id;
	}
	
	/**
	 * Check if catalog has alias field
	 */	
	private function getCatalogAliasField() {
	
		$oEntry = NULL;
		$oEntry = $this->Database->prepare(" SELECT aliasField FROM `tl_catalog_types` WHERE tableName = ? ")->limit(1)->execute( $this->catalogTableName );
		
		return $oEntry->aliasField;
	}
	
	
	/**
	 * Get list of all catalog fields available
	 */	
	private function _getCatalogFields() {

		$objFields = NULL;
		$objFields = $this->Database->prepare("SELECT * FROM tl_catalog_fields WHERE pid = ? ORDER BY sorting ASC")->execute( $this->catalogID );
		
		$fields = array();
		
		while( $objFields->next() ) {
			$fields[] = $objFields->colName;
		}

		return $fields;
	}
}

?>