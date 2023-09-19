<?php

use Core\JwtAuth;
use Core\Session;
use Core\Utility;
use Core\Response;
use Core\TableField;
use Dotenv\Dotenv;

if(file_exists(Utility::parentPath() . 'vendor/autoload.php'))
{
    require Utility::parentPath() . 'vendor/autoload.php';
}

require Utility::parentPath() . 'core/helpers/themes.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '//../');
$dotenv->safeLoad();

function app($key, $default = null)
{
    $config = config('app');
    return isset($config[$key]) ? $config[$key] : $default;
}

function env($key, $default = null)
{
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}

function config($key)
{
    $parent_path = Utility::parentPath();
    
    $file = $parent_path . 'config/' . $key .'.php';

    if(file_exists($file))
    {
        return require $file;
    }

    return [];
}

function conn(){
    $database = config('database');
    $type = $database['driver'];
    if($type=='PDO')
    {
        // Connect using UNIX sockets
        if($database['socket'])
        {
            $dsn = sprintf(
                'mysql:dbname=%s;unix_socket=%s',
                $database['dbname'],
                $database['socket']
            );
        }
        else
        {
            $dsn = sprintf(
                'mysql:dbname=%s;host=%s',
                $database['dbname'],
                $database['host']
            );
        }

        // Connect to the database.
        $conn = new PDO($dsn, $database['username'], $database['password']);

        return $conn;
    }
    else
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            return new mysqli(
                $database['host'],
                $database['user'],
                $database['password'],
                $database['name'],
                $database['port'],
                $database['socket']
            );
        } catch (\mysqli_sql_exception $e) {
            echo $e->getMessage();
            die();
        }
    }

}

function startWith($str, $compare)
{
    return substr($str, 0, strlen($compare)) === $compare;
}

function routeTo($path = false, $param = [])
{
    $path = $path == '/' ? '' : $path;
    $pretty = true;
    $base_url = base_url();
    if($param)
    {
        $param = http_build_query($param);
        $param = $pretty ? '?'.$param : '&'.$param;
    }
    else
    {
        $param = '';
    }
    if($pretty)
    {
        return $base_url.'/'.$path.$param;
    }
    return $base_url.'/index.php?r='.$path.$param;
}

function base_url()
{
    return url(); // config('base_url');
}

function url(){
    return app('url');
}

function auth()
{
    // mode jwt
    if(config('auth') == 'jwt')
        return JwtAuth::get();
    if(config('auth') == 'session')
        return Session::get();
}


function stringContains($string,$val){
    if (strpos($string, $val) !== false) {
        return true;
    }

    return false;
}

function arrStringContains($string,$arr){

    $result = [];

    for($i = 0; $i < count($arr);$i++){
       $result[] = stringContains($string,$arr[$i]);
    }

    return in_array(true,$result);
}

function startsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    return substr( $haystack, 0, $length ) === $needle;
}

function endsWith( $haystack, $needle ) {
   $length = strlen( $needle );
   if( !$length ) {
       return true;
   }
   return substr( $haystack, -$length ) === $needle;
}

function set_flash_msg($data)
{
    $_SESSION['flash'] = $data;
}

function get_flash_msg($key)
{
    if(isset($_SESSION['flash'][$key]))
    {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    return false;
}

/**
 * Wrapper for easy cURLing
 *
 * @author Viliam Kopecký
 *
 * @param string HTTP method (GET|POST|PUT|DELETE)
 * @param string URI
 * @param mixed content for POST and PUT methods
 * @param array headers
 * @param array curl options
 * @return array of 'headers', 'content', 'error'
 */
function simple_curl($uri, $method='GET', $data=null, $curl_headers=array(), $curl_options=array()) {
	// defaults
	$default_curl_options = array(
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_HEADER => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 3,
	);
	$default_headers = array();

	// validate input
	$method = strtoupper(trim($method));
	$allowed_methods = array('GET', 'POST', 'PUT', 'DELETE');

	if(!in_array($method, $allowed_methods))
		throw new \Exception("'$method' is not valid cURL HTTP method.");

	// init
	$curl = curl_init($uri);

	// apply default options
	curl_setopt_array($curl, $default_curl_options);

	// apply method specific options
	switch($method) {
		case 'GET':
			break;
		case 'POST':
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;
		case 'PUT':
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;
		case 'DELETE':
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
			break;
	}

	// apply user options
	curl_setopt_array($curl, $curl_options);

	// add headers
	curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge($default_headers, $curl_headers));

	// parse result
	$raw = rtrim(curl_exec($curl));
	$lines = explode("\r\n", $raw);
	$headers = array();
	$content = '';
	$write_content = false;
	if(count($lines) > 3) {
		foreach($lines as $h) {
			if($h == '')
				$write_content = true;
			else {
				if($write_content)
					$content .= $h."\n";
				else
					$headers[] = $h;
			}
		}
	}
	$error = curl_error($curl);

	curl_close($curl);

	// return
	return array(
		'raw' => $raw,
		'headers' => $headers,
		'content' => $content,
		'error' => $error
	);
}

function _ucwords($str)
{
    $str = str_replace('_',' ',$str);
    $str = str_replace('-',' ',$str);

    return ucwords($str);
}

function redirectBack($message = [])
{
    if($message)
    {
        set_flash_msg($message);
        $url = $_SERVER['HTTP_REFERER']??base_url();
        header('location:'.$url);
        die();
    }
}

function asset($file)
{
    return url() . '/' .$file;
}

/** 
 * Get header Authorization
 * */
function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

/**
 * get access token from header
 * */
function getBearerToken() {
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function view($file, $data = [])
{    
    return Response::view($file, $data);
}

function getModules()
{
    $parent_path = Utility::parentPath();
    $dir = $parent_path . 'modules';
    $folders = scandir($dir);
    $modules = [];

    foreach($folders as $folder)
    {
        if (!in_array($folder,array(".","..")))
        {
            $modules[] = "modules/$folder";
        }
    }

    return $modules;
}

function __($string)
{
    // module.string -> module/lang/{localization}/{file}.php [string]
    $data = explode('.', $string);
    if(stringContains($string, '.') && is_array($data))
    {
        $module = $data[0];
        $langFile = $data[1];
        $localization = app('localization');
        $file = Utility::parentPath() . "modules/" . $module . "/config/lang/$localization/$langFile.php";
        if(file_exists($file))
        {
            $lang = require $file;
            return isset($lang[$data[2]]) ? $lang[$data[2]] : $string;
        }
    }

    return $string;
}

function tableFields($tbl = false)
{
    $tableFields = [];
    $modules = getModules();

    foreach($modules as $module)
    {
        $file = Utility::parentPath() . $module . "/config/table-fields.php";
        if(file_exists($file))
        {
            $tables = require $file;
            foreach($tables as $table => $fields)
            {
                $tableFields = array_merge($tableFields, [$table => new TableField($table, $fields, str_replace('modules/','',$module))]);
            }
        }
    }

    if($table)
    {
        if(isset($tableFields[$tbl]))
        {
            return $tableFields[$tbl];
        }

        return [];
    }

    return $tableFields;
}