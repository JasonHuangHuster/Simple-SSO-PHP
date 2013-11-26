<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
         
      
		return View::make('hello');
	}

/**
     * The current broker
     * @var string
     */
    protected $broker = null;
   /**
     * Flag to indicate the sessionStart has been called
     * @var boolean
     */
    protected $started=false;
    /**
     * Information of the brokers.
     * This should be data in a database.
     * 
     * @var array
     */
    protected static $brokers = array(
        'ALEX' => array('secret'=>"abc123"),
        'BINCK' => array('secret'=>"xyz789"),
        'UZZA' => array('secret'=>"rino222"),
        'AJAX' => array('secret'=>"amsterdam"),
        'LYNX' => array('secret'=>"klm345"),
    );


	public function ssoEntry()
	{
		$action = Input::get('action');
		if($action == 'attach')
		{
			$token = Input::get('token');
			$broke = Input::get('broke');
			if($token && $broke)
			{
				$this->attach($token,);

			}else
			{

				$this->fail('绑定参数不完整');
			}



            
 
		}else if('login'){
          
          $this->login();

		}else if('getUserInfo'){
          
          $this->getUserInfo();

		}
	}

	public function attach(){


	}
	public function login(){


	}
	public function getUserInfo(){


	}


    /**
     * An error occured.
     * I would normaly solve this by throwing an Exception and use an exception handler.
     *
     * @param string $message
     */
    protected function fail($message)
    {
        header("HTTP/1.1 406 Not Acceptable");
        echo $message;
        exit;
    }
    
    /**
     * Login failure.
     * I would normaly solve this by throwing a LoginException and use an exception handler.
     *
     * @param string $message
     */
    protected function failLogin($message)
    {
        header("HTTP/1.1 401 Unauthorized");
        echo $message;
        exit;
    }

}