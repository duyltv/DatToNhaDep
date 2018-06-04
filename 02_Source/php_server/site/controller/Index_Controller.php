<?php if ( ! defined('PATH_SYSTEM')) die ('Bad requested!');

define("DEBUG", true);
 
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
	        
	        if (DEBUG)
	        	echo "{ \"input\": ".$received_json.", \"output\": ";
	        echo json_encode($output_data);

	        if (DEBUG)
	        	echo "}";
	    }
    }

    function ProcessInput($raw_data)
    {
    	if ($raw_data["mcode"])
    	{
    		if (isset($raw_data["session"]))
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

                case "check_moderator":
                    $session = $raw_data["session"];
                    return $this->CheckModerator($session);
                    break;

				case "add_moderator":
					$session = $raw_data["session"];
                    $user_id_mod = $raw_data["user_id_mod"];
					return $this->AddModerator($session, $user_id_mod);
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

				case "get_expand_content_define":
					$session = $raw_data["session"];
					$type_id = $raw_data["type_id"];
					return $this->GetExpandContentDefine($session, $type_id);
					break;

				case "add_expand_content_define":
					$session = $raw_data["session"];
					$type_id = $raw_data["type_id"];
					$expand_name = $raw_data["expand_name"];
					return $this->AddExpandContentDefine($session, $type_id, $expand_name);
					break;

				case "add_content":
					$session = $raw_data["session"];
					$title = $raw_data["title"];
					$content = $raw_data["content"]; // base64 content
					$address = $raw_data["address"];
					$stretch = $raw_data["stretch"];
					$price = $raw_data["price"];
					$priority = $raw_data["priority"];
					$date = date('d/m/Y', time());
					$expire = $raw_data["expire"];
					$images = $raw_data["images"];
					$type_id = $raw_data["type_id"];
					$expand_data = $raw_data["expand_data"];
					return $this->AddContent($session, $title, $content, $address, $stretch, $price, $priority, $date, $expire, $images, $type_id, $expand_data);
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
                                'role_id' => $member['role_id'],
                                'balance' => $member['balance'],
                                'validated' => $member['validaed'],
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
            'role_id' => 1,
            'balance' => 0,
            'is_mod' => 0,
            'session' => $this->generateRandomString(7),
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

    function check_moderator($user_id)
    {
    	if ($user_id == null)
    	{

    	}
    	else
    	{
    		$this->model->load('member');
    		$mod_list = $this->model->get_moderator_list();

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

    function CheckModerator($session)
    {
        $user_id = $this->check_valid_session($session);

        if ($user_id == null)
        {

        }
        else
        {
            $this->model->load('member');

            if ($this->check_moderator($user_id))
            {
                return array (  "mcode" => "check_moderator",
                                "status" => "error",
                                "data" => "existed"
                            );
            }
        }

        return array (  "mcode" => "check_moderator",
                        "status" => "error",
                        "data" => "fail"
                    );
    }

    function AddModerator($session, $user_id_mod)
    {
    	$user_id = $this->check_valid_session($session);

    	if ($user_id == null)
    	{

    	}
    	else
    	{
    		$this->model->load('member');

            if (!$this->check_moderator($user_id))
            {
                return array (  "mcode" => "add_moderator",
                                "status" => "error",
                                "data" => "permission_denied"
                            );
            }

    		if ($this->check_moderator($user_id_mod))
    		{
    			return array (	"mcode" => "add_moderator",
		    					"status" => "error",
		    					"data" => "existed"
		    				);
    		}

        	$data = array(
	            'is_mod' => "1"
	        );

	        if ($this->model->update_manual('member', $data, "user_id=\"".$user_id_mod."\""))
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
		$this->model->load('content_type');
		$content_type = $this->model->get('content_type');

		return array (	"mcode" => "get_content_type_list",
    					"status" => "success",
    					"data" => $content_type
    				);
    }

    function GetExpandContentDefine($session, $type_id)
    {
		$this->model->load('expand_content_define');
		$expand_content_define = $this->model->get_expand_content_define($type_id);

		return array (	"mcode" => "get_expand_content_define",
    					"status" => "success",
    					"data" => $expand_content_define
    				);
    }

    function AddExpandContentDefine($session, $type_id, $expand_name)
    {
    	$user_id = $this->check_valid_session($session);
    	if ($user_id == null)
    	{
    		return array (	"mcode" => "add_expand_content_define",
	    					"status" => "error",
	    					"data" => "fail"
	    				);
    	}
    	else
    	{
    		$this->model->load('expand_content_define');
    		$expand_content_define_list = $this->model->get("expand_content_define");

    		foreach ($expand_content_define_list as $expand_content_define) {
    			if ($expand_content_define['expand_name'] == $expand_name)
    				return array (	"mcode" => "add_expand_content_define",
			    					"status" => "error",
			    					"data" => "existed"
			    				);
    		}

    		$data = array(
	            'type_id' => $type_id,
	            'expand_name' => $expand_name
	        );

	        if ($this->model->insert('expand_content_define', $data))
	        	return array (	"mcode" => "add_expand_content_define",
		    					"status" => "success"
		    				);

	        return array (	"mcode" => "add_expand_content_define",
	    					"status" => "error",
	    					"data" => "fail"
	    				);
    	}
    }

    // images is an array of base64 image
    // expand_data is an array
    function AddContent($session, $title, $content, $address, $stretch, $price, $avatar, $priority, $date, $expire, $images, $type_id, $expand_data)
    {
    	$user_id = $this->check_valid_session($session);
    	if ($user_id == null)
    	{
    		return array (	"mcode" => "add_content",
	    					"status" => "error",
	    					"data" => "fail"
	    				);
    	}
    	else
    	{
    		$this->model->load('content');

    		$data = array(
	            "title" => $title,
	            "content" => $content,
	            "address" => $address,
	            "stretch" => $stretch,
	            "price" => $price,
                "avatar" => $avatar,
	            "priority" => $priority,
	            "status" => 0,
	            "date" => $date,
	            "expiredate" => $expire,
	            "user_id" => $user_id,
	            "type_id" => $type_id
	        );

	        if (! $this->model->insert('content', $data))
	        	return array (	"mcode" => "add_content",
		    					"status" => "error",
		    					"data" => "cannot_insert_content"
		    				);

	        $content_id = $this->model->conn->insert_id;

	        $this->model->load('expand_content');
	        foreach ($expand_data as $expand) {
	        	$data = array(
	        		"expand_id" => $expand["expand_id"],
	        		"expand_content" => $expand["expand_content"]
	        	);

	        	if (! $this->model->insert('expand_content', $data))
		        	return array (	"mcode" => "add_content",
			    					"status" => "error",
			    					"data" => "cannot_insert_expand_content"
			    				);
	        }

	        $this->model->load('images');
	        foreach ($images as $image) {
	        	$image_raw = base64_decode($image);
	        	$filename = $this->generateRandomString(7).".png";
	        	$filepath = './images/'.$filename;
				file_put_contents($filepath, $image_raw);

				$data = array(
					"image_url" => $filepath,
					"content_id" => $content_id
				);

				if (! $this->model->insert('images', $data))
		        	return array (	"mcode" => "add_content",
			    					"status" => "error",
			    					"data" => "cannot_insert_image"
			    				);
	        }

	        return array (	"mcode" => "add_content",
	    					"status" => "success"
	    				);
    	}
    }

    function GetContentList($content_id)
    {

    }
}