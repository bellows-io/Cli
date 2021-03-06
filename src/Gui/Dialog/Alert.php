<?php

namespace Cli\Gui\Dialog;

use \Cli\Gui\Traits\RowMemoryTrait;
use \Cli\Gui\Borderset\Borderset;

class Alert extends Dialog {

	protected $options;
	protected $highlightFormat = [];

	public function __construct($terminal, $title, $contents, array $options, Borderset $borderset) {

		parent::__construct($terminal, $borderset);
		$this->title = $title;
		$this->contents = $contents;
		$o = [];
		foreach ($options as $i => $option) {
			$o[] = [$i, $option];
		}
		$this->options = $o;
		$this->textAlign = self::TEXT_ALIGN_CENTER;

	}

	protected function updateLayout() {
		parent::updateLayout();
		$this->calculated[5] -= 2;
	}

	public function setHighlightFormat($formats) {
		$this->highlightFormat = $formats;
	}

	public function show() {
		parent::show();

		$option = 0;
		$this->drawButtons($option);
		$this->terminal->readInput(function($char, $isControl) use (&$option) {
			if ($char == '[C') {
				$option = min($option + 1, count($this->options) - 1);
				$this->drawButtons($option);
			} else if ($char == '[D') {
				$option = max(0, $option - 1);
				$this->drawButtons($option);
			} else if ($char == "\n") {
				return true;
			}
		});

		$this->terminal->resetFormatting();
		return $this->options[$option][0];
	}

	public function drawButtons($selected) {
		list($startX, $startY, $width, $height, $innerWidth, $innerHeight) = $this->calculated;
		$top = $this->makeLine(
			$this->borderSet->getLeftIntersect(),
			$this->borderSet->getInnerHorizontalEdge(),
			$this->borderSet->getRightIntersect(), $width);

		$this->terminal
			->setPosition($startX, $startY + $height - 3)
			->format($this->borderFormats)
			->printf($top);


		$options = array_map('end', $this->options);
		$length = strlen(join(' | ', $options)) + 2;

		$x = $startX + $width - $length - 1;
		$this->terminal->setPosition($x, $startY + $height - 2);
		foreach ($options as $i => $option) {
			if ($i > 0) {
				$this->terminal->printf('|');
			}

			$string = ' ' . $option . ' ';
			if ($i == $selected) {
				$this->terminal
					->resetFormatting()
					->format($this->highlightFormat)
					->printf($string)
					->format($this->borderFormats);
			} else {
				$this->terminal->printf($string);
			}
			$x += strlen($string) + 1;


		}

		$this->terminal->setPosition(0, $startY + $height + 1);

	}

}
