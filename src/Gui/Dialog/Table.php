<?php

namespace Cli\Gui\Dialog;

use \Cli\Gui\Borderset\Borderset;
use \Cli\Terminal\Ansi;

class Table extends Dialog {

	protected $headers;
	protected $rowTemplate;
	protected $rowLengths = array();
	protected $data;
	protected $index;

	public function __construct($terminal, $title, $data, $headers, Borderset $borderset) {

		parent::__construct($terminal, $borderset);

		$this->title = $title;
		$this->data = $data;
		$this->headers = $headers;
		$this->index = 0;

		$templates = [];
		foreach ($headers as $key => $header) {
			$this->rowLengths[$key] = max(
				strlen($header),
				call_user_func_array('max', array_map(
					function($row) use ($key) {
						return strlen($row[$key]);
					},
					$data
				))
			);
			$templates[] = "%-".$this->rowLengths[$key]."s";
		}

		$this->rowTemplate = " ".implode(" │ ", $templates)." ";
	}

	protected function drawTableBody() {
		$height = $this->getBodyHeight();
		$width = $this->terminal->getWidth();
		$subset = array_slice($this->data, $this->index, $height);

		for ($y = 4; $y <= $height + 3; $y++) {
			$this->eraseLine($y, $width - 5);
		}
		$y = 4;
		foreach ($subset as $row) {
			$args = [];
			foreach ($this->headers as $key => $header) {
				$args[] = $row[$key];
			}

			$this->terminal
				->setPosition(3, $y++)
				->printf(vsprintf($this->rowTemplate, $args));
		}
		$this->terminal
			->setPosition(3, 4 + $height)
			->format([Ansi::RED_BG, Ansi::WHITE])
			->printf("Press `q` to quit")
			->resetFormatting();
	}

	protected function getBodyHeight() {
		return $this->terminal->getHeight() - 7;
	}

	protected function eraseLine($line, $width) {
		$this->terminal->setPosition(3, $line)
			->printf(str_repeat(" ", $width));
	}

	protected function drawTableHeader() {
		$this->eraseLine(3, $this->terminal->getWidth() - 5);
		$this->terminal
			->setPosition(3, 3)
			//->eraseLine()
			->format([Ansi::CYAN_BG, Ansi::WHITE])
			->printf(vsprintf($this->rowTemplate, $this->headers))
			->resetFormatting();
	}

	public function show() {
		$this->eraseScreen();

		$this->updateLayout();

		$this->drawBox();
		$this->drawTitle();
		$this->drawShadow();

		$this->drawContents();
	}

	protected function drawContents() {

		$this->drawTableHeader();
		$this->drawTableBody();
		$this->terminal->readInput(function($char, $isControl) {
			if ($isControl) {
				$height = $this->getBodyHeight();
				if ($char == '[A') {
					$this->index -= $height;
				} else if ($char == '[B') {
					$this->index += $height;
				}
				$this->index = max(0, min($this->index, count($this->data) - 1));
			} else if ($char == 'q') {
				return true;
			}
			$this->drawTableBody();
			$this->terminal->setPosition(1, 1);
		});

		$this->eraseScreen();

	}

}