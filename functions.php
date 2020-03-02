<?php
/**
 * @param $url
 * @param $headers
 * @return mixed
 */
function curl_get_req($url, $headers)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result =curl_exec($ch);
    curl_close($ch);

    return $result;
}

/**
 * get id user
 * @param $token
 * @return mixed
 */
function get_user_id($token)
{
    $url = "https://api.webmaster.yandex.net/v3/user/";
    $headers = array("Authorization: OAuth {$token}", "Content-type: application/json;charset=UTF-8");

    $result = json_decode( curl_get_req($url, $headers) );

    if ($result->user_id)
        $response = $result->user_id;
    else
        $response = $result;

    return $response;
}

/**
 * get list sites user
 * @param $user_id
 * @param $token
 * @return mixed
 */
function get_list_sites($user_id, $token)
{
    $url = "https://api.webmaster.yandex.net/v3/user/{$user_id}/hosts/";
    $headers = array("Authorization: OAuth {$token}", "Content-type: application/json;charset=UTF-8");

    $result = json_decode( curl_get_req($url, $headers) );

    if ($result->hosts)
        $response = $result->hosts;
    else
        $response = $result;

    return $response;
}

/**
 * получить статистику ( - по документации, по факту информация) сайта
 * @param $user_id
 * @param $host_id
 * @param $token
 * @return bool|mixed
 */
function get_info_site($user_id, $host_id, $token)
{
    $url = "https://api.webmaster.yandex.net/v3/user/{$user_id}/hosts/{$host_id}/";
    $headers = array("Authorization: OAuth {$token}", "Content-type: application/json;charset=UTF-8");

    $result = json_decode( curl_get_req($url, $headers) );

    if ($result)
        $response = $result;
    else
        $response = false;

    return $response;
}

function write_log($log_name, $log_msg) {
    $f = fopen("./logs/{$log_name}.txt", "a+");
    fwrite($f, "{$log_msg}\n");
    fclose($f);
}