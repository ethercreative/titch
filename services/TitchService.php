<?php

namespace Craft;

class TitchService extends BaseApplicationComponent
{
	private $_sources;
	
	public function startTask ()
	{
		craft()->tasks->createTask('Titch_ProcessImages');
//		craft()->titch->startOptimization();
//		craft()->end();
	}

	public function startOptimization ()
	{
		$titch = craft()->plugins->getPlugin('titch');
		$settings = $titch->getSettings();
		$key = $settings->apiKey;
		$fromDate = $settings->lastRun ?: DateTimeHelper::fromString('2000-01-01');

		if ($key) {
			try {
				\Tinify\setKey($key);
				\Tinify\validate();
				$continue = true;
			} catch(\Tinify\Exception $e) {
				TitchPlugin::log($e->getMessage(), LogLevel::Error);
				$continue = false;
			}

			if ($continue) {
				$this->_sources = craft()->assetSources->getAllSources('id');
				$this->_findTransformedImages($fromDate);

				$newSettings = $settings->attributes;
				$newSettings['lastRun'] = DateTimeHelper::currentTimeForDb();
				craft()->plugins->savePluginSettings( $titch, $newSettings );
			}
		} else {
			TitchPlugin::log('API Key must be set.', LogLevel::Error);
		}
	}

	private function _findTransformedImages ($fromDate)
	{
		// TODO: Fix this (dateUpdated check should be part of query)
		$images = craft()->db->createCommand()
			->select('*')
			->from('assettransformindex')
//			->where("dateUpdated > " . DateTimeHelper::fromString($fromDate))
//			->where('dateUpdated > :dateUpdated', array(':dateUpdated' => DateTimeHelper::fromString($fromDate)))
			->queryAll();

		foreach ($images as $image)
			if ($image['fileExists'] && DateTimeHelper::fromString($image['dateUpdated']) > DateTimeHelper::fromString($fromDate))
				$this->_compress($image['id'], $image['filename'], $image['location'], $image['sourceId']);
	}

	private function _compress ($id, $name, $folder, $sourceId)
	{
		TitchPlugin::log($name);

		$path = craft()->config->parseEnvironmentString($this->_sources[$sourceId]->settings['path']);
		$path .= $folder . '/' . $name;

		try {
			$source = \Tinify\fromFile($path);
			$source->toFile($path);
			$success = true;
		} catch (\Tinify\Exception $e) {
			TitchPlugin::log($e->getMessage(), LogLevel::Error);
			$success = false;
		}

		if ($success)
			craft()->db->createCommand()->update('assettransformindex', [], 'id = :id', array(':id' => $id));
	}
}