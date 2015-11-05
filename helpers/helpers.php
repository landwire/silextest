<?php
// helper
function removeElementWithValue($array, $key, $value){
     foreach($array as $subKey => $subArray){
          if($subArray[$key] == $value){
               unset($array[$subKey]);
          }
     }
     return $array;
}

function strToBool($str) {
    return ($str==='true' || $str==='1' ? true : false);
}