<?php
include_once("./factoring.php");

/** Main course behind every type of this API call */
abstract class Transaction
	{
		
	/** verb from client request */
	protected $mVerb;
	
	/** url array from client request */
	protected $mURLArray;
	
	/** client request params */
	protected $mRequestParameters;
	
	protected ParameterChecker $mParameterChecker ;
	
	protected abstract function createParameterChecker() : ParameterChecker;
	
	/** children transactions will do their job here*/
	protected abstract function onExecute(array $inRequestParameters, ?int $inResourceID) : array;
	
	
	/** 
	Main method of the class, this API call active principle will be happening here.
	@returns an value Array being the answer of the call. The calling code will have the responsibility to relay this answer to client in its preferred format (but JSON is smartest)
	*/
	public function execute() : array
		{
		$vResID = null;
		if(count($this->mURLArray)>=3 && is_numeric($this->mURLArray[2]))
			{
			$vResID = $this->mURLArray[2];
			}
		$vResultOfInitialParameterCheck = $this->mParameterChecker->checkRequestParametersCompletion($this->mRequestParameters,$vResID);
		if($vResultOfInitialParameterCheck!= "")
			return $this->createOutput(ErrorCodes::PARAMETER_PROBLEM, $vResultOfInitialParameterCheck, null);
		return $this->onExecute($this->mRequestParameters, $vResID);
		}
		

	private const ERROR_CODE = "errorCode";
	private const MESSAGE = "message";
	private const DATA = "data";
		
	//==========================
	// heritable toolbox:
	//==========================
	
	/** method of convenience to create the output array*/
	protected function createOutput($inErrorCode, $inMessage, $inData): array
		{
		$outData =array
			(
			Transaction::ERROR_CODE => $inErrorCode,
			Transaction::MESSAGE => "$inMessage",
			Transaction::DATA => $inData
			);
		return $outData;
		}
		
	protected function getPermanentMemoryHandler(): PermanentMemoryHandler
		{
		return $this->mPermanentMemoryHandler;
		}
		
		
	protected function hasOrNull($inData,$inKey) : ?string
		{
		return isset($inData[$inKey])?$inData[$inKey]:null;
		}
		
		
	//===============
	// constructor and private toolbox
	//===============
	private $mPermanentMemoryHandler;
	
	public function __construct
		(
		$inVerb, $inURLArray, $inParameters
		) 
		{
		//echo "transaction create";
		$this->mVerb 				= $inVerb;
		$this->mURLArray 			= $inURLArray;
		$this->mRequestParameters 	= $inParameters;
		$this->mParameterChecker 	= $this->createParameterChecker();
		$this->mPermanentMemoryHandler = factory_PermanentMemoryHandler();
		}
	}

?>