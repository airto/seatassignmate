<?php
define('BASE_PATH', realpath(dirname(__FILE__)));
define('APPLICATION_PATH', BASE_PATH . '/application');
date_default_timezone_set('Europe/London');
// Include path
set_include_path('.' . PATH_SEPARATOR .
    BASE_PATH . '/library/Zend'
    . PATH_SEPARATOR . BASE_PATH . '/library'
    . PATH_SEPARATOR . APPLICATION_PATH . '/models'
    . PATH_SEPARATOR . APPLICATION_PATH . '/forms'
    . PATH_SEPARATOR . get_include_path()
);

// Define application environment
define('APPLICATION_ENV', getenv('APPLICATION_ENV'));

// Zend_Application
require_once 'Application.php';

// Constant File
require_once BASE_PATH.'/library/ConstInclude.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();
$application->run();