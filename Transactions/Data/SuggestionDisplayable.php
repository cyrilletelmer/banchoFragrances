<?php

/** object able to render a suggestion in the way it will be presented in the answer of the API call*/
class SuggestionDisplayable
	{
	
	
	public function addIngredient(IngredientDisplayable $inIngDis, float $inDesirabilityValue,int $inAmount)
		{
		//print_r($this->mSortedBaseCandidates);
		$vSuggestionItem = new SuggestionItem($inIngDis,$inDesirabilityValue, $inAmount);
		$this->insert3($this->mSortedBaseCandidates, $vSuggestionItem);
		}
	
		
	public function getDisplay() : array
		{
		return $this->toDisplayableArray($this->mSortedBaseCandidates);
		}
		
		
	private array $mSortedBaseCandidates = array();
	
	private function toDisplayableArray(array $inSortedArrayOfCandidates) : array
		{
		$outDisplayables = array();
		$vi=0;
		foreach($inSortedArrayOfCandidates as $vCandidate)
			{
			$outDisplayables[$vi]= array
				(
				SuggestionDisplayableFields::DESIRABILITY_VALUE=>$vCandidate->mDesirabilityValue,
				SuggestionDisplayableFields::INGREDIENT =>$vCandidate->mIngredientDisplayable->getDisplay(),
				SuggestionDisplayableFields::SUGGESTED_AMOUNT => $vCandidate->mSuggestedAmount
				);
			$vi++;
			}
		return $outDisplayables;
		}
	
	private function insert3(&$arr, SuggestionItem $elem)
		{
		if(count($arr)==0)
			{
			$arr[0] = $elem;
			return;
			}
		$startIndex = 0;
		$stopIndex = count($arr) - 1;
		$middle = 0;
		while($startIndex < $stopIndex)
			{
			$middle = ceil(($stopIndex + $startIndex) / 2);
			if($elem->mDesirabilityValue > $arr[$middle]->mDesirabilityValue)
				$stopIndex = $middle - 1;
			else if($elem->mDesirabilityValue <= $arr[$middle]->mDesirabilityValue)
				$startIndex = $middle;
			}
		$offset = $elem->mDesirabilityValue >= $arr[$startIndex]->mDesirabilityValue ? $startIndex : $startIndex + 1; 
		array_splice($arr, $offset, 0, array($elem));
		}
	
	}
	

class SuggestionDisplayableFields
	{
	const DESIRABILITY_VALUE = "desirabilityValue";
	const INGREDIENT ="ingredient";
	const SUGGESTED_AMOUNT = "suggestedAmount";
	}
	
	
class SuggestionItem {
	public function __construct
		(
		 IngredientDisplayable $inIngredientDisplayable,
		 float $inDesirabilityValue,
		 int $inSuggestedAmount
		 )
		{
		$this->mIngredientDisplayable = $inIngredientDisplayable;
		$this->mDesirabilityValue = $inDesirabilityValue;
		$this->mSuggestedAmount = $inSuggestedAmount;
		}
	public float $mDesirabilityValue;// correlation usally
	public IngredientDisplayable $mIngredientDisplayable;
	public int $mSuggestedAmount;
}






?>