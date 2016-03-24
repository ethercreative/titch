<?php

namespace Craft;

class Titch_ProcessImagesTask extends BaseTask
{
	public function getDescription()
	{
		return 'Optimizing Images';
	}

	public function getTotalSteps()
	{
		return 1;
	}

	public function runStep($step)
	{
		craft()->titch->startOptimization();
		return true;
	}
}