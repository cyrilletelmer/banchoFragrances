<?php
include_once("Transactions/Model/Ingredient.php");

/** handling all the  permanent memory CRUD (hides database stuff from rest of code) */
interface PermanentMemoryHandler
	{
	
	
	public function getIngredients(?string $inNoteType):array;
	
	public function getIngredientById(int $inID): ?Ingredient;
	
	public function getTranslatablesByTextID(int $inTextID):array;
	
	public function getCorrelationsFromIngredientID(int $inIngredientID) :array;
	
	public function getCorrelation(int $inIngredientID1, int $inIngredientID2) : Correlation;
	}
	



?>