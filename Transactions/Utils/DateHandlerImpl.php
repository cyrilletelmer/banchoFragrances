<?php
/** object checking date-related things */
class DateHandlerImpl implements DateHandler
	{
		
	const FORMAT_DATE = 'd-m-Y H:i';
	public function checkDate (string $inDate) : bool
		{
		$date = DateTime::createFromFormat(DateHandlerImpl::FORMAT_DATE, $inDate);
		return $date !== false && !array_sum($date::getLastErrors());	
		}
		
	public function checkDateInPast (string $inDate) : bool
		{
		if(!$this->checkDate($inDate))
			return false;
		$vDateToCheck = DateTime::createFromFormat(DateHandlerImpl::FORMAT_DATE, $inDate);
		$vRightNow = new DateTime();
		return $vDateToCheck<=$vRightNow;
		}
		
	public function checkDateInFuture (string $inDate) : bool
		{
		if(!$this->checkDate($inDate))
			return false;
		$vDateToCheck = DateTime::createFromFormat(DateHandlerImpl::FORMAT_DATE, $inDate);
		$vRightNow = new DateTime();
		return $vDateToCheck>=$vRightNow;
		}
		
	public function toTimestamp(string $inDate) : int
		{
		if(!$this->checkDate($inDate))
			return -1;
		$vDateToTransform = DateTime::createFromFormat(DateHandlerImpl::FORMAT_DATE, $inDate);
		return $vDateToTransform->getTimestamp();
		}
		
	public function fromTimestamp(int $inTS) : string
		{
		return date(DateHandlerImpl::FORMAT_DATE, $inTS);
		}

	}

?>