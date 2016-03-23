<?php

namespace Craft;

class TitchService extends BaseApplicationComponent
{
	public function compress (AssetFileModel $asset)
	{
		if ($asset->mimeType == 'image/png' || $asset->mimeType == 'image/jpeg')
		{
			TitchPlugin::log($asset->path);
			TitchPlugin::log($asset->transformSource);
		}
	}
}