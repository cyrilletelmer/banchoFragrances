<?php
/** represents a request incoming from client */
class Request 
	{
	/** array of /separated elements in the url */
    public $mUrlElements;
	/** verb used by the client */
    public $mVerb;
	/** parameters of the GET or POST request, wether JSON or otherwise*/
    public $mParameters;
	
	private $mServerArray;

    public function __construct
		(
		$inServer
		) 
		{
		$this->mServerArray = $inServer;
        $this->mVerb = $inServer['REQUEST_METHOD'];
        $this->mUrlElements = explode('/', $inServer['PATH_INFO']);
		
		$this->parseIncomingParams();
        // initialise json as default format
        $this->format = 'json';
        if(isset($this->mParameters['format'])) 
			{
            $this->format = $this->mParameters['format'];
			}
		//echo "mVerbe ".$this->mVerb." <br>";
		//print_r($this->mUrlElements);
		//echo "parametres ".$this->mParameters." <br>";
		//print_r($this->mParameters);
        return true;
		}
		
		
	private function parseIncomingParams() 
		{
        $vParameters = array();

        // first of all, pull the GET vars
        if (isset($this->mServerArray['QUERY_STRING'])) 
			{
            parse_str($this->mServerArray['QUERY_STRING'], $vParameters);
			}

        // now how about PUT/POST bodies? These override what we got from GET
        $body = file_get_contents("php://input");
        $content_type = false;
        if(isset($this->mServerArray['CONTENT_TYPE'])) 
			{
            $content_type = $this->mServerArray['CONTENT_TYPE'];
			}
        switch($content_type) 
			{
            case "application/json":
                $body_params = json_decode($body);
                if($body_params) 
					{
                    foreach($body_params as $param_name => $param_value) 
						{
                        $vParameters[$param_name] = $param_value;
						}
					}
                $this->format = "json";
                break;
            case "application/x-www-form-urlencoded":
                parse_str($body, $postvars);
                foreach($postvars as $field => $value) 
					{
                    $vParameters[$field] = $value;
					}
                $this->format = "html";
                break;
            default:
                // we could parse other supported formats here
                break;
			}
        $this->mParameters = $vParameters;
		}
	}



?>