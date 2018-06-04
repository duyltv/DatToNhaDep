<?php
 
class BK_Model_Loader
{
    public $conn = NULL;
    /**
     * Load model
     *
     * @param   string
     * @desc    hàm load model, tham số truyền vào là tên của model
     */
    public function load($model)
    {
        ob_start();
        require_once PATH_APPLICATION . '/model/' . $model . '.php';
        $content = ob_get_contents();
        ob_end_clean();
		
		// Open SQL session
		$this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if (!$this->conn) {
			die('Could not connect: ' . mysqli_connect_error());
		}
		//mysqli_set_charset('utf8',$link);
		//@mysqli_select_db(DB_NAME) or die( "Unable to select database");
    }

    function mysqli_query_internal($connection, $query)
    {
    	if (DEBUG)
    	{
    		$string = trim(preg_replace('/\s\s+/', ' ', $query));
    		//echo "\"Query\": \"".base64_encode($string)."\",";
    	}

    	return mysqli_query($connection,$query);
    }
	
	/**
     * Search
     *
     * @desc    Hàm cho phép get nội dung table được filter content
	 *			Hàm trả về array 2 chiều. Sử dụng như trong example file Controller.
     */
    public function search($model, $input, $field = 'content')
    {
        $query = "SELECT * FROM $model WHERE $field LIKE '%".$input."%'";
		$result_q = $this->mysqli_query_internal($this->conn,$query);
		
		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}
		
