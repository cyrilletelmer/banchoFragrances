<?php
include_once("factoring.php");

//var_dump($_SERVER);

//taking the client request
$vReq= factory_Request($_SERVER);
$vBeautifulDataDisplayer = factory_OutputBeautifier("PLAIN_JSON");

//verify that client is not trying to screw us up
$vWatchdog = factory_CommunicationWatchdog();
$vWatchdog->checkTheRequest($vReq);

//processing the client request
$vTransaction =factory_Transaction($vReq);
$vCallResult = $vTransaction->execute();

//answering to client with style
$vBeautifulDataDisplayer->backToClient( $vCallResult);
?>