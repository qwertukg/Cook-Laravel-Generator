<?php namespace cook\templates\bundle\models;

use cook\libraries\Generator;
use cook\libraries\iPartial;
use cook\libraries\Helpers;

class ModelName extends Generator implements iPartial {

	public function fill()
	{
		/* Here come's code for fill token ModelName in tpl file in this folder */

		return $this->arguments;
	}

}