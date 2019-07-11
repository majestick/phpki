<?php

#$PHP_SELF = $_SERVER['PHP_SELF'];
$PHP_SELF = $_SERVER['PHP_SELF'];

#
# Will convert /path/to/test/.././..//..///..///../one/two/../three/filename
# to ../../one/three/filename
#
function normalizePath($path)
{
    $parts = array();// Array to build a new path from the good parts
    $path = str_replace('\\', '/', $path);// Replace backslashes with forwardslashes
    $path = preg_replace('/\/+/', '/', $path);// Combine multiple slashes into a single slash
    $segments = explode('/', $path);// Collect path segments
    $test = '';// Initialize testing variable
    foreach($segments as $segment)
    {
        if($segment != '.')
        {
            $test = array_pop($parts);
            if(is_null($test))
                $parts[] = $segment;
            else if($segment == '..')
            {
                if($test == '..')
                    $parts[] = $test;

                if($test == '..' || $test == '')
                    $parts[] = $segment;
            }
            else
            {
                $parts[] = $test;
                $parts[] = $segment;
            }
        }
    }
    return implode('/', $parts);
}

#
# Returns TRUE if browser is Internet Explorer.
#
function isIE() {
	global $_SERVER;
	return strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE');
}

function isKonq() {
	global $_SERVER;
	return strstr($_SERVER['HTTP_USER_AGENT'], 'Konqueror');
}

function isMoz() {
	global $_SERVER;
	return strstr($_SERVER['HTTP_USER_AGENT'], 'Gecko');
}


#
# Force upload of specified file to browser.
#
function upload($source, $destination, $content_type="application/octet-stream") {
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	header("Expires: -1");
#	header("Cache-Control: no-store, no-cache, must-revalidate");
#	header("Cache-Control: post-check=0, pre-check=0", false);
#	header("Pragma: no-cache");
    header("Content-Type: $content_type");

	if (is_array($source)) {
		$fsize = 0;
		foreach($source as $f) $fsize += filesize($f);
	}
	else {
		$fsize = filesize($source);
	}

	header("Content-length: " . $fsize);
#        header("Content-Disposition: attachment; filename=\"" . $destination ."\"");
        header("Content-Disposition: filename=\"" . $destination ."\"");

	if (is_array($source))
		foreach($source as $f) $ret = readfile($f);
	else 
        	$ret=readfile($source);

#        $fd=fopen($source,'r');
#        fpassthru($fd);
#        fclose($fd);
}


#
# Returns a value from the GET/POST global array referenced
# by field name.  POST fields have precedence over GET fields.
# Quoting/Slashes are stripped if magic quotes gpc is on.
#
function gpvar($v) {
	global $_GET, $_POST;
    $x = "";
	if (isset($_GET[$v]))  $x = $_GET[$v];
	if (isset($_POST[$v])) $x = $_POST[$v];
	if (get_magic_quotes_gpc()) $x = stripslashes($x);
	return $x;
}


#
# Sort a two multidimensional array by one of it's columns
#
function csort($array, $column, $ascdec=SORT_ASC){    
	if (sizeof($array) == 0) return $array;

	foreach($array as $x) $sortarr[]=$x[$column];
	array_multisort($sortarr, $ascdec, $array);  

	return $array;
}


#
# Returns a value suitable for display in the browser.
# Strips slashes if second argument is true.
#
function htvar($v, $strip=false) {
	if ($strip) 
		return  htmlentities(stripslashes($v), 0, "UTF-8");
	else
		return  htmlentities($v, 0, "UTF-8");
}


#
# Returns a value suitable for use as a shell argument.
# Strips slashes if magic quotes is on, surrounds
# provided strings with single-quotes and quotes any
# other dangerous characters.
#
function escshellarg($v, $strip=false) {
	if ($strip)
		return escapeshellarg(stripslashes($v));
	else
		return escapeshellarg($v);
}


#
# Similar to escshellarg(), but doesn't surround provided
# string with single-quotes.
#
function escshellcmd($v, $strip=false) {
	if ($strip)
		return escapeshellcmd(stripslashes($v));
	else
		return escapeshellarg($v);
}
	
#
# Recursively strips slashes from a string or array.
#
function stripslashes_array(&$a) {
	if (is_array($a)) {
		foreach($a as $k => $v) {
			my_stripslashes($a[$k]);
		}
	}
	else {
		$a = stripslashes($a);
	}
}


#
# Don't use this.
#
function undo_magic_quotes(&$a) {
	if(get_magic_quotes_gpc()) {
		global $HTTP_POST_VARS, $HTTP_GET_VARS;

		foreach($HTTP_POST_VARS as $k => $v) {
			stripslashes_array($HTTP_POST_VARS[$k]);
			global $$k;
			stripslashes_array($$k);
		}
		foreach($HTTP_GET_VARS as $k => $v) {
			stripslashes_array($HTTP_GET_VARS[$k]);
			global $$k;
			stripslashes_array($$k);
		}
	}
}

#
# Returns TRUE if argument contains only alphabetic characters.
#
function is_alpha($v) {
	return (preg_match('[^A-Z]',$v) ? false : true) ;
}

#
# Returns TRUE if argument contains only numeric characters.
#
function is_num($v) {
	return (preg_match('[^0-9]',$v) ? false : true) ;
}

#
# Returns TRUE if argument contains only alphanumeric characters.
#
function is_alnum($v) {
	return (preg_match('[^A-Z0-9]',$v) ? false : true) ;
}

#
# Returns TRUE if argument is in proper e-mail address format.
#
function is_email($v) {
	return (preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD',$v) ? true : false);
}

#
# Returns True if the given string is a IP address
#
function is_ip( $ip = null ) {
    if( !$ip or strlen(trim($ip)) == 0){
        return false;
    }
    $ip=trim($ip);
    if(preg_match("/^[0-9]{1,3}(.[0-9]{1,3}){3}$/",$ip)) {
        foreach(explode(".", $ip) as $block)
            if($block<0 || $block>255 )
                return false;
        return true;
    }
    return false;
}

#
# Returns True if the given string is a valid FQDN
#
function is_fqdn($FQDN) {
    // remove leading wildcard characters if exist
    $FQDN = preg_replace('/^\*\./','', $FQDN, 1);
    return (!empty($FQDN) && preg_match('/^(?=.{1,254}$)((?=[a-z0-9-]{1,63}\.)(xn--+)?[a-z0-9]+(-[a-z0-9]+)*\.)+(xn--+)?[a-z0-9]{2,63}$/i', $FQDN) > 0);
}

#
# Checks regexp in every element of an array, returns TRUE as soon
# as a match is found.
#
function eregi_array($regexp, $a) {

foreach($a as $e) {
	if (preg_match($regexp,$e)) return true;
}
return false;
}

#
# Reads entire file into a string
# Same as file_get_contents in php >= 4.3.0
#
function my_file_get_contents($f) {
	return implode('', file($f));
}

?>
