<?php

namespace Craft;

/**
 * SEO for Craft CMS
 *
 * @author    Ether Creative <hello@ethercreative.co.uk>
 * @copyright Copyright (c) 2016, Ether Creative
 * @license   http://ether.mit-license.org/
 * @since     1.0
 */
class TitchPlugin extends BasePlugin {

	public function getName()
	{
		return 'Titch';
	}

	public function getDescription()
	{
		return 'Compresses images using the TinyPNG API';
	}

	public function getVersion()
	{
		return '1.0.0';
	}

	public function getSchemaVersion()
	{
		return '0.0.1';
	}

	public function getDeveloper()
	{
		return 'Ether Creative';
	}

	public function getDeveloperUrl()
	{
		return 'http://ethercreative.co.uk';
	}

	public function getReleaseFeedUrl()
	{
		return 'https://raw.githubusercontent.com/ethercreative/titch/master/releases.json';
	}

	protected function getSettingsModel()
	{
		return new Titch_SettingsModel();
	}

	public function getSettingsHtml()
	{
		return craft()->templates->render('titch/settings', array(
			'settings' => $this->getSettings()
		));
	}

	public function init()
	{
		require_once("vendor/autoload.php");

		if ($this->getSettings()->apiKey) {
			craft()->on('content.onSaveContent', function(Event $event) {
				craft()->titch->startTask();
			});
		}
	}

}