<?php

namespace Cli\Gui\Traits;

trait RowMemoryTrait {

	protected $lastRow = null;

	protected function rememberRow($terminal) {
		if (is_null($this->lastRow)) {
			$terminal->printf('');
			list($_, $this->lastRow) = $terminal->getPosition();
		} else {
			$terminal->setPosition(0, $this->lastRow);
		}
	}

}