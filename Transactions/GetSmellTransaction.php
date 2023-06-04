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
		if(isset($inReqParameters[GetSmellParameters::CORRELATION_TYPE]))
			$vCorrelationType = $inReqParameters[GetSmellParameters::CORRELATION_TYPE];
		$vIngredientArray 				= array();
		$vIngredientsDisplayableArray 	= array();
		$vi=0;
		$vAdjectiveIssuer = factory_AdjectiveIssuer();
		if(!isset($vAmountArray))
			$vAmountArray = array();
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
				//here if the user hasn't provided an amount array, we will fill it up ourselves with blending factors by default
				if(!isset($vAmountArray[$vi]))
					$vAmountArray[$vi] = $vIngredient->mBlendingFactor;
				$vAdjectiveIssuer->addIngredient($vIngredientDisplayable,$vAmountArray[$vi]);
				$vi++;
				}
			}
		//----------
		//below the actual analysis of the smell, relying on objects specialized with analysing aspects
		//------------
		$vWeightingStrategy		= factory_CorrelationWeightingStrategy();
		$vCorrelationCalculator = factory_CorrelationCalculator($vIngredientArray, $vAmountArray,$vCorrelationType, $this->getPermanentMemoryHandler(),$vWeightingStrategy);
		$vWarningsIssuer		= factory_WarningIssuer($vIngredientArray,$vAmountArray, "BASIC_WARNINGS");
		foreach($vWarningsStrategies as $vStrategy)
			{
			$vWarningsIssuer = factory_WarningIssuerWithAdditionalStrategy($vWarningsIssuer,$vStrategy);
			}
		$vMaxNumberOfAdjectives	= max(1,round(count($vAmountArray)*0.75));
		$vWarningsList			= $vWarningsIssuer->calculateWarnings();
		$vArrayOfCorr 			= array("type"=>$vCorrelationType,"value"=> $vCorrelationCalculator->getCorrelation());
		$vBestAdjectivesList	= $vAdjectiveIssuer->calculateBestAdjectives($vMaxNumberOfAdjectives);
		$outData 				= array
			(
			"ingredients"=> $vIngredientsDisplayableArray,
			"averageCorrelations"=>$vArrayOfCorr,
			"warnings" => $this->warningListToDisplayable($vWarningsList),
			"bestAdjectives" => $this->bestAdjectivesListToDisplayable($vBestAdjectivesList)
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
		
	private function bestAdjectivesListToDisplayable(array $inBestAdjectives)
		{
		
		$outFinalArray = array();
		$vi =0;
		foreach($inBestAdjectives as  $vAdj)
			{
			$vMultilingualWord = $vAdj->mMultilingual;
			$vMultilingualWord["strength"] = $vAdj->mStrength;
			$outFinalArray[$vi] =$vMultilingualWord;
			$vi++;
			}
		return $outFinalArray;
		
		}
	}
	
	
class GetSmellParameters
	{
	const INGREDIENTS = "ingredients";
	const AMOUNTS = "amounts";
	const WARNING_STRATEGY ="warning_strategy";
	const CORRELATION_TYPE="correlation_type";
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
		
		else if(isset($inParameters[GetSmellParameters::CORRELATION_TYPE]) && $inParameters[GetSmellParameters::CORRELATION_TYPE]!="BASIC" && $inParameters[GetSmellParameters::CORRELATION_TYPE]!="UNGENDERED" && $inParameters[GetSmellParameters::CORRELATION_TYPE]!="SIGNIFICATIVE" )
			return "correlation_type is not among authorized values: BASIC, UNGENDERED, SIGNIFICATIVE";
		else if(isset($inParameters[GetSmellParameters::AMOUNTS]) && substr_count($inParameters[GetSmellParameters::AMOUNTS],",")!= substr_count($inParameters[GetSmellParameters::INGREDIENTS],","))
			return "amounts and ingredients should have the same size";
		return "";
		}
	}


?>