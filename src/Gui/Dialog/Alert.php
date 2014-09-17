<?php

namespace Cli\Gui\Dialog;

use \Cli\Gui\Traits\RowMemoryTrait;

class Alert extends Dialog {

	protected $options;
	protected $highlightFormat;

	public function __construct($terminal, $title, $contents, array $options) {

		parent::__construct($terminal);
		$this->title = $title;
		$this->contents = $contents;
		$this->options = $options;

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
		$this->terminal->readInput(function($char) use (&$option) {
			if ($char == 'C') {
				$option = min($option + 1, count($this->options) - 1);
				$this->drawButtons($option);
			} else if ($char == 'D') {
				$option = max(0, $option - 1);
				$this->drawButtons($option);
			} else if ($char == "\n") {
				return true;
			}
		});

		$this->terminal->resetFormatting();
		return $option;
	}

	public function drawButtons($selected) {
		list($startX, $startY, $width, $height, $innerWidth, $innerHeight) = $this->calculated;
		$top = $this->makeLineFromBorder(9, 10, 11, $width);

		$this->terminal
			->setPosition($startX, $startY + $height - 3)
			->format($this->borderFormats)
			->printf($top);


		$length = strlen(join(' | ', $this->options)) + 2;

		$x = $startX + $width - $length - 1;
		$this->terminal->setPosition($x, $startY + $height - 2);
		foreach ($this->options as $i => $option) {
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
