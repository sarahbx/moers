<?php
//nolicense_functions.php
////////////////////////////////////////////////////////////////////////
// The code that follows is not subject to any version of the GPL,
// nor does it have any license attached to it.
// If you find this statement to be false, please contact me with
// more info: Sarah Bennert sarah@xhub.com
////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////
// Function 'rand_string'
// Code Sourced from:
// http://us3.php.net/manual/en/function.mt-rand.php#76658
// posted by 'www.mrnaz.com'
// 
// 2/19/08 - added lowercase letters
function rand_string($len, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
{
    $string = '';
    for ($i = 0; $i < $len; $i++)
    {
        $pos = rand(0, strlen($chars)-1);
        $string .= $chars{$pos};
    }
    return $string;
}

?>
