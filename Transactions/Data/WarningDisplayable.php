<?php
class WarningDisplayable {
	
	private Warning $mWarning;
	
	public function __construct(Warning $inWarning)
	{
	$this->mWarning = $inWarning;
	}
	/*
	 *JSONARRAYOF({"type":STR(<EXCESS_AMOUNT,LACK_BASE,LACK_MIDDLE,LACK_TOP>), "targetOfWarning":OPTINT})
}*/
	public function getDisplay() :array
		{
		
		return array
			(
			"type" => $this->mWarning->mType,
			"targetOfWarning" => $this->mWarning->mTargetOfWarning
			);
		}
}


?>