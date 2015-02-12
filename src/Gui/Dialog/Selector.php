<?php

namespace Cli\Gui\Dialog;

use \Cli\Gui\Borderset\Borderset;
use \Cli\Terminal\Ansi;

class Selector extends Dialog {

	protected $data;
	protected $index;
	protected $title;

	public function __construct($terminal, $title, $data, Borderset $borderset) {

		parent::__construct($terminal, $borderset);


		$this->selectedKey = null;
		$this->title = $title;
		$this->data = $data;

	}

	public function show() {
		$this->eraseScreen();
		$this->updateLayout();

		$this->drawBox();
		$this->drawTitle();
		$this->drawShadow();

		$this->drawContents();
		$this->eraseScreen();
		$this->terminal->setPosition(1, 1);
		return $this->selectedKey;
	}

	protected function drawPrompt($input) {
		$width = $this->calculated[4];
		$prompt = "Filter: ";
		$this->terminal
			->setPosition(4, 3)
			->printf($prompt)
			->format([Ansi::CYAN, Ansi::BLACK_BG])
			->printf($input.str_repeat(" ", $width - strlen($input) - strlen($prompt)))
			->resetFormatting();
	}

	protected function drawSelection($subset) {
		list($startX, $startY, $width, $height) = $this->calculated;
		$j = 0;
		foreach ($subset as $j => $key) {
			$this->terminal->setPosition(4, $j + 5);
			$row = sprintf("%s (%s)", $this->data[$key], $key);
			$line = $row.str_repeat(" ", $width - strlen($row) - 4);
			if ($key == $this->selectedKey) {
				$this->terminal
					->format([Ansi::WHITE_BG, Ansi::BLACK])
					->printf($line)
					->resetFormatting();
			} else {
				$this->terminal->printf($line);
			}
		}
		for ($j = 5 + count($subset); $j <= $height; $j++) {
			$this->terminal
				->setPosition(3, $j)
				->printf(str_repeat(" ", $width - 3));

		}
	}

	protected function drawContents() {

		$this->drawPrompt("");
		//$this->drawSelection();

		$textBuffer = '';
		$onInput = function($char, $isControl) use (&$textBuffer) {
			list($startX, $startY, $width, $height) = $this->calculated;
			$resultCount = $height - 4;
			if (! $isControl) {
				$ord = ord($char);
				if ($ord == 127) {
					$textBuffer = substr($textBuffer, 0, strlen($textBuffer) - 1);
				} else if ($ord >= 32 && $ord < 127) {
					$textBuffer .= $char;
				} else if ($char == "\n") {
					return true;
				}
			}

			$this->drawPrompt($textBuffer);

			$subset = [];
			foreach ($this->data as $key => $row) {
				if (! $textBuffer || strpos(strtolower($row), strtolower($textBuffer)) !== false) {
					$subset[] = $key;
					if (count($subset) >= $resultCount) {
						break;
					}
				}
			}
			if ($this->selectedKey && array_search($this->selectedKey, $subset) === false) {
				$this->selectedKey = null;
			}

			if ($isControl) {

				if ($char == "[B") {
					if ($this->selectedKey) {
						$selectedIndex = (array_search($this->selectedKey, $subset) + 1) % count($subset);
						$this->selectedKey = $subset[$selectedIndex];
					} else {
						$this->selectedKey = reset($subset);
					}
				} else if ($char == "[A") {
					if ($this->selectedKey) {
						$selectedIndex = (array_search($this->selectedKey, $subset) - 1 + count($subset)) % count($subset);
						$this->selectedKey = $subset[$selectedIndex];
					} else {
						$this->selectedKey = end($subset);
					}
				}
			}
			if (count($subset) == 1) {
				$this->selectedKey = reset($subset);
			}

			$this->drawSelection($subset);
		};

		$onInput('', false);

		$this->terminal->readInput($onInput);

	}


}