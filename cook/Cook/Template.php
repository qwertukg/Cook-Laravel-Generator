<?php namespace Cook;

use Laravel\File;
use Exception;
use Symfony\Component\Console\Input\ArgvInput;
use Laravel\Bundle;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Template {

	// Template root folder
	public $root;
	public $path;

	// Constructor container
	public $constructor;

	// Templates list
	public $templates;

	public $name;
	public $newName;
	public $replacerObject;
	public $content;
	public $result;

	public $tokens;
	public $tabs;
	public $replacers;

	public function setConstructor(Constructor $constructor)
	{
		$this->constructor = $constructor;

		$this->checkInput();

		$this->findTemplatesRecursive();

		$this->findReplacerObjects();

		$this->findTokens();

		$this->findReplacers();

		return $this;
	}

	protected function checkInput()
	{
		$input = new ArgvInput;

		$name = $input->getParameterOption('--tpl');

		if ($name === false)
		{
			throw new Exception("continue", 500);
		}

		if ($name === null)
		{
			throw new Exception("Set template name.");
		}

		$this->root = Bundle::path('cook').'Templates'.DS.$name;

		if (!is_dir($this->root))
		{
			throw new Exception("Template $template not found.");
		}
	}

	protected function findTemplatesRecursive()
	{
		$directoryIterator = new RecursiveDirectoryIterator($this->root);
		$recursiveIterator = new RecursiveIteratorIterator($directoryIterator);

		foreach ($recursiveIterator as $file) 
		{
			$info = pathinfo($file);

			if (!is_dir($file) and $info['extension'] == 'tpl')
			{
				$template = new static;

				$template->content = File::get($file);
				$template->name = $info['filename'];
				$template->root = $info['dirname'];

				$this->templates[] = $template;
			}

		}
	}

	protected function findReplacerObjects()
	{
		foreach ($this->templates as $i => $template) 
		{
			$replacerPath = $template->root.DS.$template->name.'.php';
			
			if (File::exists($replacerPath))
			{
				require_once $replacerPath;

				$replacer = $template->name.'_Replacer';

				$template->replacerObject = new $replacer;
			}
		}
	}

	// Get tokens from each template, 
	// and set them to template token proterty as key.
	protected function findTokens()
	{
		foreach ($this->templates as $i => $template) 
		{
			$template->tokens = $this->getTokens($template->content);
			$template->tabs = $this->getTabs($template->content, $template->tokens);
		}
	}

	// Get tokens from string
	protected function getTokens($content)
	{
		$result = array();

		preg_match_all('#\<[A-z]+?\>#', $content, $result);

		return $result[0];
	}

	protected function getTabs($content, $tokens)
	{
		$result = array();

		preg_match_all('#\t*\<[A-z]+?\>#', $content, $result);

		foreach ($result[0] as $i => $line) 
		{
			$result[0][$i] = strspn($line, "\t");
		}

		return $result[0];
	}

	// Get replacers for each template by invoking all replace methods, 
	// and set them to template token proterty as value.
	protected function findReplacers()
	{
		foreach ($this->templates as $i => $template) 
		{
			$template = $this->constructor->findReplacers($template);
		}
	}

}