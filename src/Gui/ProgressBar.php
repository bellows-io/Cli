<?php

namespace Cli\Gui;

use \Cli\Gui\Traits\RowMemoryTrait;

class ProgressBar extends Row {

	protected $label;
	protected $elapsedSymbol;
	protected $remainingSymbol;
	protected $elapsedFormats;
	protected $terminal;
	protected $spinOffset;
	protected $animationFrame;

	public function __construct($label, $elapsedSymbol, $remainingSymbol, array $elapsedFormats, $terminal, $lastRow = null) {

		parent::__construct($terminal);

		self::setLabel($label);
		self::setElapsedSymbol($elapsedSymbol);
		self::setRemainingSymbol($remainingSymbol);
		self::setElapsedFormats($elapsedFormats);

		$this->spinOffset = 0;
		$this->animationFrame = 0;
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
		$this->rememberRow();
		$width = min($this->terminal->getWidth() - strlen($this->label) + 1, 30);

		$elapsedSymbol = $this->getElapsedSymbol();
		$base = str_repeat($elapsedSymbol, $width * 2);
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

		$this->rememberRow();

		$width = $this->terminal->getWidth();

		$length = strlen($denomenator);

		$details = sprintf("%${length}d / %{$length}d", $numerator, $denomenator);
		$width -= (strlen($details) + strlen($this->label) + 2);

		$width = min($width, $denomenator);
		$percent = $numerator / $denomenator;

		$elapsed = ceil($percent * $width);
		$elapsedSymbol = $this->getElapsedSymbol();

		$elapsedStr = substr(str_repeat($elapsedSymbol, $elapsed), -$elapsed);
		$remainingStr = substr(str_repeat($this->remainingSymbol, $width), $elapsed, ($width - $elapsed));

		$this->terminal
			->eraseLine()
			->printf($this->label.' ')
			->format($this->elapsedFormats)
			->printf($elapsedStr)
			->resetFormatting()
			->printf($remainingStr.' '.$details."\n");



	}

	protected function getElapsedSymbol() {
		if (is_array($this->elapsedSymbol)) {
			$this->animationFrame = ($this->animationFrame + 1) % count($this->elapsedSymbol);
			return $this->elapsedSymbol[$this->animationFrame];
		}
		return $this->elapsedSymbol;
	}

}