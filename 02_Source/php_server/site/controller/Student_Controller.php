<?php if ( ! defined('PATH_SYSTEM')) die ('Bad requested!');
 
class Student_Controller extends BK_Controller
{
    private function check_exist_subject($data = array(), $subject_id, $semester_id)
    {
        foreach($data as $row)
        {
            if ($row['subject_id'] == $subject_id && $row['semester_id'] == $semester_id)
            {
                return True;
            }
        }

        return False;
    }
    private function convert_scoretable_to_printable($data = array())
    {
        $data_ = array();
        foreach($data as $row) 
        {
            if ($this->check_exist_subject($data_, $row['subject_id'], $row['semester_id']))
            {
                $end_row = array_pop($data_); // Because input array's data is ordered by SQL
                $append_row = array(
                    'subject_id' => $end_row['subject_id'],
                    'subject_name' => $end_row['subject_name'],
                    'elements_score' => $end_row['elements_score'] . ', ' . $row['score_element_name'] . ': ' . $row['score'], // Append by rows
                    'total_score' => '', // Process by client
                    'semester_id' => $row['semester_id'],
                    'fomular' => $row['fomular']
                );
                $data_[] = $append_row;
            }
            else
            {
                $append_row = array(
                    'subject_id' => $row['subject_id'],
                    'subject_name' => $row['subject_name'],
                    'elements_score' => $row['score_element_name'] . ': ' . $row['score'], // Append by rows
                    'total_score' => '', // Process by client
                    'semester_id' => $row['semester_id'],
                    'fomular' => $row['fomular']
                );
                $data_[] = $append_row;
            }
        }

        return $data_;
    }
    public function indexAction()
    {
        // Please check login before go to this page
        if(isset($_SESSION['username']))
        {
            $this->model->load('users');

            // Get score table of user
            $score_table = $this->model->get_score_by_studentid($_SESSION['username']);
            $print_score_table = $this->convert_scoretable_to_printable($score_table);

            $data = array(
                'title' => 'Bảng điểm cá nhân',
                'score_table' => $print_score_table,
                'full_score_table' => $score_table
            );
            $this->view->load('003_student_view', $data);
            $this->view->show();
        }
    }
}