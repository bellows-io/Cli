<?php

namespace Cli\Gui\Element;

use \Cli\Gui\Traits\RowMemoryTrait;
use \Cli\Terminal\TerminalInterface;

abstract class Element {

	/**
	 * @var TerminalInterface
	 */
	protected $terminal;
	protected $lastRow = null;

	public function __construct(TerminalInterface $terminal) {
		$this->terminal = $terminal;
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