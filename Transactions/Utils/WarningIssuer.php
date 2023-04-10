<?php

/**
 * object analyzing a perfume recipe and issuing warnings if something is weird.
 **/
class WarningIssuer
	{
	private array $mIngredients;
	private array $mAmounts;
	public function __construct(array $inIngredients, array $inAmounts)
		{
		$this->mIngredients = $inIngredients;
		$this->mAmounts = $inAmounts;
		}
		
		
	public function calculateWarnings() : array
		{
		$outWarnings = array();
		$vBasePresent = false;
		$vTopPresent = false;
		$vMiddlePresent = false;
		$vTotalAmount = 0;
		$vTotalBlendingFactor=0;
		for($vi = 0; $vi < count($this->mIngredients); $vi++)
			{
			$vIngredient 			= $this->mIngredients[$vi];
			$vAmount 				= $this->mAmounts[$vi];
			$vTotalAmount 			+= $vAmount;
			$vTotalBlendingFactor 	+= $vIngredient->mBlendingFactor;
			if(!$vBasePresent && $vIngredient->mNoteType == "BASE")
				$vBasePresent = true;
			if(!$vMiddlePresent && $vIngredient->mNoteType == "MIDDLE")
				$vMiddlePresent = true;
			if(!$vTopPresent && $vIngredient->mNoteType == "TOP")
				$vTopPresent = true;
			
			}
		$vi=0;
		if(!$vTopPresent)
			{
			$outWarnings[$vi] = new Warning(Warning::TYPE_LACKS_TOP, null);
			$vi++;
			}
		if(!$vMiddlePresent)
			{
			$outWarnings[$vi] = new Warning(Warning::TYPE_LACKS_MIDDLE, null);
			$vi++;
			}
		if(!$vBasePresent)
			{
			$outWarnings[$vi] = new Warning(Warning::TYPE_LACKS_BASE, null);
			$vi++;
			}
			
		return $outWarnings;
		}
	
	}


?>