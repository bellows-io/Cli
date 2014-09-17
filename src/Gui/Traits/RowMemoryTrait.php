<?php

namespace Cli\Gui\Traits;

trait RowMemoryTrait {

	protected $lastRow = null;

	protected function rememberRow($terminal) {
		if (is_null($this->lastRow)) {
			$terminal->printf('');
			list($this->lastRow) = $terminal->getPosition();
		} else {
			$terminal->setPosition($this->lastRow, 0);
		}
	}

}