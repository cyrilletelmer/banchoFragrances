<?php
//include_once("../factoring.php");
//include_once("../PermanentMemoryHandler.php");
class StubTransaction extends Transaction
	{
	//@Override
	public function onExecute(array $inReqParameters, ?int $inResID): array
		{
		$vOutArray =array();
		
		return $this->createOutput(ErrorCodes::UNKNOWN_REQUEST, "stub of GET ingredient request ", null);
		}
		
	protected function createParameterChecker() : ParameterChecker
		{
		return new StubTransactionParameterCheck();
		}
	}
	
class StubTransactionParameterCheck implements ParameterChecker
	{
	public function checkRequestParametersCompletion(array $inParameters, ?int $inResID): string
		{
		return "";
		}
	}


?>