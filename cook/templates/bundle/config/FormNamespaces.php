<?php namespace cook\templates\bundle\config;

use cook\libraries\Generator;
use cook\libraries\iPartial;
use cook\libraries\Helpers;

class FormNamespaces implements iPartial {

	public function __construct($constructor)
	{
		$this->constructor = $constructor;
	}

	public function fill()
	{
		/* Here come's code for fill token FormNamespaces in tpl file in this folder */

		return $this->constructor->name;
	}

}