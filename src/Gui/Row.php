<?php

namespace BitMoth\Cli\Gui;

class Row {

	protected $row;

	public function __construct($terminal, $row = null) {
		$this->terminal = $terminal;
		$this->row = $row;
	}

	public function printf($format) {
		if (is_null($this->row)) {
			$this->terminal->printf('');
			list($this->row) = $this->terminal->getPosition();
		} else {
			$this->terminal->setPosition($this->row, 0);
		}

		$args = func_get_args();
		$args[0] = trim($format, "\n")."\n";

		$this->terminal->eraseLine();

		call_user_func_array([$this->terminal, 'printf'], $args);
	}

}