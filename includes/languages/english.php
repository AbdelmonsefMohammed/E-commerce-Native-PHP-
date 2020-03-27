<?php  

function lang($phrase){
    static $lang = array(
        //NAVBAR links
        'HOME_ADMIN' => 'Home',
        'CATEGORIES' => 'Categories',
        'ITEMS' => 'Items',
        'MEMBERS' => 'Members',
        'COMMENTS' => 'Comments',
        'STATISTICS' => 'Statistics',
        'LOGS' => 'Logs',
        'ADMIN-NAME' => 'Monsef',
        'DROP-OP1' => 'Edit profile',
        'DROP-OP2' => 'Settings',
        'DROP-OP3' => 'Logout'

    );
    return $lang[$phrase];
}