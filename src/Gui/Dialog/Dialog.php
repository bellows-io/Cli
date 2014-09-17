<?php

namespace Cli\Gui\Dialog;

use \Cli\Gui\Traits\RowMemoryTrait;

class Dialog {

	const SIZE_FULL = 0;
	const SIZE_FIXED = 1;

	public static $borderSetSingle = ['┌','─', '┐', '│', '┘', '─', '└', '│', ' ', '├', '─', '┤'];
	public static $borderSetDouble = ['╔','═', '╗', '║', '╝', '═', '╚', '║', ' ', '╟', '─', '╢	'];

	protected $padding = 1;
	protected $margin = 1;

	protected $sizeMode = 0;

	protected $borderSet ="X.";
	protected $borderFormats = [];
	protected $shadowString = "`";
	protected $shadowFormats = [];

	protected $height = null;
	protected $width = null;

	protected $calculated = null;

	protected $x = 1;
	protected $y = 1;

	protected $title = "";
	protected $contents = "";

	protected $terminal;

	public function __construct($terminal) {
		$this->terminal = $terminal;
		$this->borderSet = self::$borderSetDouble;
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

		$topStr    = $this->makeLineFromBorder(0, 1, 2, $width);
		$bottomStr = $this->makeLineFromBorder(6, 5, 4, $width);
		$midStr    = $this->makeLineFromBorder(7, 8, 3, $width);

		$this->terminal
			->setPosition($startX, $startY)
			->printf($topStr)
			->setPosition($startX, $startY + $height - 1)
			->printf($bottomStr);


		for ($y = 1; $y < $height - 1; $y++) {
			$this->terminal->setPosition($startX, $startY + $y)->printf($midStr);
		}

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
				$this->terminal->setPosition($innerX, $innerY + $i)->printf($lines[$i]);
			}
		}
	}

	protected function makeLineFromBorder($leftIndex, $middleIndex, $rightIndex, $width) {
		$left = $this->borderSet[$leftIndex];
		$right = $this->borderSet[$rightIndex];
		$middle = $this->borderSet[$middleIndex];
		return $left.str_repeat($middle, $width - 2).$right;
	}
}