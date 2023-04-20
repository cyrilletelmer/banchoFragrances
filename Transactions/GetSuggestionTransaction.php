<?php
//include_once("../factoring.php");
//include_once("../PermanentMemoryHandler.php");


	
class GetSuggestionTransaction extends Transaction
	{	
	//@Override
	public function onExecute(array $inReqParameters, ?int $inResourceID): array
		{
		$vOutArray 				= array();
		$vIngredientIDsArray 	= explode(",",$inReqParameters[GetSuggestionParameters::INGREDIENTS]);
		$vAmountArray 			= null;
		if(isset($inReqParameters[GetSuggestionParameters::AMOUNTS]))
			$vAmountArray = explode(",",$inReqParameters[GetSuggestionParameters::AMOUNTS]);
		$vMinFreq =0;
		if(isset($inReqParameters[GetSuggestionParameters::FREQ_MIN]))
			$vMinFreq = $inReqParameters[GetSuggestionParameters::FREQ_MIN];
		$vCorrelationType 				= "BASIC";
		$vIngredientArray 				= array();
		$vIngredientsDisplayableArray 	= array();
		$vi=0;
		$vTotalBlendingFactor =0;
		$vTotalAmount= 0;
		$vRecipe = "";
		foreach($vIngredientIDsArray as $vIngID)
			{
			$vIngredient = $this->getPermanentMemoryHandler()->getIngredientById($vIngID);
			if(isset($vIngredient))
				{
				$vTotalBlendingFactor 				+= $vIngredient->mBlendingFactor;
				$vIngredientArray[$vi] 				= $vIngredient;
				$vTranslatableNames 				= $this->getPermanentMemoryHandler()->getTranslatablesByTextID($vIngredient->mNameID);
				$vIngredientDisplayable 			= new IngredientDisplayable($vIngredient,$vTranslatableNames,null);
				$vIngredientsDisplayableArray[$vi] 	= $vIngredientDisplayable->getDisplay();
				$vRecipe .= "-".$vIngredient->mIngredientID."-";
				$vi++;
				}
			}
		
		if(!isset($vAmountArray))
			{
			$vAmountArray=array();
			$vi=0;
			foreach($vIngredientArray as $vIngredient)
				{
				$vAmountArray[$vi]=$vIngredient->mBlendingFactor;
				$vTotalAmount += $vAmountArray[$vi];
				$vi++;
				}
			}
		else
			{
			foreach($vAmountArray as $vAmount)
				{
				$vTotalAmount += $vAmount;
				}
			}
		$vWeightingStrategy		= factory_CorrelationWeightingStrategy();
		$vCorrelationCalculator = factory_CorrelationCalculator($vIngredientArray, $vAmountArray,$vCorrelationType, $this->getPermanentMemoryHandler(),$vWeightingStrategy);
		$vCandidates			= $this->getPermanentMemoryHandler()->getIngredients($inReqParameters[GetSuggestionParameters::NOTE_TYPE],$vMinFreq);
		$outSuggestionDisplayable = new SuggestionDisplayable();
		foreach($vCandidates as $vIngredient)
			{
			if(str_contains($vRecipe,"-".$vIngredient->mIngredientID."-"))
				continue;
			$vTranslatableNames 				= $this->getPermanentMemoryHandler()->getTranslatablesByTextID($vIngredient->mNameID);
			$vTranslatableAdjectives 			= $this->getPermanentMemoryHandler()->getTranslatableAdjectives($vIngredient->mIngredientID);
			$vIngredientDisplayable 			= new IngredientDisplayable($vIngredient,$vTranslatableNames,$vTranslatableAdjectives);
			//examining this ingredient as a candidate for suggestion, we also want to suggest the right amount
			//so we will consider 3 possible amounts. Medium, high and low
			//echo "amount normal "
			$vAmountNormal = round( ($vIngredient->mBlendingFactor/($vTotalBlendingFactor+$vIngredient->mBlendingFactor))*($vTotalAmount+$vIngredient->mBlendingFactor));
			$vAmountHigh = round(1.3 * $vAmountNormal);
			$vAmountLow = max (1, round( 0.7* $vAmountNormal));
			$vFinalAmount = 0;
			//echo "amount normal ".$vAmountNormal;
			$vPurportedCorrelation;
			if($vAmountNormal == $vAmountHigh)
				{
				$vPurportedCorrelation = $vCorrelationCalculator->getCorrelationWithAdditionalData($vIngredient,$vAmountNormal);
				$vFinalAmount = $vAmountNormal;
				}
			else
				{
				$vPurportedCorrelationNormal 	=  $vCorrelationCalculator->getCorrelationWithAdditionalData($vIngredient,$vAmountNormal);
				$vPurportedCorrelationHigh 		=   $vCorrelationCalculator->getCorrelationWithAdditionalData($vIngredient,$vAmountHigh);
				$vPurportedCorrelationLow 		=   $vCorrelationCalculator->getCorrelationWithAdditionalData($vIngredient,$vAmountLow);
				if(max($vPurportedCorrelationNormal,$vPurportedCorrelationHigh,$vPurportedCorrelationLow)==$vPurportedCorrelationNormal)
					{
					$vPurportedCorrelation = $vPurportedCorrelationNormal;
					$vFinalAmount = $vAmountNormal;
					}
				else if(max($vPurportedCorrelationNormal,$vPurportedCorrelationHigh,$vPurportedCorrelationLow)==$vPurportedCorrelationHigh)
					{
					$vPurportedCorrelation = $vPurportedCorrelationHigh;
					$vFinalAmount = $vAmountHigh;
					}
				else
					{
					$vPurportedCorrelation = $vPurportedCorrelationLow;
					$vFinalAmount = $vAmountLow;
					}
				}
			$outSuggestionDisplayable->addIngredient($vIngredientDisplayable, $vPurportedCorrelation,$vFinalAmount);
			}
		//$outData 				= 
		
		return $this->createOutput(ErrorCodes::OK, "", $outSuggestionDisplayable->getDisplay());
		}
		
	protected function createParameterChecker() : ParameterChecker
		{
		return new GetSuggestionParameterCheck();
		}
	}
	
