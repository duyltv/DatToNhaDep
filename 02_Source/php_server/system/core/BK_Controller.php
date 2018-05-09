<?php if ( ! defined('PATH_SYSTEM')) die ('Bad requested!');
 
/**
 * @package     BK_Framework
 * @filesource  system/core/BK_Controller.php
 */
class BK_Controller
{
    // Đối tượng view
    protected $view     = NULL;
     
    // Đối tượng model
    protected $model    = NULL;
     
    // Đối tượng library
    protected $library  = NULL;
     
    // Đối tượng helper
    protected $helper   = NULL;
     
    // Đối tượng config
    protected $config   = NULL;
     
    /**
     * Hàm khởi tạo
     * 
     * @desc    Load các thư viện cần thiết
     */
    public function __construct() 
    {
        // Loader cho config
        require_once PATH_SYSTEM . '/core/loader/BK_Config_Loader.php';
        $this->config   = new BK_Config_Loader();
        $this->config->load('config');

        // Loader Library
        require_once PATH_SYSTEM . '/core/loader/BK_Library_Loader.php';
        $this->library = new BK_Library_Loader();

        // Load View
        require_once PATH_SYSTEM . '/core/loader/BK_View_Loader.php';
        $this->view = new BK_View_Loader();

        // Load Model
        require_once PATH_SYSTEM . '/core/loader/BK_Model_Loader.php';
        $this->model = new BK_Model_Loader();
    }
}