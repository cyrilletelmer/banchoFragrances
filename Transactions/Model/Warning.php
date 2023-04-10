<?php

class Warning
	{
	const TYPE_EXCESS_AMOUNT = "EXCESS_AMOUNT";
	const TYPE_LACKS_BASE = "LACKS_BASE";
	const TYPE_LACKS_MIDDLE = "LACKS_MIDDLE";
	const TYPE_LACKS_TOP = "LACKS_TOP";
	
	public string $mType;
	public ?int $mTargetOfWarning;
	
	public function __construct(string $inType, ?int $inTarget)
		{
		$this->mType = $inType;
		$this->mTargetOfWarning = $inTarget;
		}
	}


?>