<?php
/** object checking parameters of a request. */
interface ParameterChecker {
	
	/** returns a string explaining the error, or empty string of no error*/
	public function checkRequestParametersCompletion(array $inParameters, ?int $inResID): string;
	
}


?>