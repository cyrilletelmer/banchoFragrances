<?php

/**
 * object calculating the average value of correlation matrix for a list of ingredients and their amounts.
 * This aims at calculating the "consistency" of a recipe (ie how much it smells like common perfumes in market)
 * */
class CorrelationCalculator {
	private array $mIngredients;
	private array $mAmounts;
	private string $mCorrelationType;
	private CorrelationWeightingStrategy $mWeightingStrategy;
	private PermanentMemoryHandler $mPermanentMemory;
	private float $mSumOfCorrelations = 0;
	private float $mSumOfWeights = 0;
	private float $mCorrelationValue ;
	

	
	public function __construct
		(
		array $inIngredients,
		?array $inAmounts,
		string $inCorrelationType,
		PermanentMemoryHandler $inPMH,
		CorrelationWeightingStrategy $inWeightingStrategy
		)
		{
			
		$this->mWeightingStrategy = $inWeightingStrategy;
		$this->reset($inIngredients,$inAmounts,$inCorrelationType);
		$this->mPermanentMemory = $inPMH;
		}
		
	public function reset
		(
		array $inIngredients,
		?array $inAmounts,
		string $inCorrelationType
		)
		{
		$this->mIngredients = $inIngredients;
		$this->mCorrelationType = $inCorrelationType;
		$this->mCorrelationValue = -2.0;
		if(!isset($inAmounts))
			{
			$this->mAmounts=array();
			$vi=0;
			foreach($inIngredients as $vIngredient)
				{
				$this->mAmounts[$vi]=$vIngredient->mBlendingFactor;
				$vi++;
				}
			}
		else
			{
			$this->mAmounts = $inAmounts;
			}
		}
		
		
	/**
	 * calculates mean value of correlation matrix for the current recipe.
	 * The mean is weighted according to an weighting strategy (depending on the amounts, the note type, and removing the 1 diagonal)
	 * */
	public function getCorrelation() : float
		{
		if($this->mCorrelationValue!= -2.0)
			return $this->mCorrelationValue;
		if(count($this->mAmounts) != count($this->mIngredients))
			return -2.0;
		$vIndexIngredient1 =0;
		foreach($this->mIngredients as $vIngredient1)
			{
			$vIndexIngredient2 =0;
			foreach($this->mIngredients as $vIngredient2)
				{
				$vCorrelation 			= $this->mPermanentMemory->getCorrelation($vIngredient1->mIngredientID, $vIngredient2->mIngredientID, $this->mCorrelationType);
				$vWeight 				= $this->mWeightingStrategy->calculateWeight($vIngredient1,$vIngredient2,$this->mAmounts[$vIndexIngredient1],$this->mAmounts[$vIndexIngredient2]);
				$this-> mSumOfWeights += $vWeight;
				$this->mSumOfCorrelations += $vWeight * $vCorrelation->mValue;
				//echo "+ $vWeight x ".$vCorrelation->mValue;
				$vIndexIngredient2++;
				}
			$vIndexIngredient1++;
			}
		if($this->mSumOfWeights!=0)
			{
			$this->mCorrelationValue = $this->mSumOfCorrelations/$this->mSumOfWeights;
			return $this->mCorrelationValue;
			}
			
		return -2.0;
		}
		
		
	/**
	 * caculates what would be mean value of correlation matrix for the current recipe if we added one ingredient.
	 * The mean is weighted according to an weighting strategy (depending on the amounts, the note type, and removing the 1 diagonal)
	 * */
	public function getCorrelationWithAdditionalData(Ingredient $inIngredient, ?float $inAmount) : float
		{
		$this->getCorrelation();
		$outCorrelationWithAdditionalData 	= -2.0;
		$outSumOfCorrelations 				= $this->mSumOfCorrelations;
		$outSumOfWeights 					= $this->mSumOfWeights;
		$vAmount = $inIngredient->mBlendingFactor;
		if(isset($inAmount))
			$vAmount = $inAmount;
		$vIndexIngredient2 =0;
		foreach($this->mIngredients as $vIngredient2)
			{
			$vCorrelation = $this->mPermanentMemory->getCorrelation($inIngredient->mIngredientID, $vIngredient2->mIngredientID, $this->mCorrelationType);
			$vWeight = $this->mWeightingStrategy->calculateWeight($inIngredient,$vIngredient2,$vAmount,$this->mAmounts[$vIndexIngredient2]);
			$outSumOfWeights += $vWeight;
			$outSumOfCorrelations += $vWeight * $vCorrelation->mValue;
			$vIndexIngredient2++;
			}
		if($outSumOfWeights!= 0)
			$outCorrelationWithAdditionalData = $outSumOfCorrelations / $outSumOfWeights;
		//echo "<br> testing ingredient ".$inIngredient->mIngredientID." with amount ".$inAmount." results in correlation ".$outCorrelationWithAdditionalData."  ";
		//echo ", sum of corr : ".$this->mSumOfCorrelations." sum of weights ".$this->mSumOfWeights."<br>";
		return $outCorrelationWithAdditionalData;				
		}
		
	
}





?>