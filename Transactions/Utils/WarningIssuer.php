<?php

/**
 * object analyzing a perfume recipe and issuing warnings if something is weird.
 **/
class WarningIssuer
	{
	protected array $mIngredients;
	protected array $mAmounts;
	public function __construct(array $inIngredients, array $inAmounts)
		{
		$this->mIngredients = $inIngredients;
		$this->mAmounts = $inAmounts;
		}
		
		
	public function calculateWarnings() : array
		{
		return array();
		}
	
	}
	
/** object adding new ways to find warnings to an existing warningIssuer*/
abstract class WarningIssuerDecorator  extends WarningIssuer
	{
	
	protected WarningIssuer $mDecorated;
	public function __construct(WarningIssuer $inDecorated)
		{
		$this->mDecorated = $inDecorated;
		parent::__construct($this->mDecorated->mIngredients,$this->mDecorated->mAmounts);
		}
		
	public abstract function calculateAdditionalWarnings() : array;
		
	public function calculateWarnings() : array
		{
		$outArray = $this->mDecorated->calculateWarnings();
		$vDecoratedArraySize = count($outArray);
		$vAdditionalWarnings = $this->calculateAdditionalWarnings();
		for($vi = $vDecoratedArraySize; $vi < $vDecoratedArraySize+ count($vAdditionalWarnings); $vi++)
			{
			$outArray[$vi] = $vAdditionalWarnings[$vi- $vDecoratedArraySize];
			}
		return $outArray;
		}
	}


	
