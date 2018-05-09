<?php if ( ! defined('PATH_SYSTEM')) die ('Bad requested!');
 
class Index_Controller extends BK_Controller
{
    public function indexAction()
    {
    	if ($_POST['data'])
    	{
	    	$encoded_json = $_POST['data'];

	    	$received_json = base64_decode ($encoded_json);

	    	header("Content-Type: application/json; charset=UTF-8");

	    	$received_data = json_decode ($received_json);

	    	$output_data = $this->ProcessInput ($received_data);
	        
	        echo json_encode($output_data);
	    }
    }

    function ProcessInput($raw_data)
    {
    	return $raw_data;
    }
}