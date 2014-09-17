<?php

namespace Cli\Gui;

use \Cli\Gui\Traits\RowMemoryTrait;

class ProgressBar {

	use RowMemoryTrait;

	protected $label;
	protected $elapsedSymbol;
	protected $remainingSymbol;
	protected $elapsedFormats;
	protected $terminal;
	protected $spinOffset;

	public function __construct($label, $elapsedSymbol, $remainingSymbol, array $elapsedFormats, $terminal, $lastRow = null) {

		self::setLabel($label);
		self::setElapsedSymbol($elapsedSymbol);
		self::setRemainingSymbol($remainingSymbol);
		self::setElapsedFormats($elapsedFormats);

		$this->spinOffset = 0;
		$this->terminal = $terminal;
		$this->lastRow = $lastRow;
	}

	public function setLabel($value) {
		$this->label = $value;
	}

	public function setElapsedSymbol($value) {
		$this->elapsedSymbol = $value;
	}

	public function setRemainingSymbol($value) {
		$this->remainingSymbol = $value;
	}

	public function setElapsedFormats($value) {
		$this->elapsedFormats = $value;
	}

	public function spin() {
		$this->rememberRow($this->terminal);
		$width = min($this->terminal->getWidth() - strlen($this->label) + 1, 30);

		$base = str_repeat($this->elapsedSymbol, $width * 2);
		$offset = $this->spinOffset % $width;
		$elapsedStr = substr($base, $width - $offset, $width);

		$this->terminal
			->eraseLine()
			->printf($this->label.' ')
			->format($this->elapsedFormats)
			->printf($elapsedStr)
			->resetFormatting()
			->printf("\n");

		$this->spinOffset++;
	}

	public function update($numerator, $denomenator) {

		$this->rememberRow($this->terminal);

		$width = $this->terminal->getWidth();

		$length = strlen($denomenator);

		$details = sprintf("%${length}d / %{$length}d", $numerator, $denomenator);
		$width -= (strlen($details) + strlen($this->label) + 2);

		$width = min($width, $denomenator);
		$percent = $numerator / $denomenator;

		$elapsed = ceil($percent * $width);

		$elapsedStr = substr(str_repeat($this->elapsedSymbol, $elapsed), -$elapsed);
		$remainingStr = substr(str_repeat($this->remainingSymbol, $width), $elapsed, ($width - $elapsed));

		$this->terminal
			->eraseLine()
			->printf($this->label.' ')
			->format($this->elapsedFormats)
			->printf($elapsedStr)
			->resetFormatting()
			->printf($remainingStr.' '.$details."\n");

	}

	public function message() {
		$this->rememberRow($this->terminal);

		$this->terminal->eraseLine();
		$str = call_user_func_array('sprintf', func_get_args());
		$this->terminal->printf(trim($str)."\n");
	}

}