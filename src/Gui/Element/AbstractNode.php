<?php

namespace Cli\Gui\Element;

use \Cli\Gui\Traits\RowMemoryTrait;
use \Cli\Terminal\TerminalInterface;

abstract class AbstractNode extends Element {

	/**
	 * @var TerminalInterface
	 */
	protected $terminal;

	protected $color = null;
	protected $background = null;
	protected $position = null;
	protected $width = null;
	protected $height = null;
	protected $left = null;
	protected $right = null;
	protected $padding = [0,0,0,0];

	private $parent = null;
	protected $children = [];

	protected function getParent() {
		return $this->parent;
	}

	protected function getRenderingParent() {
		if (! $this->parent) {
			return null;
		}
		if (in_array($this->parent->getPosition(), ['relative', 'absolute'])) {
			return $this->parent;
		}
		return $this->parent->getRenderingParent();
	}

	public function getLayout($parentLayout, $renderingParentLayout, $previousSibling = null) {
		$right = null;
		$bottom = null;

		if (! $this->parent) {
			$left = 0;
			$top = 0;
			$width = $this->terminal->getWidth();
			$height = $this->terminal->getHeight();
		} else {
			switch ($this->position) {
				case 'fixed':
					$left = $this->left;
					$top = $this->top;
					$width = $this->width;
					$height = $this->height;
					break;
				case 'absolute':
					$left = $thix->left + $renderingParentLayout['left'];
					$top = $this->top + $renderingParentLayout['top'];

					$right = $left;
					$bottom = $top;

					$width = $this->width;
					$height = $this->height;
					break;
				default:
				case 'static':
				case 'relative':

					$left = $parentLayout['left'];
					$top = $parentLayout['top'];

					$width = $this->width;
					$height = $this->height;

					if ($previousSibling) {
						$left += $previousSibling['right'];
						$top += $previousSibling['bottom'];
					}

					if ($this->position == 'relative') {
						$right = $left + $width;
						$bottom = $top + $height;

						$left += $this->left;
						$top += $this->top;
					} else {
						if ($left + $width > $parentLayout['width'] && $previousSibling) {
							$left = $parentLayout['left'];
							$top = $previousSibling['bottom'];
						}
					}
					break;
			}
		}
		return [
			'left'   => $left,
			'width'  => $width,
			'top'    => $top,
			'height' => $height,

			'right'  => is_null($right) ? $left + $width, $right,
			'bottom'  => is_null($bottom) ? $top + $height, $bottom
		]
	}

	public function render() {

	}
}