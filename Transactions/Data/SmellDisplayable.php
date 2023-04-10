<?php

// IS IT USED?
class SmellDisplayable
	{
	private array $mIngredients;
	private array $mAmounts;
	private array $mPossibleCorrelationTypes;
	private PermanentMemoryHandler $mPermanentMemory;
	private CorrelationCalculator $mCorrelationCalculator;
	public function __construct(array $inIngredients, ?array $inAmounts, CorrelationCalculator $inCorrelationCalculator, PermanentMemoryHandler $inPMH)
		{
		$this->mIngredients 			= $inIngredients;
		$this->mPermanentMemory 		= $inPMH;
		$this->mCorrelationCalculator 	= $inCorrelationCalculator;
		$this->mPossibleCorrelationTypes = array("BASIC");
		if(isset($inAmounts))
			$this->mAmounts = $inAmounts;
		else
			{
			for($vi=0; $vi<count($this->mIngredients);$vi++)
				{
				$this->mAmounts[$vi]= $this->mIngredients[$vi]->mBlendingFactor;
				}
			}
		}
		
		
	/**
 {
"ingredients":JSONARRAYOF(INGREDIENTS),
"averageCorrelations":JSONARRAYOF({"type":STR,"value":DOUBLE})
}*/
		
	public function getDisplay() :array
		{
		$vIngredients = array();
		$vi = 0;
		foreach($this->mIngredients as $vIngredient)
			{
			$vTranslatableNames = $this->mPermanentMemory->getTranslatablesByTextID($vIngredient->mNameID);
			$vDisplayaleIngredient = new IngredientDisplayable($vIngredient, $vTranslatableNames,null);
			$vIngredients[$vi] = $vDisplayaleIngredient;
			$vi++;
			}
		$vAverageCorrelations = array();
		$vi=0;
		foreach($this->mPossibleCorrelationTypes as $vCorrelationType)
			{
			$this->mCorrelationCalculator->reset($this->mIngredients, $this->mAmounts,$vCorrelationType);
			$vCorrelationValue = $this->mCorrelationCalculator->getCorrelation();
			$vAverageCorrelations[$vi] = array("type" => $vCorrelationType, "value"=>$vCorrelationValue);
			$vi++;
			}
		return array
			(
			"ingredients"=> $vIngredients,
			"averageCorrelations" => $vAverageCorrelations
			);
		}
	}



?>