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

		$this->mysqli_query_internal($this->conn,$query);
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

		$this->mysqli_query_internal($this->conn,$query);
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

		$this->mysqli_query_internal($this->conn,$query);
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
		         where is_mod=\"true\"";
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
}