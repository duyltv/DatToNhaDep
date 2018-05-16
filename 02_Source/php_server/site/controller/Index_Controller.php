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
    		if ($raw_data["session"])
    		{
    			if ($raw_data["session"] == "")
    				return array (	"mcode" => $raw_data["mcode"],
			    					"status" => "error",
			    					"data" => "session_empty"
			    				);
    		}

    		switch ($raw_data["mcode"]) {
				case "login":
					$email = $raw_data["email"];
					$pass = $raw_data["password"]; // MD5 encoded
					return $this->Login ($email, $pass);
					break;

				case "check_login":
					$user_id = $raw_data["user_id"];
					$session = $raw_data["session"];
					return $this->CheckLogin ($user_id, $session);
					break;

				case "logout":
					$user_id = $raw_data["user_id"];
					$session = $raw_data["session"];
					return $this->Logout ($user_id, $session);
					break;

				case "register":
					$name = $raw_data["name"];
					$phone = $raw_data["phone"];
					$email = $raw_data["email"];
					$password = $raw_data["password"]; // MD5 encoded
					$avatar = $raw_data["avatar"]; // path to image
					$address = $raw_data["address"];
					return $this->Register($name, $phone, $email, $password, $avatar, $address);
					break;

				case "add_moderator":
					$session = $raw_data["session"];
					return $this->AddModerator($session);
					break;

				case "add_content_type":
					$session = $raw_data["session"];
					$type_name = $raw_data["type_name"];
					return $this->AddContentType($session, $type_name);
					break;

				case "get_content_type_list":
					$session = $raw_data["session"];
					return $this->GetContentTypeList($session);
					break;

				default:
					return array (	"mcode" => "error",
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
            	if($member['validated'] == "0")
            	{
            		return array (	"mcode" => "login",
			    					"status" => "error",
			    					"data" => "not_validated"
			    				);
            	}

            	$session = $this->generateRandomString(7);
            	$user_id = $member['user_id'];

            	$update_data = array (
            					'session' => $session
            					);

            	$this->model->update_manual('member', $update_data, 'user_id='.$user_id);

            	$return_data = array (
            					'user_id' => $user_id,
            					'email' => $member['email'],
            					'phone' => $member['phone'],
            					'avatar' => $member['avatar'],
            					'address' => $member['address'],
            					'session' => $session
            						);

            	return array (	"mcode" => "login",
		    					"status" => "success",
		    					"data" => $return_data
		    					);
            }
        }

    	return array (	"mcode" => "login",
    					"status" => "error",
    					"data" => "fail"
    				);
    }

    function CheckLogin($user_id, $session)
    {
    	$this->model->load('member');
    	$members = $this->model->get('member');

    	foreach($members as $member)
        {
            if($member['user_id'] == $user_id && $member['session'] == $session && $member['session'] != '') 
            {
            	return array (	"mcode" => "check_login",
		    					"status" => "success"
		    				);
            }
        }

        return array (	"mcode" => "check_login",
    					"status" => "error",
    					"data" => "fail"
    				);
    }

    function Logout($user_id, $session)
    {
    	$this->model->load('member');
    	$members = $this->model->get('member');

    	foreach($members as $member)
        {
            if($member['user_id'] == $user_id && $member['session'] == $session && $member['session'] != '') 
            {
            	$update_data = array (
            					'session' => ''
            					);

            	$this->model->update_manual('member', $update_data, 'user_id='.$user_id);

            	return array (	"mcode" => "logout",
		    					"status" => "success"
		    				);
            }
        }

        return array (	"mcode" => "logout",
    					"status" => "error",
    					"data" => "fail"
    				);
    }

    function Register($name, $phone, $email, $password, $avatar, $address)
    {
    	$this->model->load('member');
    	$members = $this->model->get('member');

    	// Check if user existed
    	foreach($members as $member)
        {
            if($member['phone'] == $phone) 
            {
            	return array (	"mcode" => "register",
		    					"status" => "error",
		    					"data" => "phone_exist"
		    				);
            }
            elseif ($member['email'] == $email) 
            {
            	return array (	"mcode" => "register",
		    					"status" => "error",
		    					"data" => "email_exist"
		    				);
            }
        }

        // Add user to database
        $data = array(
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'password' => $password,
            'avatar' => $avatar,
            'address' => $address,
            'session' => generateRandomString(7),
            'validated' => 0
        );

        if ($this->model->insert('member', $data))
        {
        	return array (	"mcode" => "register",
	    					"status" => "success"
	    				);
        }

        return array (	"mcode" => "register",
    					"status" => "error",
    					"data" => "data_error"
    				);
    }

    function Validate($email, $session)
    {
    	$this->model->load('member');
    	$members = $this->model->get('member');    	$members = $this->model->get('member');
    	// Check if user existed
    	foreach($members as $member)
        {
            if($member['email'] == $email) 
            {
            	if ($member['session'] == $session)
            	{
            		$update_data = array (
	            					'session' => '',
	            					'validated' => 1
	            					);

            		$this->model->update_manual('member', $update_data, 'user_id='.$member['user_id']);
            	}

            	break;
            }
        }
    }

    /*
     * 	SUBFUNCTIONS
     *  START
     */
    function check_valid_session($session)
    {
    	$this->model->load('member');
    	$members = $this->model->check_valid_session($session);

    	if (empty($members))
    	{
    		return null;
    	}
    	else 
    	{
    		$valid_member = $members[0];
    		return $valid_member['user_id'];
    	}
    }

    function check_moderator($session)
    {
    	$user_id = $this->check_valid_session($session);

    	if ($user_id == null)
    	{

    	}
    	else
    	{
    		$this->model->load('moderator');
    		$mod_list = $this->model->get('moderator');

    		foreach($mod_list as $mod)
        	{
        		if ($mod['user_id'] == $user_id)
        			return true;
        	}
    	}

    	return false;
    }

    /*
     * 	SUBFUNCTIONS
     *  END
     */

    function AddModerator($session)
    {
    	$user_id = $this->check_valid_session($session);

    	if ($user_id == null)
    	{

    	}
    	else
    	{
    		$this->model->load('moderator');

    		if ($this->check_moderator($session))
    		{
    			return array (	"mcode" => "add_moderator",
		    					"status" => "error",
		    					"data" => "existed"
		    				);
    		}

        	$data = array(
	            'user_id' => $user_id
	        );

	        if ($this->model->insert('moderator', $data))
	        	return array (	"mcode" => "add_moderator",
		    					"status" => "success"
		    				);

	        return array (	"mcode" => "add_moderator",
	    					"status" => "error",
	    					"data" => "fail"
	    				);
    	}

    	return array (	"mcode" => "add_moderator",
    					"status" => "error",
    					"data" => "fail"
    				);
    }

    function AddContentType($session, $type_name)
    {
    	if ($this->check_moderator($session))
    	{
    		$this->model->load('content_type');
    		$content_type_list = $this->model->get('content_type');

    		foreach ($content_type_list as $content_type) {
    			if ($content_type['type_name'] == $type_name)
    				return array (	"mcode" => "add_content_type",
			    					"status" => "error",
			    					"data" => "existed"
			    				);
    		}

    		$data = array(
	            'type_name' => $type_name
	        );

	        if ($this->model->insert('content_type', $data))
	        	return array (	"mcode" => "add_content_type",
		    					"status" => "success"
		    				);

	        return array (	"mcode" => "add_content_type",
	    					"status" => "error",
	    					"data" => "fail"
	    				);
    	}

    	return array (	"mcode" => "add_content_type",
    					"status" => "error",
    					"data" => "fail"
    				);
    }

    function GetContentTypeList($session)
    {
    	$user_id = $this->check_valid_session($session);
    	if ($user_id == null)
    	{
    		return array (	"mcode" => "get_content_type_list",
	    					"status" => "error",
	    					"data" => "fail"
	    				);
    	}
    	else
    	{
    		$this->model->load('content_type');
    		$content_type = $this->model->get('content_type');

    		return array (	"mcode" => "get_content_type_list",
	    					"status" => "success",
	    					"data" => $content_type
	    				);
    	}
    }
}