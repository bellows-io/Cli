<?php

namespace Cli\Gui;

class Row extends Element {

	protected $terminal;
	protected $lastRow;

	public function __construct($terminal, $lastRow = null) {
		$this->terminal = $terminal;
		$this->lastRow = $lastRow;
	}

	public function printf($format) {
		$this->rememberRow();

		$this->terminal->eraseLine();
		$str = call_user_func_array('sprintf', func_get_args());
		$this->terminal->printf(trim($str)."\n");
	}

	protected function rememberRow() {
		if (is_null($this->lastRow)) {
			$this->terminal->printf('');
			list($_, $this->lastRow) = $this->terminal->getPosition();
		} else {
			$this->terminal->setPosition(0, $this->lastRow);
		}
	}

}