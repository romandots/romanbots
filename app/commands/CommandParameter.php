<?php
namespace RomanBots\Commands;

class CommandParameter {
	public $name;
	public $value;
	public $pattern;
	public $title;
	public $description;
	public $required;

	public function validate($value){
		return preg_match("/^{$this->pattern}$/ui", $value);
	}


	public function set($value){
		if($this->validate($value)){
			$this->value = $value;
			return $this;
		} else {
			return false;
		}
	}


}