class GetSuggestionParameters
	{
	const INGREDIENTS = "ingredients";
	const NOTE_TYPE = "note_type";
	const AMOUNTS = "amounts";
	const FREQ_MIN ="freq_min";
	}

	
class GetSuggestionParameterCheck implements ParameterChecker
	{
	
	public function checkRequestParametersCompletion(array $inParameters, ?int $inResID): string
		{
		$vArrayRegEx = "/^(?:\d+,)*\d+$/i";
		if(isset($inResID) )
			return "GET SUGGESTIONS does not accept resource id";
		else if(!isset($inParameters[GetSuggestionParameters::INGREDIENTS]) )
			return "'ingredients' parameter is mandatory";
		else if(!isset($inParameters[GetSuggestionParameters::NOTE_TYPE]) )
			return "'note_type' parameter is mandatory";
		else if(isset($inParameters[GetSuggestionParameters::NOTE_TYPE]) && $inParameters[GetSuggestionParameters::NOTE_TYPE] != "MIDDLE" && $inParameters[GetSuggestionParameters::NOTE_TYPE] != "TOP" && $inParameters[GetSuggestionParameters::NOTE_TYPE] != "BASE")
			return "'note_type' value should be 'MIDDLE','BASE' or 'TOP'";
		else if(isset($inParameters[GetSuggestionParameters::FREQ_MIN]) && !is_numeric($inParameters[GetSuggestionParameters::FREQ_MIN]))
			return "'freq_min' value should be a number";
		else if(!preg_match($vArrayRegEx, $inParameters[GetSuggestionParameters::INGREDIENTS]))
			return "ingredient list is not well formatted. should be 1,2,3...";
		else if(isset($inParameters[GetSuggestionParameters::AMOUNTS]) && !preg_match($vArrayRegEx, $inParameters[GetSuggestionParameters::AMOUNTS]))
			return "amounts list is not well formatted. should be 1,2,3...";
		else if(isset($inParameters[GetSuggestionParameters::AMOUNTS]) && substr_count($inParameters[GetSuggestionParameters::AMOUNTS],",")!= substr_count($inParameters[GetSuggestionParameters::INGREDIENTS],","))
			return "amounts and ingredients should have the same size";
		return "";
		}
	}


?>