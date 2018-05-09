<?php if ( ! defined('PATH_SYSTEM')) die ('Bad requested!');
 
class Teacher_Controller extends BK_Controller
{
    private function check_exist_record($data = array(), $user_id, $semester_id)
    {
        foreach($data as $row)
        {
            if ($row['user_id'] == $user_id && $row['semester_id'] == $semester_id)
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
            if ($this->check_exist_record($data_, $row['user_id'], $row['semester_id']))
            {
                $end_row = array_pop($data_); // Because input array's data is ordered by SQL
                $end_row[$row['element_name']] = $row['score'];
                $data_[] = $end_row;
            }
            else
            {
                $append_row = array(
                    'semester_id' => $row['semester_id'],
                    'user_id' => $row['user_id'],
                    'fullname' => $row['fullname'],
                    $row['element_name'] => $row['score']
                );
                $data_[] = $append_row;
            }
        }

        return $data_;
    }
    public function subjectsAction()
    {
        // Please check login before go to this page
        if(isset($_SESSION['username']))
        {
            $this->model->load('users');

            $subjects = $this->model->get_subjects_by_user_id($_SESSION['username']);

            $data = array(
                'title' => 'Quản lý môn học',
                'subjects' => $subjects
            );
            $this->view->load('004_1_teacher_subject_list', $data);
            $this->view->show();
        }
    }

    private function convert_standard_to_structure($standard_raw = array())
    {
        $standard = array();
        foreach ($standard_raw as $std_row)
        {
            $row_out = $std_row['outcome_des'];
            $exists = false;

            $element = array(
                'score_element_des' => $std_row['score_element_des'],
                'pass' => $std_row['pass'],
                'fail' => $std_row['fail']
                );
            $count = 0;
            foreach ($standard as $std) {
                if ($std['outcome_des'] == $row_out)
                {
                    $standard[$count]['score_element'][] = $element;
                    //$std['score_element'][] = $element;
                    $exists = true;
                }

                $count = $count + 1;
            }
            if ($exists == false)
                $standard[] = array(
                    'outcome_des' => $row_out,
                    'score_element' => array($element)
                    );
        }
        return $standard;
    }

    public function scoresAction()
    {
        if(isset($_GET['subject_id']))
        {
            $this->model->load('users');

            $score_table = $this->model->get_score_list_by_subject($_GET['subject_id']);

            $scores = $this->convert_scoretable_to_printable($score_table);

            $subject = $this->model->get('subjects', $_GET['subject_id'])[0];
            $subject_name = $subject['name'];

            $standard_raw = $this->model->get_standard_table_by_subject($_GET['subject_id']);
            $standard = $this->convert_standard_to_structure($standard_raw);

            $data = array(
                'title' => 'Quản lý môn học',
                'subject_id' => $_GET['subject_id'],
                'subject_name' => $subject_name,
                'score_table' => $scores,
                'full_score_table' => $score_table,
                'fomular' => $subject['fomular'],
                'standard' => $standard
            );

            if(sizeof($data['score_table'])==0)
            {
                $data = array(
                    'title' => 'Quản lý môn học',
                    'subject_id' => $_GET['subject_id'],
                    'subject_name' => $subject_name,
                    'elements' => $this->model->get_element_list_by_subject($_GET['subject_id']),
                    'fomular' => $subject['fomular']
                );
            }
            $this->view->load('004_3_teacher_score_list', $data);
            $this->view->show();
        }
    }

    public function typeAction()
    {
        if(!isset($_POST['semester1']))
        {
            if(isset($_GET['subject_id']))
            {
                $this->model->load('users');

                $score_table = $this->model->get_score_list_by_subject($_GET['subject_id']);

                $scores = $this->convert_scoretable_to_printable($score_table);

                $subject = $this->model->get('subjects', $_GET['subject_id'])[0];
                $subject_name = $subject['name'];

                $teach = $this->model->get_condition('teach', 'user_id = ' . $_SESSION['username'] . ' and subject_id = ' . $_GET['subject_id']);
                $semester = '';
                if (count($teach) > 0)
                {
                    $semester = $teach[0]['semester_id'];
                }

                $data = array(
                    'title' => 'Quản lý môn học',
                    'subject_id' => $_GET['subject_id'],
                    'subject_name' => $subject_name,
                    'elements' => $this->model->get_element_list_by_subject($_GET['subject_id']),
                    'semester_id' => $semester
                );
                $this->view->load('004_4_teacher_type_score', $data);
                $this->view->show();
            }
        } else {
            $this->model->load('subjects');
            $score_count = $_POST['score_count'];
            $subject_id = $_POST['subject_id'];

            $subject = $this->model->get('subjects', $subject_id)[0];
            $subject_name = $subject['name'];
            $insert_success = true;

            for ($i = 1; $i <= $score_count; $i++) 
            {
                if (empty($_POST['semester'.$i]))
                    break;
                $semester_id = $_POST['semester'.$i];
                $student_id = $_POST['mssv'.$i];

                // Check if score record exists
                $score_record = $this->model->get_condition('scores', 'user_id = '.$student_id.' AND semester_id = '.$semester_id);

                // Insert into database

                $data = array(
                    'subject_id' => $subject_id,
                    'semester_id' => $semester_id,
                    'user_id' => $student_id
                );
                $insert_success = $this->model->insert('study', $data);
                
                $elements = $this->model->get_element_list_by_subject($subject_id);
                $ele_count = 1;
                foreach($elements as $element)
                {
                    $data = array(
                        'semester_id' => $semester_id,
                        'user_id' => $student_id,
                        'score_element_id' => $element['id'],
                        'score' => $_POST['score'.$i.'_'.$ele_count]
                    );
                    if (!$insert_success)
                    {
                        $this->model->update_manual('scores', $data, 'user_id = ' . $student_id . ' and semester_id = ' . $semester_id . ' and score_element_id = ' . $element['id']);
                    } 
                    else
                        $this->model->insert('scores', $data);
                    $ele_count = $ele_count + 1;
                }
            }
            if ($insert_success)
                echo '<script type="text/javascript"> window.location = "index.php?c=teacher&a=scores&subject_id='.$subject_id.'" </script>';
            else
                echo '<script type="text/javascript"> window.location = "index.php?c=teacher&a=scores&subject_id='.$subject_id.'&update=1" </script>';
        }
    }

