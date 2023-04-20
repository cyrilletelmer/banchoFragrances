<?php
//include_once("../factoring.php");
//include_once("../PermanentMemoryHandler.php");


	
class GetIngredientsTransaction extends Transaction
	{
	
	//@Override
	public function onExecute(array $inReqParameters, ?int $inResourceID): array
		{
		$vOutArray =array();
		//var_dump($inReqParameters);
		//var_dump($this->mURLArray);
		if(isset($inResourceID))
			{
				
			$vIngredient = $this->getPermanentMemoryHandler()->getIngredientById($inResourceID);
			if(isset($vIngredient))
				{
				$vTranslatableNames = $this->getPermanentMemoryHandler()->getTranslatablesByTextID($vIngredient->mNameID);
				$vIngredientDisplayable = new IngredientDisplayable($vIngredient,$vTranslatableNames,null);
				$vOutArray[0] = $vIngredientDisplayable->getDisplay();
				return $this->createOutput(ErrorCodes::OK, " ingredient for one id ", $vOutArray);
				}
			else
				return $this->createOutput(ErrorCodes::RESSOURCE_NOT_FOUND, "no ingredient found for that id", null);	
			}
		else
			{
			$vMinFreq =0;
			if(isset($inReqParameters[GetIngredientsParameters::FREQ_MIN]))
				$vMinFreq = $inReqParameters[GetIngredientsParameters::FREQ_MIN];
			if(isset($inReqParameters[GetIngredientsParameters::NOTE_TYPE]))
				$vIngredients = $this->getPermanentMemoryHandler()->getIngredients($inReqParameters[GetIngredientsParameters::NOTE_TYPE],$vMinFreq);
			else
				$vIngredients = $this->getPermanentMemoryHandler()->getIngredients(null,$vMinFreq);
			$vi = 0;
			foreach($vIngredients as $vIngredient)
				{
				$vTranslatableNames = $this->getPermanentMemoryHandler()->getTranslatablesByTextID($vIngredient->mNameID);
				$vTranslatableAdjectives = $this->getPermanentMemoryHandler()->getTranslatableAdjectives($vIngredient->mIngredientID);
				$vIngredientDisplayable = new IngredientDisplayable($vIngredient,$vTranslatableNames,$vTranslatableAdjectives);
				$vOutArray[$vi] = $vIngredientDisplayable->getDisplay();
				$vi++;
				}
			return $this->createOutput(ErrorCodes::OK, " ingredients : ", $vOutArray);
			}
		//echo "resid ".$inResourceID;
		return $this->createOutput(ErrorCodes::UNKNOWN_REQUEST, "stub of GET ingredient request ", null);
		}
		
	protected function createParameterChecker() : ParameterChecker
		{
		return new GetIngredientsParameterCheck();
		}
	}
	
class GetIngredientsParameters
	{
	const BLENDING_FACTOR = "blending_factor";
	const NOTE_TYPE = "note_type";
	const FREQ_MIN = "freq_min";
	}
	
class GetIngredientsParameterCheck implements ParameterChecker
	{
	public function checkRequestParametersCompletion(array $inParameters, ?int $inResID): string
		{
		if(isset($inResID) && $inResID <0)
			return "resource id should be positive";
		else if(isset($inParameters[GetIngredientsParameters::NOTE_TYPE])
				&& $inParameters[GetIngredientsParameters::NOTE_TYPE] != "MIDDLE"
				&& $inParameters[GetIngredientsParameters::NOTE_TYPE] != "TOP"
				&& $inParameters[GetIngredientsParameters::NOTE_TYPE] != "BASE")
			return "'note_type' value should be 'MIDDLE','BASE' or 'TOP'";
		else if(isset($inParameters[GetIngredientsParameters::BLENDING_FACTOR])
				&& !is_numeric($inParameters[GetIngredientsParameters::BLENDING_FACTOR]))
			return "'blending_factor' value should be a number";
		else if(isset($inParameters[GetIngredientsParameters::FREQ_MIN])
				&& !is_numeric($inParameters[GetIngredientsParameters::FREQ_MIN]))
			return "'freq_min' value should be a number";
		else if(isset($inParameters["nameLIKE"]) && !isset($inParameters["language"]))
			return "'nameLIKE' parameter should be accompanied with 'language' parameter";
		return "";
		}
	}


?>