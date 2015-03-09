<?php

namespace Cli\Terminal;

interface TerminalInterface {

	public function setPosition($x, $y);
	public function getPosition();

	public function getSize();
	public function getWidth();
	public function getHeight();

	public function move($up, $forward);
	public function moveUp($count);
	public function moveDown($count);
	public function moveForward($count);
	public function moveBackward($count);

	public function eraseEnd();
	public function eraseStart();
	public function eraseLine();
	public function eraseDown();
	public function eraseUp();
	public function eraseScreen();

	public function save();
	public function restore();
	public function format(array $format);
	public function resetFormatting();
	public function printf($format);
	public function getStatus();
	public function readInput($callback);

}