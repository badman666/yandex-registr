<?php
set_time_limit(0);
ini_set('memory_limit', '-1');

require_once 'functions.php';
// get tokens
$tokens = require_once 'tokens.php';

foreach ($tokens as $login => $token) {
    write_log('log', '-------------------------');
    write_log('log', '-> ' . $login);
    write_log('log', '-------------------------');

    $userId = get_user_id($token);
    $hostsInfo = get_list_sites($userId, $token);

    $hostsIds = [];
    foreach ($hostsInfo as $host) {
        if ($host->verified == 1 && empty($host->main_mirror)) {
            $hostsIds[] = $host->host_id;
        } elseif (empty($host->main_mirror)) {
            $hostsIds[] = $host->main_mirror->host_id;
        }
    }

    $hostsIds = array_unique($hostsIds);
    sort($hostsIds);
    $hostsNames = [];

    foreach($hostsIds as $hostId) {
        $res = get_info_site($userId, $hostId, $token);
        $hostsNames[] = $res->host_display_name;
    }

    $sitesForCheck = [];
    foreach ($hostsNames as $name) {

        $search = ['www.', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $replace = ['', '', '', '', '', '', '', '', '', '', ''];

        $clearName = str_replace($search, $replace, $name);
        $clearName = explode('.', $clearName)[0];

        if (ctype_lower($clearName)) {
            //$sitesForCheck[] = $name;
            //echo $name . PHP_EOL;
            write_log('log', $name);
        }
    }
    write_log('log',PHP_EOL . PHP_EOL . PHP_EOL);
}
