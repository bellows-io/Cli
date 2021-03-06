<?php

namespace Cli\Gui\Dialog;

use \Cli\Gui\Traits\RowMemoryTrait;
use \Cli\Gui\Element\Element;
use \Cli\Gui\Borderset\Borderset;

class Dialog extends Element {

	const SIZE_FULL = 0;
	const SIZE_FIXED = 1;

	const TEXT_ALIGN_LEFT = 0;
	const TEXT_ALIGN_CENTER = 1;
	const TEXT_ALIGN_RIGHT = 2;

	protected $padding = 1;
	protected $margin = 1;

	protected $sizeMode = 0;

	protected $borderSet ="X.";
	protected $borderFormats = [];
	protected $shadowString = "`";
	protected $shadowFormats = [];

	protected $textAlign = 0;

	protected $height = null;
	protected $width = null;

	protected $calculated = null;

	protected $x = 1;
	protected $y = 1;

	protected $title = "";
	protected $contents = "";

	public function __construct($terminal, Borderset $borderSet) {
		parent::__construct($terminal);

		$this->borderSet = $borderSet;
	}

	public function setPosition($x, $y) {
		$this->x = $x;
		$this->y = $y;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function setSize($width, $height) {
		$this->width = $width;
		$this->height = $height;
	}

	public function setSizeMode($mode) {
		$this->sizeMode = $mode;
	}

	public function setBorderFormats($formats) {
		$this->borderFormats = $formats;
	}

	public function setShadowFormats($formats) {
		$this->shadowFormats = $formats;
	}

	public function setShadowString($string) {
		$this->shadowString = $string;
	}

	protected function updateLayout() {

		$startX = $this->x;
		$startY = $this->y;
		if ($this->sizeMode == self::SIZE_FIXED) {
			$width = $this->width;
			$height = $this->height;
		} else {
			$startX += $this->margin;
			$startY += $this->margin;
			$width = 1+ $this->terminal->getWidth() - $startX - (2 * $this->margin);
			$height = 1 +$this->terminal->getHeight() - $startY - (2 * $this->margin);
		}


		$innerWidth = ($width - $this->padding * 2) - 2;
		$innerHeight = ($height - $this->padding * 2) - 2;

		$this->calculated = [$startX, $startY, $width, $height, $innerWidth, $innerHeight];

	}

	public function show() {
		$this->updateLayout();

		$this->drawBox();
		$this->drawTitle();
		$this->drawContents();
		$this->drawShadow();
	}

	protected function drawBox() {
		list($startX, $startY, $width, $height) = $this->calculated;

		$this->terminal
			->resetFormatting()
			->format($this->borderFormats);

		$topStr = $this->makeLine(
			$this->borderSet->getTopLeftCorner(),
			$this->borderSet->getTopEdge(),
			$this->borderSet->getTopRightCorner(), $width);

		$bottomStr = $this->makeLine(
			$this->borderSet->getBottomLeftCorner(),
			$this->borderSet->getBottomEdge(),
			$this->borderSet->getBottomRightCorner(), $width);

		$midStr = $this->makeLine(
			$this->borderSet->getLeftEdge(), ' ',
			$this->borderSet->getRightEdge(), $width);

		$this->terminal
			->setPosition($startX, $startY)
			->printf($topStr)
			->setPosition($startX, $startY + $height - 1)
			->printf($bottomStr);


		for ($y = 1; $y < $height - 1; $y++) {
			$this->terminal->setPosition($startX, $startY + $y)->printf($midStr);
		}
	}

	protected function eraseScreen() {
		$height = $this->terminal->getHeight();
		for ($i = 1; $i <= $height; $i++) {
			$this->terminal->setPosition(1, $i)->eraseLine();
		}
		$this->terminal->setPosition(1, 1);
	}

	protected function drawShadow() {
		if ($this->shadowString) {

			list($startX, $startY, $width, $height) = $this->calculated;

			$this->terminal
				->resetFormatting()
				->format($this->shadowFormats);

			$r = ceil($width / strlen($this->shadowString));
			$fullString = substr(str_repeat($this->shadowString, $r), 0, $width + 1);

			$this->terminal->setPosition($startX +1, $startY + $height)->printf($fullString);

			$r = ceil(($height) / strlen($this->shadowString));
			$fullString = substr(str_repeat($this->shadowString, $r), 0, $height);
			for ($y = 0; $y < $height; $y++) {
				$this->terminal->setPosition($startX + $width, $y + $startY + 1)->printf($fullString[$y]);
			}
		}
	}

	protected function drawTitle() {
		if ($this->title) {
			list($startX, $startY, $width, $height) = $this->calculated;

			$len = strlen($this->title) + 2;
			$sides = floor(($width - $len) / 2);

			$this->terminal
				->setPosition($startX + $sides, $startY)
				->printf(' '.$this->title.' ');
		}
	}

	protected function drawContents() {

		if ($this->contents) {
			list($startX, $startY, $width, $height, $innerWidth, $innerHeight) = $this->calculated;


			$innerX = $startX + 1 + $this->padding;
			$innerY = $startY + 1 + $this->padding;

			$lines = explode("\n", wordwrap($this->contents, $innerWidth));
			if (count($lines) > $innerY) {
				$lines = array_slice($lines, 0, $innerY);
				$last = array_pop($lines);
				$lines[] = substr($last, 0, strlen($last) - 3).'...';
			}

			foreach ($lines as $i => $line) {
				$p = '';
				if ($this->textAlign == self::TEXT_ALIGN_RIGHT) {
					$w = floor($innerWidth - strlen($line));
					$line = str_repeat(' ', $w).$line;
				} else if ($this->textAlign == self::TEXT_ALIGN_CENTER) {
					$w = floor(($innerWidth - strlen($line)) / 2);
					$line = str_repeat(' ', $w).$line;
				}
				$this->terminal->setPosition($innerX, $innerY + $i)->printf($p.$line);
			}
		}
	}

	protected function makeLine($left, $middle, $right, $width) {
		return $left.str_repeat($middle, $width - 2).$right;
	}
}