/** warning issuer decorator specialized in finding whether there are TOP, MIDDLE and BASE notes in a recipe,
and issue warnings if not */
class BasicPyramidalCheckWarningIssuer extends WarningIssuerDecorator
	{
	
	public function __construct(WarningIssuer $inDecorated)
		{
		parent::__construct($inDecorated);
		}
	
	public function calculateAdditionalWarnings() : array
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

	
/** warning issuer issueing warnings if either top, middle or base is over/undersized */
class PyramidalBalanceWarningIssuer extends WarningIssuerDecorator
	{
		
	const TOP_NOTE_MINIMAL_PROPORTION =0.1;
	const TOP_NOTE_MAXIMAL_PROPORTION = 0.5;
	
	const MIDDLE_NOTE_MINIMAL_PROPORTION =0.4;
	const MIDDLE_NOTE_MAXIMAL_PROPORTION = 0.8;
	
	const BASE_NOTE_MINIMAL_PROPORTION =0.05;
	const BASE_NOTE_MAXIMAL_PROPORTION = 0.2;
	


	
	
	public function __construct(WarningIssuer $inDecorated)
		{
		parent::__construct($inDecorated);
		}
	
	public function calculateAdditionalWarnings() : array
		{
		$outWarnings = array();
		$vBaseAmount = 0;
		$vMiddleAmount = 0;
		$vTopAmount = 0;
		for($vi = 0; $vi < count($this->mIngredients); $vi++)
			{
			$vIngredient 			= $this->mIngredients[$vi];
			$vAmount 				= $this->mAmounts[$vi];
			if($vIngredient->mNoteType == "BASE")
				$vBaseAmount += $vAmount;
			if($vIngredient->mNoteType == "MIDDLE")
				$vMiddleAmount += $vAmount;
			if($vIngredient->mNoteType == "TOP")
				$vTopAmount += $vAmount;
			}
		$vi=0;
		$vTotalAmount = $vTopAmount + $vMiddleAmount + $vBaseAmount;
		if(($vTopAmount/$vTotalAmount)<PyramidalBalanceWarningIssuer::TOP_NOTE_MINIMAL_PROPORTION)
			{
			$outWarnings[$vi] = new Warning(Warning::TYPE_WEAK_TOP, null);
			$vi++;
			}
		else if(($vTopAmount/$vTotalAmount)>PyramidalBalanceWarningIssuer::TOP_NOTE_MAXIMAL_PROPORTION)
			{
			$outWarnings[$vi] = new Warning(Warning::TYPE_STRONG_TOP, null);
			$vi++;
			}
			
		if(($vMiddleAmount/$vTotalAmount)<PyramidalBalanceWarningIssuer::MIDDLE_NOTE_MINIMAL_PROPORTION)
			{
			$outWarnings[$vi] = new Warning(Warning::TYPE_WEAK_MIDDLE, null);
			$vi++;
			}
		else if(($vMiddleAmount/$vTotalAmount)>PyramidalBalanceWarningIssuer::MIDDLE_NOTE_MAXIMAL_PROPORTION)
			{
			$outWarnings[$vi] = new Warning(Warning::TYPE_STRONG_MIDDLE, null);
			$vi++;
			}
			
		if(($vBaseAmount/$vTotalAmount)<PyramidalBalanceWarningIssuer::BASE_NOTE_MINIMAL_PROPORTION)
			{
			$outWarnings[$vi] = new Warning(Warning::TYPE_WEAK_BASE, null);
			$vi++;
			}
		else if(($vBaseAmount/$vTotalAmount)>PyramidalBalanceWarningIssuer::BASE_NOTE_MAXIMAL_PROPORTION)
			{
			$outWarnings[$vi] = new Warning(Warning::TYPE_STRONG_BASE, null);
			$vi++;
			}
		return $outWarnings;
		}	
	}
	
	
	

/** warning issuer checking if any individual ingredient appears overdosed*/
class IndividualDosingWarningIssuer extends WarningIssuerDecorator
	{
		
	const MAX_MULTIPLE_OF_STANDARD_PROPORTION = 1.5;
	const MIN_MULTIPLE_OF_STANDARD_PROPORTION = 0.25;
	
	public function __construct(WarningIssuer $inDecorated)
		{
		parent::__construct($inDecorated);
		}
	
	public function calculateAdditionalWarnings() : array
		{
		$outArray = array();
		$vTotalAmount = 0;
		$vTotalBlendingFactor=0;
		for($vi = 0; $vi < count($this->mIngredients); $vi++)
			{
			$vIngredient 			= $this->mIngredients[$vi];
			$vAmount 				= $this->mAmounts[$vi];
			$vTotalAmount 			+= $vAmount;
			$vTotalBlendingFactor 	+= $vIngredient->mBlendingFactor;
			}
		$vJ =0;
		for($vi = 0; $vi < count($this->mIngredients); $vi++)
			{
			$vIngredient 			= $this->mIngredients[$vi];
			$vAmount 				= $this->mAmounts[$vi];
			$vProportion 			= $vAmount/$vTotalAmount;
			$vReferenceAmount	= max(1,round(($vIngredient->mBlendingFactor/$vTotalBlendingFactor)*$vTotalAmount));
			//echo "ingredient ".$vIngredient->mIngredientID;
			//echo "amount ".$vAmount;
			//echo "ref prop ".$vReferenceAmount."<br>";
			if($vAmount > IndividualDosingWarningIssuer::MAX_MULTIPLE_OF_STANDARD_PROPORTION*$vReferenceAmount)
				{
				$outArray[$vJ] = new Warning(Warning::TYPE_EXCESS_AMOUNT, $vIngredient->mIngredientID);
				$vJ++;
				}
			else if($vAmount < IndividualDosingWarningIssuer::MIN_MULTIPLE_OF_STANDARD_PROPORTION*$vReferenceAmount)
				{
				$outArray[$vJ] = new Warning(Warning::TYPE_LACK_AMOUNT, $vIngredient->mIngredientID);
				$vJ++;
				}
			}
		return $outArray;
		}	
	}
// example
class EmptyWarningIssuerDecorator extends WarningIssuerDecorator
	{
	public function __construct(WarningIssuer $inDecorated)
		{
		parent::__construct($inDecorated);
		}
	
	public function calculateAdditionalWarnings() : array
		{
		return array();
		}	
	}
	
	


?>