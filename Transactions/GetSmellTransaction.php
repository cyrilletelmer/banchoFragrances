<?php
//include_once("../factoring.php");
//include_once("../PermanentMemoryHandler.php");


	
class GetSmellTransaction extends Transaction
	{
	
	//@Override
	public function onExecute(array $inReqParameters, ?int $inResourceID): array
		{
		$vOutArray 				= array();
		$vWarningsStrategies	= array();
		$vIngredientIDsArray 	= explode(",",$inReqParameters[GetSmellParameters::INGREDIENTS]);
		$vAmountArray 			= null;
		if(isset($inReqParameters[GetSmellParameters::AMOUNTS]))
			$vAmountArray = explode(",",$inReqParameters[GetSmellParameters::AMOUNTS]);
		if(isset($inReqParameters[GetSmellParameters::WARNING_STRATEGY]))
			$vWarningsStrategies = explode(",",$inReqParameters[GetSmellParameters::WARNING_STRATEGY]);
			
		$vCorrelationType 				= "BASIC";
		$vIngredientArray 				= array();
		$vIngredientsDisplayableArray 	= array();
		$vi=0;
		foreach($vIngredientIDsArray as $vIngID)
			{
			$vIngredient = $this->getPermanentMemoryHandler()->getIngredientById($vIngID);
			if(isset($vIngredient))
				{
				$vIngredientArray[$vi] 				= $vIngredient;
				$vTranslatableNames 				= $this->getPermanentMemoryHandler()->getTranslatablesByTextID($vIngredient->mNameID);
				$vTranslatableAdjectives 			= $this->getPermanentMemoryHandler()->getTranslatableAdjectives($vIngredient->mIngredientID);
				$vIngredientDisplayable 			= new IngredientDisplayable($vIngredient,$vTranslatableNames,$vTranslatableAdjectives);
				$vIngredientsDisplayableArray[$vi] 	= $vIngredientDisplayable->getDisplay();
				$vi++;
				}
			}
		if(!isset($vAmountArray))
			{
			$vAmountArray = array();
			$vi=0;
			foreach($vIngredientArray as $vIngredient)
				{
				$vAmountArray[$vi]=$vIngredient->mBlendingFactor;
				$vi++;
				}
			}
		$vWeightingStrategy		= factory_CorrelationWeightingStrategy();
		$vCorrelationCalculator = factory_CorrelationCalculator($vIngredientArray, $vAmountArray,$vCorrelationType, $this->getPermanentMemoryHandler(),$vWeightingStrategy);
		$vWarningsIssuer		= factory_WarningIssuer($vIngredientArray,$vAmountArray, "BASIC_WARNINGS");
		foreach($vWarningsStrategies as $vStrategy)
			{
			$vWarningsIssuer = factory_WarningIssuerWithAdditionalStrategy($vWarningsIssuer,$vStrategy);
			}
		$vWarningsList			= $vWarningsIssuer->calculateWarnings();
		$vArrayOfCorr 			= array("type"=>"BASIC","value"=> $vCorrelationCalculator->getCorrelation());
		$outData 				= array
			(
			"ingredients"=> $vIngredientsDisplayableArray,
			"averageCorrelations"=>$vArrayOfCorr,
			"warnings" => $this->warningListToDisplayable($vWarningsList)
			);
		
		return $this->createOutput(ErrorCodes::OK, "", $outData);
		}
		
	protected function createParameterChecker() : ParameterChecker
		{
		return new GetSmellParameterCheck();
		}
		
	private function warningListToDisplayable(array $inWarnings): array
		{
		$outData = array();
		for($vi = 0; $vi < count($inWarnings) ; $vi++)
			{
			$vWarning = $inWarnings[$vi];
			$outData[$vi] = (new WarningDisplayable($vWarning))->getDisplay();
			}
		return $outData;
		}
	}
	
	
class GetSmellParameters
	{
	const INGREDIENTS = "ingredients";
	const AMOUNTS = "amounts";
	const WARNING_STRATEGY ="warning_strategy";
	}
	
class GetSmellParameterCheck implements ParameterChecker
	{
	public function checkRequestParametersCompletion(array $inParameters, ?int $inResID): string
		{
		$vArrayRegEx = "/^(?:\d+,)*\d+$/i";
		if(isset($inResID) )
			return "GET SMELL does not accept resource id";
		else if(!isset($inParameters[GetSmellParameters::INGREDIENTS]) )
			return "'ingredients' parameter is mandatory";
		else if(!preg_match($vArrayRegEx, $inParameters[GetSmellParameters::INGREDIENTS]))
			return "ingredient list is not well formatted. should be 1,2,3...";
		else if(isset($inParameters[GetSmellParameters::AMOUNTS]) && !preg_match($vArrayRegEx, $inParameters[GetSmellParameters::AMOUNTS]))
			return "amount list is not well formatted. should be 1,2,3...";
		else if(isset($inParameters[GetSmellParameters::AMOUNTS]) && substr_count($inParameters[GetSmellParameters::AMOUNTS],",")!= substr_count($inParameters[GetSmellParameters::INGREDIENTS],","))
			return "amounts and ingredients should have the same size";
		return "";
		}
	}


?>