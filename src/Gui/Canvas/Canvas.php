<?php

namespace Cli\Gui\Canvas;

use Cli\Terminal\TerminalInterface;

class Canvas {

	protected $terminal;
	protected $width;
	protected $height;

	protected $formats = [];
	protected $path = [];
	protected $x = 0;
	protected $y = 0;

	public function __construct(TerminalInterface $terminal, $width, $height) {
		$this->terminal = $terminal;

		$this->height = $height;
		$this->width = $width;
	}

	public function setFormat(array $formats) {
		$this->formats = $formats;
	}

	public function beginPath() {
		$this->paths = [];
	}

	public function moveTo($x, $y) {
		$this->x = $x;
		$this->y = $y;

		$this->paths[] = [$x, $y, false];
	}

	public function lineTo($x, $y) {
		$this->x = $x;
		$this->y = $y;

		$this->paths[] = [$x, $y, true];
	}

	public function closePath() {
		list($x, $y) = $this->paths[0];
		$this->lineTo($x, $y);
	}

	public function drawLine($x1, $y1, $x2, $y2) {

		$points = $this->getLinePoints($x1, $y1, $x2, $y2);
		foreach ($points as $point) {
			$this->terminal->setPosition($point[0], $point[1])->printf(' ');
		}
	}

	protected function getLinePoints($x1, $y1, $x2, $y2) {

		$dx = abs($x2 - $x1);
		$dy = abs($y2 - $y1);
		$sx = $x1 < $x2 ? 1 : -1;
		$sy = $y1 < $y2 ? 1 : -1;
		$err = ($dx > $dy ? $dx : -$dy) / 2;
		$e2;
		$o = [];
		while(true) {
			$o[] = [$x1, $y1];
			if (round($x1) == round($x2) && round($y1) == round($y2)) {
				break;
			}
			$e2 = $err;
			if ($e2 > -$dx) {
				$err -= $dy;
				$x1 += $sx;
			}
			if ($e2 < $dy) {
				$err += $dx;
				$y1 += $sy;
			}
		}
		return $o;
	}

	public function stroke() {
		$points = $this->getPolygonPoints();

		$this->terminal->format($this->formats);
		foreach ($points as $point) {
			$this->terminal->setPosition($point[0], $point[1])->printf(" ");
		}
	}

	public function fill() {
		$points = $this->getPolygonPoints();
		$rows = [];
		foreach ($points as $point) {
			list($x, $y) = $point;
			if (! isset($rows[$y])) {
				$rows[$y] = [];
			}
			$rows[$y][] = $x;
		}
		ksort($rows);
		$rows = array_map(function($row) {
			$row = array_unique($row);
			sort($row);
			return $row;
		}, $rows);

		$this->terminal->format($this->formats);
		foreach ($rows as $y => $xs) {
			$lastX = null;
			foreach ($xs as $i => $x) {
				if ($lastX) {
					if ($i & 1) {
						$s = str_repeat(' ', $x - $lastX);
					}
					$this->terminal->setPosition($lastX, $y)->printf($s);
				}
				$lastX = $x;
			}
		}
	}

	protected function getPolygonPoints() {
		$lastPoint = null;
		$points = [];
		foreach ($this->paths as $point) {
			if ($lastPoint) {
				$linePoints = $this->getLinePoints($lastPoint[0], $lastPoint[1], $point[0], $point[1]);
				foreach ($linePoints as $linePoint) {
					$points[] = $linePoint;
				}
			}
			$lastPoint = $point;
		}

		return $points;
	}

	public function fillRect($x, $y, $width, $height) {
		$this->terminal->format($this->formats);

		$line = str_repeat(' ', $width);
		for ($yy = $y; $yy < $y + $height; $yy++) {
			$this->terminal->setPosition($x, $yy)->printf($line);
		}

	}

	public function strokeRect($x, $y, $width, $height) {
		$this->terminal->format($this->formats);

		$line = str_repeat(' ', $width);
		$this->terminal->setPosition($x, $y)->printf($line);
		$this->terminal->setPosition($x, $y+$height-1)->printf($line);

		for ($yy = $y; $yy < $y + $height; $yy++) {
			$this->terminal->setPosition($x + $width - 1, $yy)->printf(' ');
			$this->terminal->setPosition($x, $yy)->printf(' ');
		}
	}

	public function clearRect($x, $y, $width, $height) {
		$this->terminal->resetFormatting();

		$line = str_repeat(' ', $width);
		for ($yy = $y; $yy < $y + $height; $y++) {
			$this->terminal->setPosition($x, $yy)->printf($line);
		}

	}
}