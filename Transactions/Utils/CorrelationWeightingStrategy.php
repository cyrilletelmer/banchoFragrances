<?php
/** object calculating the weight of a correlation value between two ingredients, when calculating average value of correlation matrix
in CorrelationCalculator
*/
class CorrelationWeightingStrategy {
	
	
	public function calculateWeight(Ingredient $inIngredient1, Ingredient $inIngredient2, int $inAmount1, int $inAmount2) : float
		{
		$outWeight 				= 0;
		if($inIngredient1->mIngredientID != $inIngredient2->mIngredientID)
			{
			$vWeightIngredient1 	= $inAmount1/$inIngredient1->mBlendingFactor;
			$vWeightIngredient2 	= $inAmount2/$inIngredient2->mBlendingFactor;
			$vWeightModulator 		= $this->calculateNoteTypeBasedCorrelationWeight($inIngredient1, $inIngredient2);
			$outWeight 				= min($vWeightIngredient1, $vWeightIngredient2) * $vWeightModulator;
			}
		return $outWeight;
		}
	
	
	
	const  SHORT_LIVED_CORRELATION_WEIGHT = 0.7;
	const MEDIUM_LIVED_CORRELATION_WEIGHT = 0.85;
	const LONG_LIVED_CORRELATION_WEIGHT = 1;
	
	private function calculateNoteTypeBasedCorrelationWeight(Ingredient $inIngredient1, Ingredient $inIngredient2)
		{
		if($inIngredient1->mNoteType == "TOP" || $inIngredient2->mNoteType =="TOP")
			return CorrelationWeightingStrategy::SHORT_LIVED_CORRELATION_WEIGHT;
		if($inIngredient1->mNoteType == "MIDDLE" || $inIngredient2->mNoteType == "MIDDLE")
			return  CorrelationWeightingStrategy::MEDIUM_LIVED_CORRELATION_WEIGHT;
		return  CorrelationWeightingStrategy::LONG_LIVED_CORRELATION_WEIGHT;
		}
}


?>