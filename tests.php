<?php
include_once("factoring.php");

// checking date handling
echo "date handler tests<br>";
$vDateHandler = factory_DateHandler();
$vCandidateDate = "12-12-2001 00:00";
assert($vDateHandler->checkDate($vCandidateDate));
$vCandidateDate = "31-12-2022 23:59";
assert($vDateHandler->checkDate($vCandidateDate));
$vCandidateDate = "31-02-2022 23:59";
assert(!$vDateHandler->checkDate($vCandidateDate));
$vCandidateDate = "20-02-2022 23:59";
assert(!$vDateHandler->checkDateInFuture($vCandidateDate));
assert($vDateHandler->checkDateInPast($vCandidateDate));
assert($vDateHandler->toTimestamp($vCandidateDate) == 1645401540);
$vCandidateDate="27-02-2030 20:00";
assert($vDateHandler->checkDate($vCandidateDate));
assert($vDateHandler->checkDateInFuture($vCandidateDate));
echo "get ingredients tests<br>";
$vGetIngredientsURLParameters 	= array(  "blending_factor"  => "b" );
$vGetIngredientURLArray 		= array("","ingredients","2","");
$vGetIngredientsTransaction 	= new GetIngredientsTransaction("GET",$vGetIngredientURLArray,$vGetIngredientsURLParameters);
$vGetIngredientsResult 			= $vGetIngredientsTransaction->execute();
assert($vGetIngredientsResult["errorCode"]==422);

echo "correlation calculator tests<br>";
//3 ingredients in smell
$vAmount1 = 9;
$vIngredient1 = new Ingredient();
$vIngredient1->mIngredientID = 2;
$vIngredient1->mNoteType = "TOP";
$vIngredient1->mNameID = 0;
$vIngredient1->mBlendingFactor = 8;
$vIngredient1->mFreq = 1;

$vAmount2 = 8;
$vIngredient2 = new Ingredient();
$vIngredient2->mIngredientID = 3;
$vIngredient2->mNoteType = "TOP";
$vIngredient2->mNameID = 0;
$vIngredient2->mBlendingFactor = 4;
$vIngredient2->mFreq = 1;


$vAmount3 = 3;
$vIngredient3 = new Ingredient();
$vIngredient3->mIngredientID = 4;
$vIngredient3->mNoteType = "TOP";
$vIngredient3->mNameID = 0;
$vIngredient3->mBlendingFactor = 7;
$vIngredient3->mFreq = 1;

//correlation =  (9/8)*c(2,3;=0.5) + (3/7)*c(2,4;=0.3)
//+ (9/8)*c(3,2;=0.5) +  (3/7)*c(3,4;=0.1)
//+ (3/7)*c(4,2;=0.3) +  (3/7)*c(4,3;=0.1) + 
//= 5.02 /7.51 = 0.66


//((9/8)*0.5+ (3/7)*0.3+ (9/8)*0.5 +  (3/7)*0.1+ (3/7)*0.3 +  (3/7)*0.1) /((9/8)+ (3/7)+ (9/8) +  (3/7)+ (3/7)+  (3/7))


class CorrelationForTest extends Correlation{
	public function __construct(float $inValue)
		{
		$this->mValue = $inValue;
		}
}


class PMHForTest implements PermanentMemoryHandler{
	public function getIngredients(?string $inNoteType):array{ return array();}
	public function getIngredientById(int $inID): ?Ingredient { return null; }
	public function getTranslatablesByTextID(int $inTextID):array{return array();}
	public function getCorrelationsFromIngredientID(int $inIngredientID) :array{return array();}
	
	public function getCorrelation(int $inIngredientID1, int $inIngredientID2) : Correlation
		{
		if(($inIngredientID1 == 2 && $inIngredientID2 == 3) || ($inIngredientID1 == 3 && $inIngredientID2 == 2))
			return new CorrelationForTest(0.5);
		if(($inIngredientID1 == 2 && $inIngredientID2 == 4) || ($inIngredientID1 == 4 && $inIngredientID2 == 2))
			return new CorrelationForTest(0.3);
		if(($inIngredientID1 == 4 && $inIngredientID2 == 3) || ($inIngredientID1 == 3 && $inIngredientID2 == 4))
			return new CorrelationForTest(0.1);
		if($inIngredientID1 == $inIngredientID2)
			return new CorrelationForTest(1);
		return new CorrelationForTest(0.2);
		}
}


$vIngredients = array($vIngredient1,$vIngredient2, $vIngredient3);
$vAmounts = array($vAmount1,$vAmount2, $vAmount3);
$vHandler = new PMHForTest();

$vCorrelationCalculator = new CorrelationCalculator($vIngredients, $vAmounts, "BASIC", $vHandler);
$vCorrelationCalculated = $vCorrelationCalculator->getCorrelation();
assert($vCorrelationCalculated < 0.38 && $vCorrelationCalculated> 0.37);
echo "choré : $vCorrelationCalculated  OK<br>";
// one candidate ingredient
echo "all tests OK <br>";

?>