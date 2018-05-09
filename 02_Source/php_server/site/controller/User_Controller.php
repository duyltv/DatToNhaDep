<?php if ( ! defined('PATH_SYSTEM')) die ('Bad requested!');
 
class User_Controller extends BK_Controller
{
    public function loginAction()
    {
        if(isset($_POST['username']))
        {
            $this->model->load('users');
            $users = $this->model->get('users');
            foreach($users as $user)
            {
                if($user['username'] == $_POST['username'] && $user['password'] == $_POST['password']) 
                {
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = $user['role'];

                    $data = array(
                        'title' => 'Đăng nhập thành công'
                    );
                    $this->view->load('002_1_login_success', $data);
                    $this->view->show();
                    return;
                }
            }

            $data = array(
                'title' => 'Đăng nhập thất bại'
            );
            $this->view->load('002_2_login_fail', $data);
            $this->view->show();
            return;
        }
        $data = array(
            'title' => 'Đăng nhập thất bại'
        );
        $this->view->load('002_2_login_fail', $data);
        $this->view->show();
        return;
    }

    public function logoutAction()
    {
        session_unset();
        session_destroy();

        echo '<script type="text/javascript"> window.location = "index.php" </script>';
        return;
    }

    // Manager roles
    public function showAction()
    {
        $this->model->load('users');

        $users = $this->model->get('users');

        $data = array(
            'title' => 'Quản lý thành viên',
            'users' => $users   
        );

        $this->view->load('005_3_manager_user_list', $data);
        $this->view->show();
    }
    public function addAction()
    {
        if(isset($_POST['user_id']))
        {
            $this->model->load('users');

            $dup_count = $this->model->get_count('users', 'id=\''.$_POST['user_id'].'\'')['soluong'];
            if ($dup_count > 0)
            {
                echo "User exist:";
                print_r($dup_count);
                return;
            }

            $data = array(
                'id' => $_POST['user_id'],
                'username' => $_POST['user_id'],
                'password' => $_POST['password'],
                'email' => $_POST['email'],
                'fullname' => $_POST['fullname'],
                'role' => $_POST['role'],
            );

            $this->model->insert('users',$data);
            echo '<script type="text/javascript"> window.location = "index.php?c=user&a=show"</script>';
        } else {
            $data = array(
                'title' => 'Quản lý thành viên'
            );
            $this->view->load('005_4_manager_user_add', $data);
            $this->view->show();
        }
    }

    public function deleteAction()
    {
        if(isset($_GET['user_id']))
        {
            $this->model->load('users');

            $data = array(
                'id' => $_GET['user_id']
            );

            $this->model->delete('users',$data);

            $data = array(
                'user_id' => $_GET['user_id']
            );
            $this->model->delete('teach', $data);

            $data = array(
                'user_id' => $_GET['user_id']
            );
            $this->model->delete('study', $data);

            $data = array(
                'user_id' => $_GET['user_id']
            );
            $this->model->delete('scores', $data);

            echo '<script type="text/javascript"> window.location = "index.php?c=user&a=show"</script>';
        }
    }

    public function editAction()
    {
        if(isset($_POST['user_id']))
        {
            $this->model->load('users');

            $data = array(
                'id' => $_POST['user_id'],
                'username' => $_POST['user_id'],
                'password' => $_POST['password'],
                'email' => $_POST['email'],
                'fullname' => $_POST['fullname'],
                'role' => $_POST['role'],
            );

            $this->model->update('users',$data);
            echo '<script type="text/javascript"> window.location = "index.php?c=user&a=show"</script>';
        } else {
            if (isset($_GET['user_id']))
            {
                $this->model->load('users');
                $user = $this->model->get('users', $_GET['user_id'])[0];
                $data = array(
                    'title' => 'Quản lý thành viên',
                    'user_id' => $user['id'],
                    'fullname' => $user['fullname'],
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'role' => $user['role']
                );
                $this->view->load('005_5_manager_user_edit', $data);
                $this->view->show();
            }
        }
    }
}