<?php
 
class BK_Model_Loader
{
    protected $conn = NULL;
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
	
	/**
     * Search
     *
     * @desc    Hàm cho phép get nội dung table được filter content
	 *			Hàm trả về array 2 chiều. Sử dụng như trong example file Controller.
     */
    public function search($model, $input, $field = 'content')
    {
        $query = "SELECT * FROM $model WHERE $field LIKE '%".$input."%'";
		$result_q = mysqli_query($this->conn,$query);
		
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
		
		return mysqli_query($this->conn,$query);
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

		$result_q = mysqli_query($this->conn,$query);

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
		$result_q = mysqli_query($this->conn,$query);
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
		$result_q = mysqli_query($this->conn,$query);

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

		mysqli_query($this->conn,$query);
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

		mysqli_query($this->conn,$query);
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

		mysqli_query($this->conn,$query);
    }


    /*
     * ADVANCED FUNCTIONS
     *	
     * These funcs are used to do the advanced jobs that don't need
     * much resources
     *
     */

   	public function get_score_by_studentid($id = "")
   	{
   		if ($id == "")
   		{
   			return [];
   		}

   		if ($this->conn == NULL)
   		{
   			$this->load('users');
   		}

	   	$query = "select scoretable.*,  elementtable.score_element_name
	   	from (
	   		select score.id as score_id, score.user_id, score.semester_id, info.subject_id, info.name as subject_name, score.score_element_id, score.score, info.fomular as fomular
   			from (
   				SELECT * 
   				FROM scores
   			) 
   			as score 
   			JOIN (
   				SELECT study.user_id, study.semester_id, subject.subject_id, subject.name, subject.fomular 
   				FROM (
   					SELECT * 
   					FROM study 
   					where user_id=" . $id . "
   				) 
   				AS study 
   				JOIN (
   					SELECT id as subject_id, name, fomular 
   					FROM subjects
   				) 
   				as subject
   				ON subject.subject_id = study.subject_id
   			) 
   			as info
   		) 
   		as scoretable
   		JOIN (
   			select id as score_element_id, name as score_element_name 
   			from score_elements
   		)
   		as elementtable
   		ON scoretable.score_element_id = elementtable.score_element_id
   		group by elementtable.score_element_id
   		order by subject_id, score_element_id";
		$result_q = mysqli_query($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
   	}

	public function get_subjects_by_user_id($id = "")
   	{
   		if ($id == "")
   		{
   			return [];
   		}

   		if ($this->conn == NULL)
   		{
   			$this->load('users');
   		}

	   	$query = "select subject.id as subject_id, subject.name as subject_name, teach.semester_id from (
	   		select id, name
	   		from subjects) as subject
	   	join (
	   		select * 
	   		from teach) as teach
	   	on subject.id = teach.subject_id and teach.user_id = " . $id . "
	   	order by teach.semester_id";
		$result_q = mysqli_query($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
   	}

   	public function get_score_list_by_subject($subject_id = "")
   	{
   		if ($subject_id == "")
   		{
   			return [];
   		}

   		if ($this->conn == NULL)
   		{
   			$this->load('users');
   		}

	   	$query = "select score.semester_id, score.user_id, course_info.fullname, course_info.element_id, course_info.element_name, score 
	   	from (
	   		select user_id, semester_id, score_element_id, score 
	   		from scores
	   	) as score 
	   	join (
	   		select user_subject.semester_id, user_subject.user_id, user_subject.fullname, element.id as element_id, element.name as element_name
	   		from (
	   			select id, subject_id, name
	   			from score_elements
	   		) as element 
	   		join (
	   			select study.subject_id, study.semester_id, user.user_id, user.fullname 
	   			from (
	   				select id as user_id, fullname 
	   				from users
	   			) as user 
	   			join (
	   				select * 
	   				from study 
	   				where subject_id = " . $subject_id . " 
	   				order by semester_id
	   			) as study 
	   			on user.user_id = study.user_id
	   		) as user_subject 
	   		on element.subject_id = user_subject.subject_id
	   	) as course_info 
	   	on score.user_id = course_info.user_id 
	   	and score.semester_id = course_info.semester_id 
	   	and score.score_element_id = course_info.element_id
	   	ORDER by user_id, element_id";
		$result_q = mysqli_query($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
   	}

   	public function get_element_list_by_subject($subject_id = "")
   	{
   		if ($subject_id == "")
   		{
   			return [];
   		}

   		if ($this->conn == NULL)
   		{
   			$this->load('subjects');
   		}

	   	$query = "select id, name from score_elements where subject_id = " . $subject_id;
		$result_q = mysqli_query($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
   	}

   	public function get_standard_table_by_subject($subject_id = "")
   	{
   		if ($subject_id == "")
   		{
   			return [];
   		}

   		if ($this->conn == NULL)
   		{
   			$this->load('subjects');
   		}

   		$query = "SELECT pass.outcome_des, pass.score_element_des,IFNULL(pass.pass,0) as pass,IFNULL(fail.fail,0) as fail
		FROM   (
		                SELECT   outcome_id,
		                         outcome_des,
		                         score_element_id,
		                         score_element_des,
		                         Count(score) AS pass
		                FROM     (
		                                  SELECT   out_ele.outcome_id,
		                                           out_ele.outcome_des,
		                                           out_ele.score_element_id,
		                                           out_ele.score_element_des,
		                                           scores.score
		                                  FROM     (
		                                                  SELECT outcome_id,
		                                                         description AS outcome_des,
		                                                         score_element_id,
		                                                         name AS score_element_des
		                                                  FROM   (
		                                                                  SELECT   outcome_id,
		                                                                           score_element_id,
		                                                                           description
		                                                                  FROM     (
		                                                                                  SELECT *
		                                                                                  FROM   outcomes_of_score_element) AS out_of_ele
		                                                                  JOIN
		                                                                           (
		                                                                                  SELECT *
		                                                                                  FROM   outcomes
		                                                                                  WHERE  subject_id = ".$subject_id.") AS outcome
		                                                                  ON       out_of_ele.outcome_id = outcome.id
		                                                                  ORDER BY outcome_id) AS outs
		                                                  JOIN
		                                                         (
		                                                                SELECT *
		                                                                FROM   score_elements
		                                                                WHERE  subject_id = ".$subject_id.") AS ele
		                                                  ON     outs.score_element_id = ele.id) AS out_ele
		                                  JOIN
		                                           (
		                                                  SELECT score_element_id,
		                                                         score
		                                                  FROM   scores) AS scores
		                                  ON       out_ele.score_element_id = scores.score_element_id
		                                  ORDER BY outcome_id) as score_table
		                WHERE    score >= 5
		                GROUP BY outcome_id,
		                         score_element_id) AS pass
		LEFT OUTER JOIN
		       (
		                SELECT   outcome_id,
		                         outcome_des,
		                         score_element_id,
		                         score_element_des,
		                         Count(score) AS fail
		                FROM     (
		                                  SELECT   out_ele.outcome_id,
		                                           out_ele.outcome_des,
		                                           out_ele.score_element_id,
		                                           out_ele.score_element_des,
		                                           scores.score
		                                  FROM     (
		                                                  SELECT outcome_id,
		                                                         description AS outcome_des,
		                                                         score_element_id,
		                                                         name AS score_element_des
		                                                  FROM   (
		                                                                  SELECT   outcome_id,
		                                                                           score_element_id,
		                                                                           description
		                                                                  FROM     (
		                                                                                  SELECT *
		                                                                                  FROM   outcomes_of_score_element) AS out_of_ele
		                                                                  JOIN
		                                                                           (
		                                                                                  SELECT *
		                                                                                  FROM   outcomes
		                                                                                  WHERE  subject_id = ".$subject_id.") AS outcome
		                                                                  ON       out_of_ele.outcome_id = outcome.id
		                                                                  ORDER BY outcome_id) AS outs
		                                                  JOIN
		                                                         (
		                                                                SELECT *
		                                                                FROM   score_elements
		                                                                WHERE  subject_id = ".$subject_id.") AS ele
		                                                  ON     outs.score_element_id = ele.id) AS out_ele
		                                  JOIN
		                                           (
		                                                  SELECT score_element_id,
		                                                         score
		                                                  FROM   scores) AS scores
		                                  ON       out_ele.score_element_id = scores.score_element_id
		                                  ORDER BY outcome_id) as score_table
		                WHERE    score < 5
		                GROUP BY outcome_id,
		                         score_element_id ) AS fail
		ON     pass.outcome_id = fail.outcome_id and pass.score_element_id = fail.score_element_id";
		$result_q = mysqli_query($this->conn,$query);

		$result = array();
		while ($row = mysqli_fetch_array($result_q, MYSQLI_ASSOC)) {
			$result[] = $row;
		}

		return $result;
   	}
}