<?php
/**
 takes a data output array resulting from Transaction, and transform it into data that client will receive,
 preferably in a pretty format

 */
class OutputBeautifier
	{
	public function backToClient($inOutput)
		{
		echo json_encode($inOutput, JSON_PRETTY_PRINT);
		}
	}

?>