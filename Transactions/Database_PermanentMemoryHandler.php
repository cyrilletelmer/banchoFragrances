<?php


class Database_PermanentMemoryHandler implements PermanentMemoryHandler
	{
	public $mPDO;
	public function __construct()
		{
		$this->mPDO = new PDO
			(
			'XXX', // host, database
			'XXX', // user goes here
			'XXX', // password goes here
			array
				(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_PERSISTENT => false
				)
			);
		}
		
		
	public function getIngredients(?string $inNoteType):array
		{
		$outData = array();
		$vReqBase = "SELECT * FROM Xproject_ingredients where ";
		$vNoteTypeRestriction = "";
		$vNbOfRestrictions = 0;
		if(isset($inNoteType))
			{
			$vNbOfRestrictions++;
			$vNoteTypeRestriction = " note_type = ? AND ";
			}
		$vStmt = $this->mPDO->prepare("SELECT * FROM Xproject_ingredients where $vNoteTypeRestriction 1;");
		
		$vI = 1;
		if($vNoteTypeRestriction!= "")
			{
			$vStmt->bindParam($vI, $inNoteType, PDO::PARAM_STR);
			$vI++;
			}
		//echo "id $inId";
		$vStmt->execute();
		$vRowNb=0;
		foreach ($vStmt as $vRow)
			{
				
			$vIngredient = new Ingredient();
			$vIngredient->mIngredientID = $vRow["iID"];
			$vIngredient->mNoteType = $vRow["note_type"];
			$vIngredient->mNameID = $vRow["name"];
			$vIngredient->mBlendingFactor = $vRow["blending_factor"];
			$vIngredient->mFreq = $vRow["freq"];
			$outData[$vRowNb]=$vIngredient;
			$vRowNb++;
			}
		return $outData;
		}
	
	public function getIngredientById(int $inID): ?Ingredient
		{
		$outData = null;
		$vStmt = $this->mPDO->prepare("SELECT * FROM Xproject_ingredients where iID = ?");
		//echo "id $inId";
		$vStmt->execute([$inID]);
		foreach ($vStmt as $vRow)
			{
				
			$outData = new Ingredient();
			$outData->mIngredientID = $vRow["iID"];
			$outData->mNoteType = $vRow["note_type"];
			$outData->mNameID = $vRow["name"];
			$outData->mBlendingFactor = $vRow["blending_factor"];
			$outData->mFreq = $vRow["freq"];

			}
		return $outData;
		}

		
	public function getTranslatablesByTextID(int $inTextID):array
		{
		$outData = array();
		$vStmt = $this->mPDO->prepare("SELECT * FROM Xproject_translatables where TextID = ?");
		//echo "id $inTextID";
		$vi=0;
		$vStmt->execute([$inTextID]);
		foreach ($vStmt as $vRow)
			{
				
			$vTranslatable = new Translatable();
			$vTranslatable->mTranslatableID = $vRow["TranslatableID"];
			$vTranslatable->mTextID = $vRow["TextID"];
			$vTranslatable->mLanguage = $vRow["Language"];
			$vTranslatable->mText = $vRow["TextStr"];
			$outData[$vi]=$vTranslatable;

			}
		return $outData;
			
		}
		
		
		
	public function getCorrelationsFromIngredientID(int $inIngredientID) :array{return array();}
	
	

	public function getCorrelation(int $inIngredientID1, int $inIngredientID2) : Correlation
		{
		$outData = new Correlation;
		$outData->mCorrelationID = 0;
		$outData->mIngredient1 = 0;
		$outData->mIngredient2 = 0;
		$outData->mCorrelationType = "FAILED";
		$outData->mValue =-2.0;
		$vStmt = $this->mPDO->prepare("SELECT * FROM Xproject_correlations where ingredient_one = ? AND ingredient_two = ? ;");
		//echo "id $inId";
		$vStmt->bindParam(1, $inIngredientID1, PDO::PARAM_INT);
		$vStmt->bindParam(2, $inIngredientID2, PDO::PARAM_INT);
		$vStmt->execute();
		foreach ($vStmt as $vRow)
			{
				
			$outData = new Correlation();
			$outData->mCorrelationID = $vRow["cID"];
			$outData->mIngredient1 = $vRow["ingredient_one"];
			$outData->mIngredient2 = $vRow["ingredient_two"];
			$outData->mCorrelationType = $vRow["correlation_type"];
			$outData->mValue = $vRow["value"];

			}
		return $outData;
		}
		
	}


	
?>




