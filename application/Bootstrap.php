<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoloadModules()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'default',
            'basePath'  => APPLICATION_PATH . '/')
	      );
	Zend_Loader_Autoloader::getInstance()->registerNamespace('My_');
        return $autoloader;
    }
       
    protected function _initView()
    {
        $front = Zend_Controller_Front::getInstance();
        $layout = Zend_Layout::getMvcInstance();
        // Initialize view
        $view = new Zend_View();
        $view->doctype('HTML5');
        $view->headTitle('Full in Joy: Be a part of joy full people');
        $view->env = APPLICATION_ENV;
        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);
        $baseUrl = '/airto';
        // Js files
        $view->headScript()->prependFile($baseUrl."/public/js/jquery/ddaccordion.js");	
        //$view->headScript()->prependFile($baseUrl."/public/js/jquery/jquery.tools.min.js");
	$view->headScript()->prependFile($baseUrl."/public/js/jquery/jquery.bgiframe-2.1.2.js");
        $view->headScript()->prependFile($baseUrl."/public/js/jquery/jquery.easing.js");
        $view->headScript()->prependFile($baseUrl."/public/js/jquery/jquery.autocomplete.js");
        $view->headScript()->prependFile($baseUrl."/public/js/jquery/jquery.autosize-min.js");
        $view->headScript()->prependFile($baseUrl."/public/js/jquery/jquery.ui.core.js");
        $view->headScript()->prependFile($baseUrl."/public/js/jquery/jquery-ui-1.8.10.custom.min.js");
        $view->headScript()->prependFile($baseUrl."/public/js/jquery/jquery.validate.js");
        $view->headScript()->prependFile($baseUrl."/public/js/jquery/jquery-1.8.0.min.js");
        
        $view->headLink()->appendStylesheet($baseUrl."/public/css/style.css");
	$session = new Zend_Session_Namespace();
	$view->session = $session;
	return $view;
    }
    function _initResourceLoader(){
	    $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
		'basePath'      => APPLICATION_PATH . '/',
		'namespace'     => '',
		'resourceTypes' => array(
		    'form' => array(
			'path'      => 'forms/',
			'namespace' => 'Form',
		    ),
		    'model' => array(
			'path'      => 'models/',
			'namespace' => 'Model',
		    ),
		),
	    ));
    }
    function _initDbAdapter(){
	$resources = $this->getOptions();
	$dbParams = $resources['resources']['db'];
	$arr = array(
	    'host'     => $dbParams['params']['host'],
	    'username' => $dbParams['params']['username'],
	    'password' => $dbParams['params']['password'],
	    'dbname'   => $dbParams['params']['dbname']
	);
	$db = Zend_Db::factory($dbParams['adapter'], $arr);
	Zend_Registry::set('db', $db);
	return $db;
    }
    
    function _initCache(){
	$frontendOptions = array(
	    'lifetime' => 7200,
	    'automatic_serialization' => true
	);
	$backendOptions = array(
	    'cache_dir' => dirname(__FILE__).'/../data/cache'
	);
	
	$dbCache = Zend_Cache::factory('Core',
				     'File',
				     $frontendOptions,
				     $backendOptions);
	 
	$manager = new Zend_Cache_Manager;
	$manager->setCache('database', $dbCache);
	
	Zend_Registry::set('cache', $dbCache);
    }
    
    function _initPaginator(){
	//Zend_Paginator::setDefaultScrollingStyle('Sliding');
	//Zend_View_Helper_PaginationControl::setDefaultViewPartial(
	//    'my_pagination_control.phtml'
	//);
    }
    
    function _initRoutes(){
	$front = Zend_Controller_Front::getInstance();
	
	$router = $front->getRouter();
	
	$router->addRoute('signup',  new Zend_Controller_Router_Route('signup',
                                     array('module'=>'','controller' => 'index',
                                           'action' => 'signup')));
	
	$router->addRoute('register',  new Zend_Controller_Router_Route('flights',
                                     array('module'=>'','controller' => 'index',
                                           'action' => 'flights')));
	
	$router->addRoute('emessage',  new Zend_Controller_Router_Route('emessage',
                                     array('module'=>'','controller' => 'index',
                                           'action' => 'message')));
	
	$router->addRoute('login',  new Zend_Controller_Router_Route('login',
                                     array('module'=>'','controller' => 'index',
                                           'action' => 'login')));
	
	$router->addRoute('fblogin',  new Zend_Controller_Router_Route('fblogin',
                                     array('module'=>'','controller' => 'index',
                                           'action' => 'fblogin')));
	
	$router->addRoute('logout',  new Zend_Controller_Router_Route('logout',
                                     array('module'=>'','controller' => 'index',
                                           'action' => 'logout')));
	
	$router->addRoute('changepassword',  new Zend_Controller_Router_Route('changepassword',
                                     array('module'=>'','controller' => 'user',
                                           'action' => 'changepassword')));
	
    }
    function _initCurrency(){
	$currency = new Zend_Currency('de_AT');
	Zend_Registry::set('Zend_Currency', $currency);
	//echo $this->currency(1234.56); //this returns '€ 1.234,56'
    }
    function _initSmtp(){
	/*
	$config = array('auth' => 'login',
                'username' => 'myusername',
                'password' => 'password');
 	$transport = new Zend_Mail_Transport_Smtp('mail.server.com', $config);
	// Set From & Reply-To address and name for all emails to send.
	Zend_Mail::setDefaultTransport($transport);
	Zend_Mail::setDefaultFrom('sender@ecorprent.com', 'Ecorprent Team');
	Zend_Mail::setDefaultReplyTo('replyto@ecorprent.com','Ecorprent Team');
	*/
    }
    
}