<?php  

function lang($phrase){
    static $lang = array(
        'MESSAGE' => 'مرحبا',
        'ADMIN' => 'مدير'
    );
    return $lang[$phrase];
}