<?php

class SSOController extends BaseController {

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

    public $links_path;

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
        'JXKH' => array('secret'=>"Jason"),
        'IDEA' => array('secret'=>"Ryn"),
        'UZZA' => array('secret'=>"rino222"),
        'AJAX' => array('secret'=>"amsterdam"),
        'LYNX' => array('secret'=>"klm345"),
    );

    /**
     * Information of the users.
     * This should be data in a database.
     * 
     * @var array
     */
    protected  $user;
      /*protected static $users = array(
        'jan' => array('password'=>"jan1", 'fullname'=>"Jan Smit", 'email'=>"jan@smit.nl"),
        'peter' => array('password'=>"peter1", 'fullname'=>"Peter de Vries", 'email'=>"peter.r.de-vries@sbs.nl"),
        'bart' => array('password'=>"bart1", 'fullname'=>"Bart de Graaf", 'email'=>"graaf@bnn.info"),
        'henk' => array('password'=>"henk1", 'fullname'=>"Henk Westbroek", 'email'=>"henk@amsterdam.com"),
        'Jason' => array('password'=>"goodluck", 'fullname'=>"Huang DangWu", 'email'=>"469673467@qq.com")
    );*/
    
    /**
     * The current broker
     * @var string
     */
    protected $broker = null;
    
    
    /**
     * Class constructor.
     */
    /*public function __construct()
    {
        // if (!function_exists('symlink')) $this->links_path = sys_get_temp_dir();
    }*/
    
   public function entrance(){
  
      $method = Input::get('method');
   
    
      if($method == 'attach'){
        $this->attach();
      }else if($method == 'login'){
        // $this->test();
        $this->login();
      }else if($method == 'info'){
        $this->info();
      }else if($method == 'logout'){
        $this->logout();
      }else{
        $this->fail('动作请求错误!'.'Method::'.$method.'URL:::'.$_SERVER['REQUEST_URI'].'Session_id'.Session::getId());
      }

   }

    
    /**
     * Start session and protect against session hijacking
     */
    protected function sessionStart()
    {

        if ($this->started) return;
        $this->started = true;
        
        // Broker session
        $matches = null;
        if (isset($_COOKIE[session_name()]) && preg_match('/^SSO-(\w*+)-(\w*+)-([a-z0-9]*+)$/', $_COOKIE[session_name()], $matches)) {
            $sid = $_COOKIE[session_name()];
             
        
            $bs = BrokerSession::where('sessionKey',$sid)->get();
            if(isset($bs[0]))
            {
                Log::info(session_save_path());
                session_id($bs[0]->session_id);
                session_start();
                // setcookie(session_name(),session_id(),time()+1200);
            }else{
                session_start();
            }
            if(isset($_SESSION['client_addr'])){
                
             Log::info('ip :'.$_SESSION['client_addr']);  
            }
             
          
            if (!isset($_SESSION['client_addr'])) {
                session_destroy();
                  Log::info("SessionKey".$sid);
                $this->fail("Not attached");
            }
            
            if ($this->generateSessionId($matches[1], $matches[2], $_SESSION['client_addr']) != $sid) {
                session_destroy();
                $this->fail("Invalid session id");
            }

            $this->broker = $matches[1];
            return;
        }

        // User session
        session_start();
        if (isset($_SESSION['client_addr']) && $_SESSION['client_addr'] != $_SERVER['REMOTE_ADDR']) session_regenerate_id(true);
        if (!isset($_SESSION['client_addr'])) $_SESSION['client_addr'] = $_SERVER['REMOTE_ADDR'];
    }
    
    /**
     * Generate session id from session token
     * 
     * @return string
     */
    protected function generateSessionId($broker, $token, $client_addr=null)
    {
        if (!isset(self::$brokers[$broker])) return null;

        if (!isset($client_addr)) $client_addr = $_SERVER['REMOTE_ADDR'];
        return "SSO-{$broker}-{$token}-" . md5('session' . $token . $client_addr . self::$brokers[$broker]['secret']);
    }
    
    /**
     * Generate session id from session token
     * 
     * @return string
     */
    protected function generateAttachChecksum($broker, $token)
    {
        if (!isset(self::$brokers[$broker])) return null;
        return md5('attach' . $token . $_SERVER['REMOTE_ADDR'] . self::$brokers[$broker]['secret']);
    }
    
    /**
     * Authenticate
     */
    public function login()
    {
        $this->sessionStart();

        if (empty($_POST['username'])) $this->failLogin("No user specified");
        if (empty($_POST['password'])) $this->failLogin("No password specified");
        
        $u = User::where('staffCode',$_POST['username'])->where('password',$_POST['password'])->get()->toArray();
        if(count($u) == 0)
        {
          $this->failLogin("Incorrect credentials");     
        }

        $_SESSION['user'] = $_POST['username'];
   
        Log::info('method-----login');
        Log::warning('sessionID'.session_id());
        Log::warning('登录用户'.$_SESSION['user']);
        $user = $u[0];
        $this->info();
    }

    /**
     * Log out
     */
    public function logout()
    {
        $this->sessionStart();
        unset($_SESSION['user']);
        echo 1;
    }
    
    
    /**
     * Attach a user session to a broker session 
     */
    public function attach()
    {

        $this->sessionStart();
        
        
        if (empty($_REQUEST['broker'])) $this->fail("No broker specified");
        if (empty($_REQUEST['token'])) $this->fail("No token specified");
        if (empty($_REQUEST['checksum']) || $this->generateAttachChecksum($_REQUEST['broker'], $_REQUEST['token']) != $_REQUEST['checksum']) $this->fail("Invalid checksum");

  
        $bs = new BrokerSession();
        $bs->sessionKey = $this->generateSessionId($_REQUEST['broker'], $_REQUEST['token']);
        $bs->session_id = session_id();
        $bs->save();
     
  

        if (isset($_REQUEST['redirect'])) {
            header("Location: " . $_REQUEST['redirect'], true, 307);
            exit;        
        }
        
        // Output an image specially for AJAX apps
        // header("Content-Type: image/png");
        // readfile("empty.png");
    }
    
    /**
     * Ouput user information as XML.
     * Doesn't return e-mail address to brokers with security level < 2.
     */
    public function info()
    {
        $this->sessionStart();
        if (!isset($_SESSION['user'])) 
            {
 
               $this->failLogin("Not logged in");
            }
        $u = User::where('staffCode',$_SESSION['user'])->get()->toArray();
        if(count($u) == 0)
        {
             $this->failLogin("No UserInfo Found");
        }
        
        header('Content-type: text/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8" ?>', "\n";
        
        echo '<user identity="' . htmlspecialchars($_SESSION['user'], ENT_COMPAT, 'UTF-8') . '">';
        echo '  <name>' . htmlspecialchars($u[0]['name'], ENT_COMPAT, 'UTF-8') . '</name>';
        echo '  <email>' . htmlspecialchars($u[0]['email'], ENT_COMPAT, 'UTF-8') . '</email>';
        echo '</user>';
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
