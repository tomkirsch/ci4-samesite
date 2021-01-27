<?php namespace Tomkirsch\Samesite;

class SamesiteDetector{
	// determine if we can set samesite cookie
	static function isPossible():bool{
		return version_compare(phpversion(), '7.3.0', '>=');
	}
}