<?php
 
// Đường dẫn tới hệ  thống
define('PATH_SYSTEM', __DIR__ .'/system');
define('PATH_APPLICATION', __DIR__ . '/site');
 
// Lấy thông số cấu hình
require (PATH_SYSTEM . '/config/config.php');
 
//mở file BK_Common.php, file này chứa hàm BK_Load() chạy hệ thống
include_once PATH_SYSTEM . '/core/BK_Common.php';
 
// Chương trình chính
BK_load();