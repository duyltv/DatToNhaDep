<?php if ( ! defined('PATH_SYSTEM')) die ('Bad requested!');
 
class Index_Controller extends BK_Controller
{
    public function indexAction()
    {
    	$data = array(
            'title' => 'Trang chủ',
        );
         
        // Load view
        $this->view->load('001_mainpage', $data);
        $this->view->show();
    }
}