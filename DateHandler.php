<?php


interface DateHandler
	{
		
	public function checkDate (string $inDate) : bool;
	
	public function checkDateInPast (string $inDate) : bool;
	
	public function checkDateInFuture (string $inDate) : bool;
	
	public function toTimestamp(string $inDate) : int;
	
	public function fromTimestamp(int $inTS) : string;
	}

?>