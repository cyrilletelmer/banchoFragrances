<?php

/** object able to render an Ingredient in the way it will be presented in the answer of the API call*/
class IngredientDisplayable {
	
	private Ingredient $mIngredient;
	private array $mTranslatableNames;
	private  ?array $mTranslatableAdjectives;
	
	public function __construct(Ingredient $inIngredient, array $inTranslatableNames, ?array $inTranslatableAdjectives)
		{
		$this->mIngredient = $inIngredient;
		$this->mTranslatableNames = $inTranslatableNames;
		$this->mTranslatableAdjectives = $inTranslatableAdjectives;
		}
		
	
	public function getDisplay() :array
		{
		$vIngredient = $this->mIngredient;
		$vNameDicTionnary = array();
		if(isset($this->mTranslatableNames))
			{
			foreach($this->mTranslatableNames as $vTranslatable)
				{
				$vNameDicTionnary[$vTranslatable->mLanguage] = $vTranslatable->mText;
				}
			}
		$vAdjectives = $this->extractTranslatableAdjectives($this->mTranslatableAdjectives);
		return array
			(
			IngredientDisplayableFields::ID => $vIngredient->mIngredientID,
			IngredientDisplayableFields::NOTE_TYPE => $vIngredient->mNoteType,
			IngredientDisplayableFields::BLENDING_FACTOR =>$vIngredient->mBlendingFactor,
			IngredientDisplayableFields::NAME => $vNameDicTionnary,
			IngredientDisplayableFields::FREQ =>$vIngredient->mFreq,
			IngredientDisplayableFields::ADJECTIVES =>$vAdjectives
			);
		}
	
	private function extractTranslatableAdjectives(?array $inAdjectives)
		{
		$outData = array();
		if(isset($inAdjectives))
			{
			$vPreviousTranslatable = null;
			$vi = 0;
			foreach($inAdjectives as $vTranslatable)
				{
				//echo "adj ".$vTranslatable->mText."<br>";
				$vNewAdjective = isset($vPreviousTranslatable) &&  $vTranslatable->mTextID != $vPreviousTranslatable->mTextID;
				if($vNewAdjective)
					{
					$vi++;
					$outData[$vi] = array();
					}
				if(!isset($outData[$vi]))
					$outData[$vi] = array();
				
				$outData[$vi][$vTranslatable->mLanguage]= $vTranslatable->mText;
				$vPreviousTranslatable = $vTranslatable;
				//echo "adj ".$vTranslatable->mText." vi ".$vi." new ".$vNewAdjective."<br>";
				}
			}
		return $outData;
		}
}

class IngredientDisplayableFields
	{
	const ID = "id";
	const NOTE_TYPE = "noteType";
	const BLENDING_FACTOR = "blendingFactor";
	const NAME ="name";
	const FREQ ="freq";
	const ADJECTIVES = "adjectives";
	}



?>