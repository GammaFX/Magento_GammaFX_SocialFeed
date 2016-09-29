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
require_once Mage::getBaseDir('lib')."/GammaFX_SocialFeed/tw/twitter.php";
 
class GammaFX_SocialFeed_Block_Twitter extends Mage_Core_Block_Template
{
	/** Max amount of loaded posts
	 * @var int
	 */
	private $limit = 6;

	/**
	 * Set up required scripts and styles
	 * @var array
	 */
	private $assets = [
		'skin_css' => 'fxsocialfeed/css/twitter.css',
	];

	/**
	 * Load scripts before layout compile
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _prepareLayout() {
		if ($this->assets) {
			if ($head = $this->getLayout()->getBlock('head')) {
				// Load all assetes to the head block
				foreach ($this->assets as $type => $filePath) {
					$head->addItem($type, $filePath);
				}
			}
		}
		return parent::_prepareLayout();
	}

	public function setLimit($limit) {
		$this->limit = (int) $limit;

		return $this;
	}

	/**
	 * Set template before HTML compile
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _beforeToHtml(){
		$this->setTemplate('fxsocialfeed/twitter/feed.phtml');
		return parent::_beforeToHtml();
	}

	/**
	* Set the twitter credentials 
	*/	
	public function initTW(){
		
		$config = array(
			'oauth_access_token' => trim(Mage::getStoreConfig('fxsocialfeed/tw_socialfeed/tw_access_token')),
			'oauth_access_token_secret' => trim(Mage::getStoreConfig('fxsocialfeed/tw_socialfeed/tw_access_token_secret')),
			'consumer_key' => trim(Mage::getStoreConfig('fxsocialfeed/tw_socialfeed/tw_consumer_key')),
			'consumer_secret' => trim(Mage::getStoreConfig('fxsocialfeed/tw_socialfeed/tw_consumer_secret')),
		);
		$sc_name = trim(Mage::getStoreConfig('fxsocialfeed/tw_socialfeed/tw_screen_name'));
		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$getfield = '?screen_name='.$sc_name . '&include_rts=false&count=' . $this->limit;
		$requestMethod = 'GET';
		$twitter = new TwitterAPIExchange($config);
		$response = $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();
		if($config['oauth_access_token'] != '' && $config['oauth_access_token_secret'] != '' && $config['consumer_key'] != '' && $config['consumer_secret'] != '' && $sc_name != ''){
			return $response;
		}
	}
}