    public function newsubjectAction()
    {
        if(!isset($_POST['sname']))
        {
            $data = array(
                    'title' => 'Quản lý môn học',
            );
            $this->view->load('004_2_teacher_new_subject', $data);
            $this->view->show();
        }
        else
        {
            // POST new subject
            $outcome_count = $_POST['out_count'];
            $subject_id = $_POST['sid'];
            $subject_name = $_POST['sname'];
            $subject_descript = $_POST['sdes'];

            $outcomes = array();
            for ($i = 1; $i <= $outcome_count; $i++) 
            {
                $outcomes[$i] = $_POST['outcome' . $i];
            }
            // Insert subject
            $subject_data = array(
                'id' => $subject_id,
                'name' => $subject_name,
                'description' => $subject_descript
            );
            $this->model->insert('subjects', $subject_data);

            // Insert outcomes
            for ($i = 1; $i <= $outcome_count; $i++) 
            {
                if (empty($outcomes[$i]))
                    continue;
                $outcome = $outcomes[$i];
                $outcome_data = array(
                    'subject_id' => $subject_id,
                    'description' => $outcome
                );

                $this->model->insert('outcomes', $outcome_data);
            }

            // Insert current user to the teacher of this subject
            $teach_data = array(
                'user_id' => $_SESSION['username'],
                'subject_id' => $subject_id,
                'semester_id' => $_POST['ssem']
            );
            $this->model->insert('teach', $teach_data);

            echo '<script type="text/javascript"> window.location = "index.php?c=teacher&a=element&subject_id='.$subject_id.'" </script>';
        }
    }

    public function elementAction()
    {
        if(!isset($_POST['element_count']))
        {
            // Please check login before go to this page
            if(isset($_GET['subject_id']))
            {
                $this->model->load('outcomes');

                $outcomes = $this->model->get_condition('outcomes', 'subject_id = '.$_GET['subject_id']);

                $subject = $this->model->get('subjects', $_GET['subject_id'])[0];
                $subject_name = $subject['name'];

                // Get score elements of this subject
                $element_get = $this->model->get_condition('score_elements', 'subject_id = '.$_GET['subject_id']);

                // An array contains outcomes of elements
                $element_outs = array();
                foreach ($element_get as $element)
                {
                    $outcome_count = 1;
                    // An array contains 
                    $element_out = array();

                    $outcome_of_ele = $this->model->get_condition('outcomes_of_score_element', 'score_element_id = '.$element['id']);

                    foreach ($outcomes as $outcome)
                    {
                        $found=false;
                        foreach ($outcome_of_ele as $out_ele)
                        {
                            if ($outcome['id'] == $out_ele['outcome_id'])
                                $found=true;
                        }
                        if ($found)
                            $element_out[] = $outcome_count;
                        $outcome_count=$outcome_count+1;
                    }
                    $element_outs[] = $element_out;
                }

                $data = array(
                    'title' => 'Quản lý môn học',
                    'subject_id' => $_GET['subject_id'],
                    'subject_name' => $subject_name,
                    'subject_fomular' => $subject['fomular'],
                    'outcomes' => $outcomes,
                    'elements' => $element_get,
                    'outcome_of_ele' => $element_outs
                );
                $this->view->load('004_5_teacher_score_element', $data);
                $this->view->show();
            }
        } else {
            $this->model->load('score_elements');
            $element_count = $_POST['element_count'];
            for ($i = 1; $i <= $element_count; $i++) 
            {
                $outs = $_POST['outcome'.$i];
                $element_name = $_POST['name'.$i];

                $element_data = array (
                    'subject_id' => $_GET['subject_id'],
                    'name' => $element_name
                );

                $this->model->insert('score_elements', $element_data);

                $element_get = $this->model->get_condition('score_elements', 'subject_id = '.$_GET['subject_id'].' AND name = \''.$element_name.'\'')[0];

                $element_get_id = $element_get['id'];

                $outcomes = $this->model->get_condition('outcomes', 'subject_id = '.$_GET['subject_id']);

                foreach ($outs as $out)
                {
                    $select_outcome = array();
                    $j=1;
                    foreach ($outcomes as $outcome)
                    {
                        if ($j == $out)
                        {
                            $select_outcome = $outcome;
                        }
                        $j=$j+1;
                    }
                    $out_data = array (
                        'score_element_id' => $element_get_id,
                        'outcome_id' => $select_outcome['id']
                    );
                    $this->model->insert('outcomes_of_score_element', $out_data);
                }
            }

            $fomular_data = array (
                'id' => $_GET['subject_id'],
                'fomular' => $_POST['fomular']
            );

            $this->model->update('subjects', $fomular_data);

            echo '<script type="text/javascript"> window.location = "index.php?c=teacher&a=element&subject_id='.$_GET['subject_id'].'" </script>';
        }
    }
}