		return $result;
    }
     
    /**
     * Insert
     *
     * @desc    Hàm cho phép insert nội dung table
     */
    public function insert($model, $data = array())
    {
        $this->load($model);
		
		// Write content to Database
		$keys = array_keys($data);
		$values = array_values($data);
		$mat_keys=implode(",",$keys);
		$mat_values=implode("','",$values);
		
		$query = "INSERT INTO $model ($mat_keys) VALUES ('$mat_values')";
		
		return $this->mysqli_query_internal($this->conn,$query);
    }
	
	/**
     * Get
     *
     * @desc    Hàm cho phép get nội dung table
	 *			Hàm trả về array 2 chiều. Sử dụng như trong example file Controller.
     */
    public function get($model, $id = null )
	{
		$query = "SELECT * FROM $model ";
		if( !is_null($id) ) 
			$query .= "WHERE id = '$id'";

		$result_q = $this->mysqli_query_internal($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}
	/*
 	 * Get_count
	 * @desc    Hàm cho phép get so luong record table
	 *			Hàm trả về so luong laf mang 2 chieu. Sử dụng như trong example file Controller.
	 */
	public function get_count($model, $cond)
	{
		if (!isset($cond))
			$query = "SELECT COUNT(*) as soluong FROM $model";
		else
			$query = "SELECT COUNT(*) as soluong FROM $model WHERE $cond";
		$result_q = $this->mysqli_query_internal($this->conn,$query);
		//echo $query;
		$result = array();

		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result = $row;
		}

		return $result;
	}
	/*
	 * Get_có điều kiện
	 * @desc    Hàm cho phép get record table
	 *			Hàm trả về so luong laf mang 2 chieu. Sử dụng như trong example file Controller.
	 */
	public function get_condition($model, $cond)
	{
		$query = "SELECT * FROM $model WHERE $cond";
		$result_q = $this->mysqli_query_internal($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}
	
	/**
     * Update
     *
     * @desc    Hàm cho phép edit nội dung table
     */
    public function update($model, $data = array())
    {
        $this->load($model);
		
		// Update content to Database
		$keys = array_keys($data);
		$values = array_values($data);
		$query = "UPDATE $model SET ";
		
		$max = sizeof($data);
		for ($x = 0; $x < $max-1; $x++) {
			$query = $query . "$keys[$x] = '$values[$x]', ";
		} 
		$max_1 = $max-1;
		$query = $query . "$keys[$max_1] = '$values[$max_1]' ";
		$query = $query . "WHERE id = '$values[0]'";

		return $this->mysqli_query_internal($this->conn,$query);
    }

    /**
     * Update
     *
     * @desc    Hàm cho phép edit nội dung table
     */
    public function update_manual($model, $data = array(), $condition="")
    {
        $this->load($model);
		
		// Update content to Database
		$keys = array_keys($data);
		$values = array_values($data);
		$query = "UPDATE $model SET ";
		
		$max = sizeof($data);
		for ($x = 0; $x < $max-1; $x++) {
			$query = $query . "$keys[$x] = '$values[$x]', ";
		} 
		$max_1 = $max-1;
		$query = $query . "$keys[$max_1] = '$values[$max_1]' ";
		$query = $query . "WHERE " . $condition;

		return $this->mysqli_query_internal($this->conn,$query);
    }
	
	/**
     * Delete
     *
     * @desc    Hàm cho phép delete nội dung table
     */
    public function delete($model, $data = array())
    {
        $this->load($model);
		
		// Update content to Database
		$keys = array_keys($data);
		$values = array_values($data);
		$query = "DELETE FROM $model WHERE ";
		
		$max = sizeof($data);
		for ($x = 0; $x < $max-1; $x++) {
			$query = $query . "$keys[$x] = '$values[$x]' AND ";
		} 
		$max_1 = $max-1;
		$query = $query . "$keys[$max_1] = '$values[$max_1]' ";

		return $this->mysqli_query_internal($this->conn,$query);
    }


    /*
     * ADVANCED FUNCTIONS
     *	
     * These funcs are used to do the advanced jobs that don't need
     * much resources
     *
     */

   	public function check_valid_session($session = "")
	{
		if ($session == "")
		{
			return [];
		}

		if ($this->conn == NULL)
		{
			$this->load('member');
		}

		$query = "select * 
		         from member
		         where session=\"".$session."\"";
		$result_q = $this->mysqli_query_internal($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}

	public function get_moderator_list()
	{
		if ($this->conn == NULL)
		{
			$this->load('member');
		}

		$query = "select * 
		         from member
		         where is_mod=\"1\"";
		$result_q = $this->mysqli_query_internal($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}

	public function get_expand_content_define($type_id)
	{
		if ($type_id == "")
		{
			return [];
		}

		if ($this->conn == NULL)
		{
			$this->load('expand_content_define');
		}

		$query = "select * 
		         from expand_content_define
		         where type_id=\"".$type_id."\"";
		$result_q = $this->mysqli_query_internal($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}

	public function get_content_list_by_type($type_id)
	{
		if ($type_id == "")
		{
			return [];
		}

		if ($this->conn == NULL)
		{
			$this->load('content');
		}

		$query = "select content_id, title, avatar, stretch, price, priority, status, date, expiredate, type_id 
		         from content
		         where type_id=\"".$type_id."\"
		         and CURDATE()>=STR_TO_DATE(date, '%d/%m/%Y')
		         and CURDATE()<=STR_TO_DATE(expiredate, '%d/%m/%Y')
		         and status=\"1\"
		         order by priority DESC";

		$result_q = $this->mysqli_query_internal($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}

	public function get_content_list_by_user($user_id)
	{
		if ($user_id == "")
		{
			return [];
		}

		if ($this->conn == NULL)
		{
			$this->load('content');
		}

		$query = "select content_id, title, avatar, stretch, price, priority, status, date, expiredate, type_id 
		         from content
		         where user_id=\"".$user_id."\"
		         and status=\"1\"
		         order by priority DESC";

		$result_q = $this->mysqli_query_internal($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}

	public function get_content_list_owner($user_id)
	{
		if ($user_id == "")
		{
			return [];
		}

		if ($this->conn == NULL)
		{
			$this->load('content');
		}

		$query = "select content_id, title, avatar, stretch, price, priority, status, date, expiredate, type_id 
		         from content
		         where user_id=\"".$user_id."\"";

		$result_q = $this->mysqli_query_internal($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
	}

	private function get_content($role_id, $content_id)
	{
		if ($content_id == "")
		{
			return [];
		}

		if ($this->conn == NULL)
		{
			$this->load('content');
		}

		$query = "select content.* from
				(select * from roles_on_type where role_id=\"".$role_id."\" and (role_code=\"1\" or role_code=\"3\")) as role
				join
				(select * 
		         from content
		         where content_id=\"".$content_id."\") as content
		        on role.type_id=content.content_id";

		$result_q = $this->mysqli_query_internal($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$query_row = "select definition.expand_name, definition.measure_unit, content.expand_content from
				(select expand_id, expand_name, measure_unit from expand_content_define where type_id=\"".$row["type_id"]."\") as definition
				join
				(select expand_id, expand_content from expand_content where content_id=\"".$content_id."\") as content
				ON definition.expand_id=content.expand_id";

			$result_q_row = $this->mysqli_query_internal($this->conn,$query_row);
			$result_row = array();
			while ($row_t = mysqli_fetch_array($result_q_row, MYSQLI_ASSOC)) {
				$result_row[] = $row_t;
			}

			$row["expand_content"] = $result_row;

			$result[] = $row;
		}

		return $result;
	}

	public function get_content_anonymous($content_id)
	{
		$role_id = 0;
		return $this->get_content($role_id, $content_id);
	}

	public function get_content_with_permission($user_id, $content_id)
	{
		if ($this->conn == NULL)
		{
			$this->load('member');
		}

		$query = "select role_id 
		         from member
		         where user_id=\"".$user_id."\"";

		$result_q = $this->mysqli_query_internal($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		if (sizeof($result) == 0)
			return [];

		$role_id = $result[0]["role_id"];

		return $this->get_content($role_id, $content_id);
	}
}