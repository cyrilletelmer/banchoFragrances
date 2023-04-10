<?php

/** object able to render an Ingredient in the way it will be presented in the answer of the API call*/
class IngredientDisplayable {
	
	private Ingredient $mIngredient;
	private array $mTranslatableNames;
	
	public function __construct(Ingredient $inIngredient, ?array $inTranslatableNames, ?array $inTranslatableAdjectives)
		{
		$this->mIngredient = $inIngredient;
		$this->mTranslatableNames = $inTranslatableNames;
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
		$vAdjectives = array();
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