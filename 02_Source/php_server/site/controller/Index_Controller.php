<?php if ( ! defined('PATH_SYSTEM')) die ('Bad requested!');
 
class Index_Controller extends BK_Controller
{
	function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

    public function indexAction()
    {
    	if ($_POST['data'])
    	{
	    	$encoded_json = $_POST['data'];

	    	$received_json = base64_decode ($encoded_json);

	    	header("Content-Type: application/json; charset=UTF-8");

	    	$received_data = json_decode ($received_json, true);

	    	$output_data = $this->ProcessInput ($received_data);
	        
	        echo json_encode($output_data);
	    }
    }

    function ProcessInput($raw_data)
    {
    	if ($raw_data["mcode"])
    	{
    		switch ($raw_data["mcode"]) {
				case "login":
					$email = $raw_data["email"];
					$pass = $raw_data["password"];
					return $this->Login ($email, $pass);
					break;

				case "check_login":
					$user_id = $raw_data["user_id"];
					$session = $raw_data["session"];
					return $this->CheckLogin ($user_id, $session);
					break;
					
				default:
					return array (	"mcode" => "login",
			    					"status" => "error"
			    				);
			}
    	}
    	return array (	"mcode" => "error",
    					"status" => "error"
    				);
    }

    function Login($email, $pass)
    {
    	$this->model->load('member');
    	$members = $this->model->get('member');
    	foreach($members as $member)
        {
            if($member['email'] == $email && $member['password'] == $pass) 
            {
            	$session = $this->generateRandomString(7);
            	$user_id = $member['user_id'];

            	$update_data = array (
            					'session' => $session
            					);

            	$this->model->update_manual('member', $update_data, 'user_id='.$user_id);

            	$return_data = array (
            					'user_id' => $user_id,
            					'session' => $session
            						);

            	return array (	"mcode" => "login",
		    					"status" => "success",
		    					"data" => $return_data
		    					);
            }
        }

    	return array (	"mcode" => "login",
    					"status" => "error"
    				);
    }

    function CheckLogin($user_id, $session)
    {
    	$this->model->load('member');
    	$members = $this->model->get('member');

    	foreach($members as $member)
        {
            if($member['user_id'] == $user_id && $member['session'] == $session) 
            {
            	return array (	"mcode" => "check_login",
		    					"status" => "success"
		    				);
            }
        }

        return array (	"mcode" => "check_login",
    					"status" => "error"
    				);
    }
}