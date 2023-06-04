<?php
error_reporting(-1);
ini_set('display_errors', 'On');
set_error_handler("var_dump");

include_once("Request.php");
// this file holds factory functions necessary to create key objects in this webservice.

class ErrorCodes
	{
	const OK = 200;
	const UNKNOWN_REQUEST = 400;
	const INSERTION_ERROR = 409;
	const PARAMETER_PROBLEM = 422;
	const INCORRECT_CONFIRMATION_CODE =401;
	const GENERAL_ERROR = 500;
	const RESSOURCE_NOT_FOUND = 404;
	const RESSOURCE_NOT_AUTHORIZED = 403;
	const RESEND = 310;
	const AUTHENTICATION_NECESSARY = 401;
	const IMPERSONATION = 980;
	const REQUEST_ORDER = 981;
	
	const DEVELOPER_ERROR = 500;
	}

class DisplayableResourceRequested
	{
	const INGREDIENTS = "ingredients";
	const SMELL = "smell";
	const SUGGESTIONS = "suggestions";
	}



//=====================================
// main factories
//=====================================

/** Request creator */
function factory_Request ($inServer) : Request
	{
	$outRequestObject = new Request($inServer);
	return $outRequestObject;
	}
	
	
include_once("ParameterChecker.php");
	
	
include_once("Transaction.php");
include_once("Transactions/StubTransaction.php");
include_once("Transactions/GetIngredientsTransaction.php");
include_once("Transactions/GetSmellTransaction.php");
include_once("Transactions/GetSuggestionTransaction.php");
include_once("Transactions/Data/IngredientDisplayable.php");
include_once("Transactions/Data/SuggestionDisplayable.php");
include_once("Transactions/Data/WarningDisplayable.php");


/** Transaction creator.
this will distribute the various requests made by client towards their right concrete Transaction object.
*/
function factory_Transaction (Request $inRequest ) : Transaction
	{
	// default transaction in case we don't get what the client is asking:
	$outTransaction = new StubTransaction($inRequest->mVerb,$inRequest->mUrlElements, $inRequest->mParameters);
	
	//now actually create the right transaction according to verb, url, and parameters of the request:
	if($inRequest->mVerb == "GET")
		{
		if(count($inRequest->mUrlElements)<2)
			$aie="aie bon dieu";
		else if($inRequest->mUrlElements[1]==DisplayableResourceRequested::INGREDIENTS)
			$outTransaction = new GetIngredientsTransaction($inRequest->mVerb, $inRequest->mUrlElements, $inRequest->mParameters);
		else if($inRequest->mUrlElements[1]==DisplayableResourceRequested::SMELL)
			$outTransaction = new GetSmellTransaction($inRequest->mVerb, $inRequest->mUrlElements, $inRequest->mParameters);
		else if($inRequest->mUrlElements[1]==DisplayableResourceRequested::SUGGESTIONS)
			$outTransaction = new GetSuggestionTransaction($inRequest->mVerb, $inRequest->mUrlElements, $inRequest->mParameters);
		}
	return $outTransaction;
	}
	
include_once("OutputBeautifier.php");
/** data beautifier creator.
creates a OutputBeautifier object, that will take a data output  array from Transaction, and transform it into data that client will receive
*/
function factory_OutputBeautifier
	(
	String $inBeautyType
	)
	{
	return new OutputBeautifier();
	}
	
include_once("CommunicationWatchdog.php");
/** watchdog creator */
function factory_CommunicationWatchdog()
	{
	return new CommunicationWatchdog();
	}

include_once("Transactions/Model/Correlation.php");
include_once("Transactions/Model/Ingredient.php");
include_once("Transactions/Model/Translatable.php");
include_once("Transactions/Model/Adjective.php");
include_once("Transactions/Model/Warning.php");
include_once("PermanentMemoryHandler.php");
//include_once("Transactions/Dummy_PermanentMemoryHandler.php");
include_once("Transactions/Database_PermanentMemoryHandler.php");


function factory_PermanentMemoryHandler ()
	{
	//return new Dummy_PermanentMemoryHandler();
	return new Database_PermanentMemoryHandler();
	}
	
	
include_once("DateHandler.php");
include_once("Transactions/Utils/DateHandlerImpl.php");
include_once("Transactions/Utils/CorrelationCalculator.php");
include_once("Transactions/Utils/WarningIssuer.php");
include_once("Transactions/Utils/CorrelationWeightingStrategy.php");
include_once("Transactions/Utils/AdjectiveIssuer.php");

function factory_DateHandler() : DateHandler
	{
	return new DateHandlerImpl();
	}
	
function factory_CorrelationWeightingStrategy($inStrategyId = 0) : CorrelationWeightingStrategy
	{
	return new CorrelationWeightingStrategy();
	}
	
	
function factory_CorrelationCalculator
	(
	array $inIngredients,
	?array $inAmounts,
	string $inCorrelationType,
	PermanentMemoryHandler $inPMH,
	CorrelationWeightingStrategy $inWeightingStrategy
	) : CorrelationCalculator
	{
	return new CorrelationCalculator($inIngredients,$inAmounts,$inCorrelationType,$inPMH,$inWeightingStrategy);
	}

	
function factory_WarningIssuer(array $inIngredients, array $inAmounts, string $inWarningStrategy = "BASIC_WARNINGS")
	{
	return factory_WarningIssuerWithAdditionalStrategy(new WarningIssuer($inIngredients,$inAmounts), $inWarningStrategy);
	}
	
//compose warning issuers using decorators
function factory_WarningIssuerWithAdditionalStrategy(WarningIssuer $inWarningIssuerBase, string $inAdditionalStrategy)
	{
	switch($inAdditionalStrategy)
		{
		case "BASIC_WARNINGS" :
			return new BasicPyramidalCheckWarningIssuer($inWarningIssuerBase);
			break;
		case "PYRAMIDAL_BALANCE_WARNINGS" :
			return new PyramidalBalanceWarningIssuer($inWarningIssuerBase);
			break;
		case "INDIVIDUAL_DOSING_WARNINGS" :
			return new IndividualDosingWarningIssuer($inWarningIssuerBase);
			break;
		default:
			return $inWarningIssuerBase;
			break;
		}
	}
	
function factory_AdjectiveIssuer() : AdjectiveIssuer
	{
	return new AdjectiveIssuer();
	}

?>