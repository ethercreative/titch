<?php

namespace Craft;

class TitchController extends BaseController
{
	public function actionProcessImages ()
	{
		craft()->titch->startTask();
	}
}