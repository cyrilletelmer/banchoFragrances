<?php

/** object able to describe a recipe using adjectives */
class AdjectiveIssuer
	{
	
	private array $mDictionnaryOfAdjectivesPosition;
	private array $mListOfMultilingualAdjectives;
	
	public function __construct()
		{
		$this->mDictionnaryOfAdjectivesPosition = array();
		$this->mListOfMultilingualAdjectives = array();
		}
	
	public function addIngredient(IngredientDisplayable $inDisplayableIngredient, int $inAmount)
		{
		$vDisplay = $inDisplayableIngredient->getDisplay();
		$vBlendingFactor = $inDisplayableIngredient->mIngredient->mBlendingFactor;
		if($vBlendingFactor !=0)
			$vStrengthOfIngredient =  $inAmount/$vBlendingFactor;
		else
			$vStrengthOfIngredient = 0;
		foreach($inDisplayableIngredient->getDisplay()[IngredientDisplayableFields::ADJECTIVES] as $vWord)
			{
			//echo "word ".$vWord["fr"]." blending f of ingredient ".$vBlendingFactor.", amount ".$inAmount.", strength of ingredient ".$vStrengthOfIngredient."<br>";
			if(isset($this->mDictionnaryOfAdjectivesPosition[$vWord["fr"]]))
				{
				//we know this word.
				$vPosition = $this->mDictionnaryOfAdjectivesPosition[$vWord["fr"]];
				//we add strength to it
				$this->mListOfMultilingualAdjectives[$vPosition]->addStrength($vStrengthOfIngredient);
				//change its position: swap until finding the right place
				$vOurGuy = $this->mListOfMultilingualAdjectives[$vPosition];
				
				$vLengthofList = count($this->mListOfMultilingualAdjectives);
				while($vPosition-1>=0 && $vOurGuy->mStrength > $this->mListOfMultilingualAdjectives[$vPosition-1]->mStrength)
					{
					$vGuyJustAbove = $this->mListOfMultilingualAdjectives[$vPosition-1];
					$this->mListOfMultilingualAdjectives[$vPosition-1] = $vOurGuy;
					$this->mListOfMultilingualAdjectives[$vPosition] = $vGuyJustAbove;
					//echo "---swap : putting our guy ".$vOurGuy->mMultilingual["fr"]." at ".($vPosition-1)."<br>";
					//echo "---swap : putting other guy ".$vGuyJustAbove->mMultilingual["fr"]." at ".($vPosition)."<br>";
					$this->mDictionnaryOfAdjectivesPosition[$vWord["fr"]] = $vPosition-1;
					$this->mDictionnaryOfAdjectivesPosition[$vGuyJustAbove->mMultilingual["fr"]] = $vPosition;
					$vPosition--;
					}
				
				//echo "relocated at ".$vPosition.", ourguy ".$vOurGuy->mMultilingual["fr"]." name ".$this->mListOfMultilingualAdjectives[$vPosition]->mMultilingual["fr"]."strength ".$this->mListOfMultilingualAdjectives[$vPosition]->mStrength."<br>";
				//we have relocated the word taking into account its new strength
				}
			else
				{
				//this is a new word we have to insert in the priority list
				$vAdj = new BestAdjective($vWord,$vStrengthOfIngredient);
				$vPosition = $this->insert3($this->mListOfMultilingualAdjectives, $vAdj);
				//$this->mDictionnaryOfAdjectivesPosition[$vWord["fr"]] = $vPosition;
				//update positiononing dictionnary for the adjectives that were displaced by the insertion
				for($vi = $vPosition; $vi < count($this->mListOfMultilingualAdjectives); $vi++)
					{
					$vCurrentAdjective =$this->mListOfMultilingualAdjectives[$vi];
					$this->mDictionnaryOfAdjectivesPosition[$vCurrentAdjective->mMultilingual["fr"]] = $vi;
					}
				//echo "put in place ".$vPosition." our guy ".$this->mListOfMultilingualAdjectives[$vPosition]->mMultilingual["fr"]."<br>";
				}
			}
		}
		
	public function calculateBestAdjectives(int $inMaxNbOfAdjectivesToIssue = 10) : array //(of BestAdjectives)
		{
		$outFinalArray = array();
		$vi =0;
		foreach($this->mListOfMultilingualAdjectives as $vAdj)
			{
			if($vi>=$inMaxNbOfAdjectivesToIssue)
				break;
			//$vMultilingualWord = $vAdj->mMultilingual;
			//$vMultilingualWord["strength"] = $vAdj->mStrength;
			$outFinalArray[$vi] =$vAdj;
			$vi++;
			}
		return $outFinalArray;
		}
		
		
	private function insert3(&$arr, BestAdjective $elem) : int
		{
		if(count($arr)==0)
			{
			$arr[0] = $elem;
			return 0;
			}
		$startIndex = 0;
		$stopIndex = count($arr) - 1;
		$middle = 0;
		while($startIndex < $stopIndex)
			{
			$middle = ceil(($stopIndex + $startIndex) / 2);
			if($elem->mStrength > $arr[$middle]->mStrength)
				$stopIndex = $middle - 1;
			else if($elem->mStrength <= $arr[$middle]->mStrength)
				$startIndex = $middle;
			}
		$offset = $elem->mStrength >= $arr[$startIndex]->mStrength ? $startIndex : $startIndex + 1; 
		array_splice($arr, $offset, 0, array($elem));
		return $offset;
		}
		
		
	private function createIngreidentBundle(IngredientDisplayable $inDisplayableIngredient, int $inAmount) :IngredientBundle
		{
		return new IngredientBundle($inDisplayableIngredient, $inAmount);
		}
	}
	
	
/** an adjective with its strength ie how relevant it is to describe a smell */
class BestAdjective
	{
	public function __construct(array $inMultilingualWord, int $inStrength)
		{
		$this->mMultilingual = $inMultilingualWord;
		$this->mStrength = $inStrength;
		}
	public array $mMultilingual;
	public float $mStrength;
	public function addStrength(float $inStrength)
		{
		$this->mStrength += $inStrength;
		}
	}
	



?>