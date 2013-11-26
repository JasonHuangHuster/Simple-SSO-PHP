<?php
/**
 * Helper class for broker of single sign-on
 */
class SSOController extends BaseController {
    /**
     *  是否显示用户为验证的消息
     */
    public $pass401=false;

    /**
     * SSO 端 URL
     * @var string
     */
    // public $url = 'http://localhost/LaravelTest/public/entrance';

      public $url = "http://huangdangwu.sso.devel.oppo.com/entrance";
    
    /**
     * 本系统的身份
     * @var string
     */
    public $broker = "JXKH";

    /**
     * 本系统身份认证的密码
     * @var string
     */
    public $secret = "Jason";

    /**
     * 设置时长应该比SSO端短
     * @var string
     */
    public $sessionExpire = 1800;
    
    /**
     * 生成的随机码
     * @var string
     */
    protected $sessionToken;
    
    /**
     * 从SSO端获得的用户信息
     * @var array
     */
    protected $userinfo;
    
    
    protected static $sso ;

    public static function getInstance()
    {
       if(isset($sso))
       {
         return self::$sso;
       }else{
         return new SSOController(true);
       }

    }
    /**
     * Class constructor
     */
    public function __construct($auto_attach=true)
    {

      if (isset($_COOKIE['session_token'])) $this->sessionToken = $_COOKIE['session_token'];
        
        if ($auto_attach && !isset($this->sessionToken)) {
           
             $this->attach();
        }
    }
    
    /**
     * 生成Token
     * 
     * @return string
     */
    public function getSessionToken()
    {
        if (!isset($this->sessionToken)) {
            $this->sessionToken = md5(uniqid(rand(), true));
            setcookie('session_token', $this->sessionToken, time() + $this->sessionExpire);
        }
        
        return $this->sessionToken;
    }
    
    /**
     * 根据生成的Token生成SESSIONKEY
     * 
     * @return string
     */
    protected function getSessionId()
    {
        if (!isset($this->sessionToken)) return null;
        return "SSO-{$this->broker}-{$this->sessionToken}-" . md5('session' . $this->sessionToken . $_SERVER['REMOTE_ADDR'] . $this->secret);
    }

    /**
     * 生成绑定SSO的URL
     *
     * @return string
     */
    public function getAttachUrl($regenerate = true)
    {
        if($regenerate){
        $token = $this->getSessionToken();    
        }else{
        $token = $this->sessionToken;
        }
        
        $checksum = md5("attach{$token}{$_SERVER['REMOTE_ADDR']}{$this->secret}");
        return "{$this->url}?method=attach&broker={$this->broker}&token=$token&checksum=$checksum";
    }    


    public function attach(){

        if(!isset($this->sessionToken)) {
            header("Location: " . $this->getAttachUrl() . "&redirect=". urlencode("http://{$_SERVER["SERVER_NAME"]}{$_SERVER["REQUEST_URI"]}"), true, 307);
            exit;
        }else
        {
            header("Location: " . $this->getAttachUrl(false) . "&redirect=". urlencode("http://{$_SERVER["SERVER_NAME"]}{$_SERVER["REQUEST_URI"]}"), true, 307);
            exit;
        }
    }
    
    
    /**
     * 登录SSO
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function login($username=null, $password=null)
    {
        $name = Input::get('username');
        $pw = Input::get('password');

        if (!isset($username) && isset($name)) $username=$name;
        if (!isset($password) && isset($pw)) $password=$pw;
        
        list($ret, $body) = $this->serverCmd('login', array('username'=>$username, 'password'=>$password));
        echo "CODE: ". $ret.'::';
        switch ($ret) {
            case 200: $this->parseInfo($body);
                      
                      return 1;
            case 401: if ($this->pass401) header("HTTP/1.1 401 Unauthorized");
                      return 0;
            case 406: $this->attach();
                      return 0;
            default:  throw new Exception("SSO failure: The server responded with a $ret status" . (!empty($body) ? ': "' . substr(str_replace("\n", " ", trim(strip_tags($body))), 0, 256) .'".' : '.'));
        }
    }
    
    /**
     * Logout at sso server.
     */
    public function logout()
    {
        list($ret, $body) = $this->serverCmd('logout');
        if ($ret != 200) throw new Exception("SSO failure: The server responded with a $ret status" . (!empty($body) ? ': "' . substr(str_replace("\n", " ", trim(strip_tags($body))), 0, 256) .'".' : '.'));
        
        return true;
    }
    
    
    /**
     * 从收到的XML中读取user信息
     *
     * @param string $xml
     */
    protected function parseInfo($xml)
    {
     
        // print_r($xml);

        $sxml = new SimpleXMLElement($xml);
        
        $this->userinfo['identity'] = $sxml['identity'];
        foreach ($sxml as $key=>$value) {
         $this->userinfo[$key] = (string)$value;    
        }

        // print_r($this->userinfo);
        
        return $this->userinfo;

    }
    
    /**
     * 获取用户信息
     */
    public function getInfo()
    {
      
        if (!isset($this->userinfo)) {
            list($ret, $body) = $this->serverCmd('info');
            echo "<br>";
            echo 'response:'.$ret;
            switch ($ret) {
                case 200: return $this->parseInfo($body); break;
                case 401: if ($this->pass401) header("HTTP/1.1 401 Unauthorized");
                          $this->userinfo = false; break;
                case 406: $this->attach(); break; 
                default:  throw new Exception("SSO failure: The server responded with a $ret status" . (!empty($body) ? ': "' . substr(str_replace("\n", " ", trim(strip_tags($body))), 0, 256) .'".' : '.'));
            }
        }
        return $this->userinfo;
        
    }
    
    /**
     * Ouput user information as XML
     */
    public function info()
    {
        $this->getInfo();
        
        if (!$this->userinfo) {
            header("HTTP/1.0 401 Unauthorized");
            echo "Not logged in";
            exit;
        }
        
        header('Content-type: text/xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8" ?>', "\n";
        echo '<user identity="' . htmlspecialchars($this->userinfo['identity'], ENT_COMPAT, 'UTF-8') . '">', "\n";
        
        foreach ($this->userinfo as $key=>$value) {
            if ($key == 'identity') continue;
            echo "<$key>", htmlspecialchars($value, ENT_COMPAT, 'UTF-8'), "</$key>", "\n";
        }
        
        echo '</user>';
    }
    

    /**
     * 利用CURL与SSO通信
     *
     * @param string $cmd   Command
     * @param array  $vars  Post variables
     * @return array
     */
    protected function serverCmd($cmd, $vars=null)
    {

        $curl = curl_init($this->url.'?method='.urlencode($cmd));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIE, "PHPSESSID=" . $this->getSessionId());
        

        if (isset($vars)) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $vars);
        }
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,6);

        
      
        $body = curl_exec($curl);
        $ret = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl) != 0) throw new Exception("SSO failure: HTTP request to server failed. " . curl_error($curl));
        
        return array($ret, $body);
    }
}

// Execute controller command
/*if (realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__) && isset($_GET['cmd'])) {
    $ctl = new SingleSignOn_Broker(false);
    $ctl->pass401 = true;
    $ret = $ctl->$_GET['cmd']();

    if (is_scalar($ret)) echo $ret;
}
*/

