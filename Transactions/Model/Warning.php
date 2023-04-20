<?php

class Warning
	{
	
	const TYPE_LACKS_BASE = "LACKS_BASE";
	const TYPE_LACKS_MIDDLE = "LACKS_MIDDLE";
	const TYPE_LACKS_TOP = "LACKS_TOP";
	const TYPE_STRONG_BASE = "STRONG_BASE";
	const TYPE_WEAK_BASE = "WEAK_BASE";
	const TYPE_STRONG_MIDDLE = "STRONG_MIDDLE";
	const TYPE_WEAK_MIDDLE = "WEAK_MIDDLE";
	const TYPE_STRONG_TOP = "STRONG_TOP";
	const TYPE_WEAK_TOP = "WEAK_TOP";
	const TYPE_EXCESS_AMOUNT = "EXCESS_AMOUNT";
	const TYPE_LACK_AMOUNT = "LACK_AMOUNT";
	
	public string $mType;
	public ?int $mTargetOfWarning;
	
	public function __construct(string $inType, ?int $inTarget)
		{
		$this->mType = $inType;
		$this->mTargetOfWarning = $inTarget;
		}
	}


?>