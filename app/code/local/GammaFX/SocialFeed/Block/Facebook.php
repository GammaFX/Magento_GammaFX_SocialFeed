<?php
/**
 * GammaFX
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GammaFX.com license that is
 * available through the world-wide-web at this URL:
 * http://www.gammafx.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	GammaFX
 * @package 	GammaFX_SocialFeed
 * @copyright 	Copyright (c) 2012 GammaFX (http://www.gammafx.com/)
 * @license 	http://www.gammafx.com/license-agreement.html
 */
require_once Mage::getBaseDir('lib')."/GammaFX_SocialFeed/fb/facebook.php";

class GammaFX_SocialFeed_Block_Facebook extends Mage_Core_Block_Template
{
	/**
	 * Facebook SDK class
	 * @var null
	 */
	protected $facebook = null;

	/** Max amount of loaded posts
	 * @var int
	 */
	private $limit = 6;

	/**
	 * Set up required scripts and styles
	 * @var array
	 */
	private $assets = [
		'skin_css' => 'fxsocialfeed/css/facebook.css',
	];

	/**
	 * Load scripts before layout compile
	 * @return Mage_Core_Block_Abstract
	 */
    protected function _prepareLayout() {
	    if ($this->assets) {
		    if ($head = $this->getLayout()->getBlock('head')) {
			    // Load all asstes to the head block
			    foreach ($this->assets as $type => $filePath) {
				    $head->addItem($type, $filePath);
			    }
		    }
	    }
        return parent::_prepareLayout();
    }

	/**
	 * Set template before HTML compile
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _beforeToHtml(){
		$this->setTemplate('fxsocialfeed/facebook/feed.phtml');
		return parent::_beforeToHtml();
	}

	/**
	 * Set max items for query
	 * @param int $limit
	 *
	 * @return $this
	 */
	public function setLimit($limit) {
		$this->limit = (int) $limit;

		return $this;
	}

	/**
	 * Get max items
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

    /**
     * Set the facebook credentials
     */
    public function initFB(){
	    if (!$this->facebook) {
		    $config               = array();
		    $pageid               = trim(
			    Mage::getStoreConfig('fxsocialfeed/fb_socialfeed/fb_page_id')
		    );
		    $config['appId']      = trim(
			    Mage::getStoreConfig('fxsocialfeed/fb_socialfeed/fb_app_id')
		    );
		    $config['secret']     = trim(
			    Mage::getStoreConfig('fxsocialfeed/fb_socialfeed/fb_secret_key')
		    );
		    $config['fileUpload'] = true;
		    if ($config['appId'] != '' && $config['secret'] != ''
			    && $pageid != ''
		    ) {
			    $this->facebook = new Facebook($config);
		    }
	    }

	    return $this->facebook;
    }

	/**
	 * Return ID of facebook page (generate by service http://findmyfbid.com/)
	 * @author Bohdan Slobodian
	 * @return string
	 */
	public function getFBPageId()
	{
		return trim(Mage::getStoreConfig('fxsocialfeed/fb_socialfeed/fb_page_id'));
	}

	/**
	 * @author Bohdan Slobodian
	 * @param array $fields - needle fields (read facebook documentation)
	 * @return array
	 */
	public function getFBPageData(array $fields = ['about', 'id', 'name', 'picture', 'link'])
	{
		if (!$this->facebook) {
			$this->initFB();
		}

		return $this->facebook->api('/' . $this->getFBPageId() . '?fields=' . implode(',', $fields));
	}

	/**
	 * Get facebook posts
	 * @param array $fields
	 *
	 * @return mixed
	 */
	public function getFBFeedData(array $fields = ['picture', 'id', 'message', 'full_picture', 'source', 'created_time', 'comments.limit(1).summary(true)', 'likes.limit(1).summary(true)'])
	{
		if (!$this->facebook) {
			$this->initFB();
		}

		return $this->facebook->api('/' . $this->getFBPageId() . '/posts?limit=' . $this->limit . '&fields=' . implode(',', $fields));
	}
}
