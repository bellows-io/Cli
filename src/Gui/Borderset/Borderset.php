<?php

namespace Cli\Gui\Borderset;

class Borderset {

	protected $topLeftCorner;
	protected $topRightCorner;
	protected $bottomLeftCorner;
	protected $bottomRightCorner;
	protected $topEdge;
	protected $bottomEdge;
	protected $rightEdge;
	protected $leftEdge;
	protected $leftIntersect;
	protected $rightIntersect;
	protected $bottomIntersect;
	protected $topIntersect;
	protected $innerHorizontalEdge;
	protected $innerVerticalEdge;
	protected $middleIntersect;
	protected $innerLeftIntersect;
	protected $innerBottomIntersect;
	protected $innerRightIntersect;
	protected $innerTopIntersect;

	public function __construct($topLeftCorner, $topRightCorner, $bottomLeftCorner, $bottomRightCorner, $topEdge, $bottomEdge, $rightEdge, $leftEdge, $leftIntersect, $rightIntersect, $bottomIntersect, $topIntersect, $innerHorizontalEdge, $innerVerticalEdge, $middleIntersect, $innerLeftIntersect, $innerBottomIntersect, $innerRightIntersect, $innerTopIntersect) {

		$this->topLeftCorner        = $topLeftCorner;
		$this->topRightCorner       = $topRightCorner;
		$this->bottomLeftCorner     = $bottomLeftCorner;
		$this->bottomRightCorner    = $bottomRightCorner;
		$this->topEdge              = $topEdge;
		$this->bottomEdge           = $bottomEdge;
		$this->rightEdge            = $rightEdge;
		$this->leftEdge             = $leftEdge;
		$this->leftIntersect        = $leftIntersect;
		$this->rightIntersect       = $rightIntersect;
		$this->bottomIntersect      = $bottomIntersect;
		$this->topIntersect         = $topIntersect;
		$this->innerHorizontalEdge  = $innerHorizontalEdge;
		$this->innerVerticalEdge    = $innerVerticalEdge;
		$this->middleIntersect      = $middleIntersect;
		$this->innerLeftIntersect   = $innerLeftIntersect;
		$this->innerBottomIntersect = $innerBottomIntersect;
		$this->innerRightIntersect  = $innerRightIntersect;
		$this->innerTopIntersect    = $innerTopIntersect;
	}

	public function getTopLeftCorner() {
		return $this->topLeftCorner;
	}

	public function getTopRightCorner() {
		return $this->topRightCorner;
	}

	public function getBottomLeftCorner() {
		return $this->bottomLeftCorner;
	}

	public function getBottomRightCorner() {
		return $this->bottomRightCorner;
	}


	public function getTopEdge() {
		return $this->topEdge;
	}

	public function getBottomEdge() {
		return $this->bottomEdge;
	}

	public function getRightEdge() {
		return $this->rightEdge;
	}

	public function getLeftEdge() {
		return $this->leftEdge;
	}


	public function getLeftIntersect() {
		return $this->leftIntersect;
	}

	public function getRightIntersect() {
		return $this->rightIntersect;
	}

	public function getBottomIntersect() {
		return $this->bottomIntersect;
	}

	public function getTopIntersect() {
		return $this->topIntersect;
	}


	public function getInnerHorizontalEdge() {
		return $this->innerHorizontalEdge;
	}

	public function getInnerVerticalEdge() {
		return $this->innerVerticalEdge;
	}


	public function getMiddleIntersect() {
		return $this->middleIntersect;
	}

	public function getInnerLeftIntersect() {
		return $this->innerLeftIntersect;
	}

	public function getInnerBottomIntersect() {
		return $this->innerBottomIntersect;
	}

	public function getInnerRightIntersect() {
		return $this->innerRightIntersect;
	}

	public function getInnerTopIntersect() {
		return $this->innerTopIntersect;
	}



}