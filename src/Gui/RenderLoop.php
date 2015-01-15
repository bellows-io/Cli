<?php

namespace Cli\Gui;

use Cli\Terminal\Ansi;

class RenderLoop {

	protected $stream;
	protected $escapeCode;
	protected $running = false;
	protected $pollingInterval;

	protected $intervalCallbacks = [];
	protected $keypressCallbacks = [];

	public function __construct($stream, $pollingInterval = 0.05, $escapeCode = "\033") {
		$this->escapeCode = $escapeCode;
		$this->stream = $stream;
		$this->pollingInterval = $pollingInterval;
	}

	public function onInterval($numSeconds, callable $callback) {
		$this->intervalCallbacks[] = [
			'elapsed' => $numSeconds, // initially due for updates
			'numSeconds' => $numSeconds,
			'callback' => $callback
		];
		return $this;
	}

	public function onKeyPress(callable $callback) {
		$this->keypressCallbacks[] = $callback;
		return $this;
	}

	public function run() {
		$microseconds = $this->pollingInterval * 1000000;
		stream_set_blocking($this->stream, false);
		$term = `stty -g`;
		system("stty -icanon -echo");

		$this->running = true;
		$time = microtime(true);
		while ($this->running) {
			$elapsed = (microtime(true) - $time);
			if ($elapsed > $this->pollingInterval) {
				$this->runIntervals($elapsed);
				$time = microtime(true);
			}
			$chars = $this->getUserInputChars($isControl);
			if ($chars) {
				$this->runKeys($chars, $isControl);
			}
			usleep($microseconds);
		}
		system("stty '".$term."'");
		stream_set_blocking($this->stream, true);
	}

	public function stop() {
		$this->running = false;
	}

	protected function getUserInputChars(&$isControl = false) {
		$charFound = false;
		$isControl = false;
		$string = '';
		while (! $charFound) {
			$s = fread($this->stream, 1);
			if (! $s) {
				break;
			}
			if ($s == $this->escapeCode) {
				$isControl = true;
			} else {
				$string .= $s;
				if ($s != '[') {
					$charFound = true;
				}
			}
		}
		return $string;
	}

	protected function runIntervals($elapsed) {
		foreach ($this->intervalCallbacks as &$callbackData) {
			$callbackData['elapsed'] += $elapsed;
			if ($callbackData['elapsed'] > $callbackData['numSeconds']) {

				$callbackData['elapsed'] = fmod($callbackData['elapsed'], $callbackData['numSeconds']);
				$out = call_user_func($callbackData['callback'], $this);
				if ($out === false) {
					break;
				}
			}
		}
	}


	protected function runKeys($string, $isControl) {
		foreach ($this->keypressCallbacks as $keypressCallback) {
			$out = call_user_func($keypressCallback, $string, $isControl, $this);
			if ($out === false) {
				break 1;
			}
		}
	}

}