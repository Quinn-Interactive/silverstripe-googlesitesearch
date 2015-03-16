<?php

/**
 * @package googlesitesearch
 */
class GoogleSiteSearchPage extends Page {

	public static $db = array(
		'GoogleKey' => 'Varchar(200)',
		'GoogleCX' => 'Varchar(200)',
		'GoogleDomain' => 'Varchar(255)'
	);
	public static $create_default_search_page = true;
	public static $cse_key = '';
	public static $cse_cx = '';

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldsToTab('Root.Content.Main', array(
			new TextField('GoogleKey', 'Google Custom Search Key (sign up at <a href="https://www.google.com/cse/sitesearch/create" target="_blank">google.com/cse</a>)'),
			new TextField('GoogleCX', 'Google Custom Search CX'),
			new TextField('GoogleDomain', 'Domain to search results for (must be public, i.e use live URL for testing)')
		));

		return $fields;
	}
	
	public function requireDefaultRecords() {
		if(self::$create_default_search_page) {
			if (!DataObject::get('GoogleSiteSearchPage')) {
				$search = new GoogleSiteSearchPage();
				$search->Title = "Search results";
				$search->MenuTitle = "Search";
				$search->ShowInMenus = 0;
				$search->GoogleKey = self::$cse_key;
				$search->GoogleCX = self::$cse_cx;
				$search->URLSegment = "search";
				$search->write();

				$search->doPublish('Stage', 'Live');
			}
		}
	}

	/**
	 * @return string
	 */
	public function getCseKey() {
		if($this->GoogleKey) return $this->GoogleKey;
		return '';
	}

	/**
	 * @return string
	 */
	public function getCseCx() {
		if($this->GoogleKey) return $this->GoogleCX;
		return '';
	}
}

/**
 * @package googlesitesearch
 */
class GoogleSiteSearchPage_Controller extends Page_Controller {

	public function init() {
		parent::init();

		Requirements::javascript('https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		Requirements::javascript('googlesitesearch/javascript/uri.js');
		Requirements::javascript('googlesitesearch/javascript/googlesitesearch.js');
		
		Requirements::css('googlesitesearch/css/googlesitesearch.css');

		if(isset($_GET['Search'])) {
			$this->GoogleSiteSearchText = DBField::create(
				'HTMLText', 
				$_GET['Search']
			);
		}
	}
}