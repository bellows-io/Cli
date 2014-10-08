<?php

namespace Cli\Gui\Borderset;

class SingleBorder extends Borderset {

	public function __construct() {

		parent::__construct(
			'┌',
			'┐',
			'└',
			'┘',
			'─',
			'─',
			'│',
			'│',
			'├',
			'┤',
			'┴',
			'┬',
			'─',
			'│',
			'┼',
			'├',
			'┴',
			'┤',
			'┬');

	}

}