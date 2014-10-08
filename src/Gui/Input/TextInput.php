<?php

namespace Cli\Gui\TextInput;

use \Cli\Gui\Traits\RowMemoryTrait;

class TextInput {

	protected $terminal;


	protected $focusedFormat;
	protected $blurredFormat;
	protected $value = "";
	protected $caret = 0;

	public function __construct($terminal) {
		$this->terminal = $terminal;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function getValue() {
		return $this->value;
	}

	public function draw() {

	}

	public function focus() {

	}

	public function blur() {

	}

}