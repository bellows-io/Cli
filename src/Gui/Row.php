<?php

namespace Cli\Gui;

use \Cli\Gui\Traits\RowMemoryTrait;

class Row {

	use RowMemoryTrait;

	protected $terminal;

	public function __construct($terminal, $lastRow = null) {
		$this->terminal = $terminal;
		$this->lastRow = $lastRow;
	}

	public function printf($format) {

		$this->rememberRow($this->terminal);

		$args = func_get_args();
		$args[0] = trim($format, "\n")."\n";

		$this->terminal->eraseLine();

		call_user_func_array([$this->terminal, 'printf'], $args);
	}

}