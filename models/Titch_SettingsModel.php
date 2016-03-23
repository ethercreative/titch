<?php

namespace Craft;

class Titch_SettingsModel extends BaseModel {

	protected function defineAttributes()
	{
		return [
			'apiKey' => [ AttributeType::String, 'required' => true ]
		];
	}

	public function rules()
	{
		$rules = parent::rules();
		$rules[] = ['apiKey', 'validateApiKey'];

		return $rules;
	}

	public function validateApiKey ($attribute)
	{
		TitchPlugin::log(print_r($this, true));
		$key = $this->$attribute;

		try {
			\Tinify\setKey($key);
			\Tinify\validate();
		} catch(\Tinify\Exception $e) {
			$this->addError($attribute, $e->getMessage());
		}
	}

}