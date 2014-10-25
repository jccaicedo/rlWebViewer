<?php

function ds($x){
  echo "<pre>";
  print_r($x);
  echo "</pre>";
}

function startsWith($haystack, $needle)
{
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function safeValue(&$var,$default){
  if(isset($var)){
    return $var;
  }else{
    return $default;
  }
}

?>
