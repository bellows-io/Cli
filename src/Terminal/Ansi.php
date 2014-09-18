<?php

namespace Cli\Terminal;

class Ansi {

	protected $stream;
	protected $escape;

	const BLACK = 30;
	const RED = 31;
	const GREEN = 32;
	const YELLOW = 33;
	const BLUE = 34;
	const MAGENTA = 35;
	const CYAN = 36;
	const WHITE = 37;

	const BLACK_BG = 40;
	const RED_BG = 41;
	const GREEN_BG = 42;
	const YELLOW_BG = 43;
	const BLUE_BG = 44;
	const MAGENTA_BG = 45;
	const CYAN_BG = 46;
	const WHITE_BG = 47;

	public function __construct($stream, $escape = "\033") {

		$this->stream = $stream;
		$this->escape = $escape;
	}

	public function setPosition($x = '', $y = '') {
		$this->command("${y};${x}H");
		return $this;
	}

	public function getPosition() {
		list($y, $x) = explode(';', $this->request('6n'));
		return [$x, $y];
	}

	public function getSize() {
		return [$this->getWidth(), $this->getHeight()];
	}

	public function getWidth() {
		return (int)`tput cols`;
	}

	public function getHeight() {
		return (int)`tput lines`;
	}

	public function move($up = 0, $forward = 0) {
		$str = '';
		if ($up > 0) {
			$str .= sprintf("%dA", $up);
		} else if ($up < 0) {
			$str .= sprintf("%dB", - $up);
		}
		if ($forward > 0) {
			$str .= sprintf("%dC", $forward);
		} else if ($forward < 0) {
			$str .= sprintf("%dD", - $forward);
		}

		$this->command($str);
		return $this;
	}

	public function moveUp($count = 1) {
		$this->command(sprintf("%dA", $count));
		return $this;
	}

	public function moveDown($count = 1) {
		$this->command(sprintf("%dB", $count));
		return $this;
	}

	public function moveForward($count = 1) {
		$this->command("{$count}C");
		return $this;
	}

	public function moveBackward($count = 1) {
		$this->command("{$count}D");
		return $this;
	}

	public function eraseEnd() {
		$this->command("K");
		return $this;
	}

	public function eraseStart() {
		$this->command("1K");
		return $this;
	}

	public function eraseLine() {
		$this->command("2K");
		return $this;
	}

	public function eraseDown() {
		$this->command("J");
		return $this;
	}

	public function eraseUp() {
		$this->command("1J");
		return $this;
	}

	public function eraseScreen() {
		$this->command("2J");
		return $this;
	}

	public function save() {
		$this->command("s");
		return $this;
	}

	public function restore() {
		$this->command("u");
		return $this;
	}

	public function format($codes) {
		$this->command(implode(';', $codes).'m');
		return $this;
	}

	public function resetFormatting() {
		$this->command("0m");
		return $this;
	}

	public function printf($format /**, .. $arguments **/ ) {
		$str = call_user_func_array('sprintf', func_get_args());
		fwrite($this->stream, $str);
		return $this;
	}

	public function getStatus() {
		return $this->request('5n');
	}

	protected function command($string) {
		fwrite($this->stream, $this->escape.'['.$string);
	}

	public function readInput($callback) {
		$term = `stty -g`;

		system("stty -icanon -echo");
		$b = '';

		while ($c = fread(STDIN, 1)) {
			$out = $callback($c);
			if ($out === true) {
				break;
			}
		}

		system("stty '".$term."'");

	}

	protected function request($code) {
		$term = `stty -g`;

		$this->save();

		system("stty -icanon");
		$b = '';

		$this->command($code);
		while ($c = fread($this->stream, 1)) {
			if ($c == "R") {
				break;
			} else if ($c != $this->escape) {
				$b .= $c;
			}
		}

		$b = trim($b, "[ ");
		system("stty '".$term."'");

		$l = 4 + strlen($b);

		$this->restore();
		$this->moveBackward($l);
		$this->printf(str_repeat(" ", $l));
		$this->moveBackward($l);

		return $b;
	}
}