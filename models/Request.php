<?php

namespace models;

/**
 * Class Request
 * @package models
 */
class Request
{
    private $userAgent;

    public function __construct($userAgent = false)
    {
        if ($userAgent) {
            $this->userAgent = trim(htmlspecialchars($userAgent));
        } else {
            $this->userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36';
        }
    }

    public function clearHost($host)
    {
        $host = trim(htmlspecialchars($host));
        $search = ['http://', 'https://', '/', 'www.', ':443'];
        $replace = ['', '', '', '', ''];
        $host = str_replace($search, $replace, $host);
        $host = mb_strtolower($host);

        return $host;
    }

    public function getIp($host)
    {
        return gethostbyname($this->clearHost($host));
    }

    /**
     * analog get_headers()
     * @param $stringHeaders
     * @return array
     */
    public function normalizeHeaders($stringHeaders)
    {
        $stringHeaders = explode("\r\n\r\n", $stringHeaders);
        $stringHeaders = $stringHeaders[0];
        $stringHeaders = explode("\n", $stringHeaders);

        $headers = [];
        foreach ($stringHeaders as $key => $value) {
            if ($key == 0) {
                $headers[0] = trim($value);
            } else {
                $keys = explode(': ', $value);
                $headers[trim($keys[0])] = trim($keys[1]);
            }
        }

        if (isset($headers['location'])) {
            $headers['Location'] = $headers['location'];
            unset($headers['location']);
        }

        return $headers;
    }

    public function getContent($url, $data = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }

    public function getHeaders($url, $data = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }

    public function getMultiContent($nodes)
    {
        $mh = curl_multi_init();
        $curl_array = [];
        foreach($nodes as $i => $url) {
            $curl_array[$i] = curl_init($url);
            curl_setopt($curl_array[$i], CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_array[$i], CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl_array[$i], CURLOPT_TIMEOUT, 30);
            curl_setopt($curl_array[$i], CURLOPT_USERAGENT, $this->userAgent);
            curl_multi_add_handle($mh, $curl_array[$i]);
        }

        $running = null;
        do {
            usleep(10000);
            curl_multi_exec($mh,$running);
        } while($running > 0);

        $res = [];
        foreach($nodes as $i => $url) {
            $res[$url] = curl_multi_getcontent($curl_array[$i]);
        }

        foreach($nodes as $i => $url) {
            curl_multi_remove_handle($mh, $curl_array[$i]);
        }
        curl_multi_close($mh);

        return $res;
    }

    public function getMultiHeaders($nodes)
    {
        $mh = curl_multi_init();
        $curl_array = [];
        foreach($nodes as $i => $url) {
            $curl_array[$i] = curl_init($url);
            curl_setopt($curl_array[$i], CURLOPT_HEADER, true);
            curl_setopt($curl_array[$i], CURLOPT_NOBODY, true);
            curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_array[$i], CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl_array[$i], CURLOPT_TIMEOUT, 30);
            curl_setopt($curl_array[$i], CURLOPT_USERAGENT, $this->userAgent);
            curl_multi_add_handle($mh, $curl_array[$i]);
        }

        $running = null;
        do {
            usleep(10000);
            curl_multi_exec($mh,$running);
        } while($running > 0);

        $res = [];
        foreach($nodes as $i => $url) {
            $res[$url] = curl_multi_getcontent($curl_array[$i]);
        }

        foreach($nodes as $i => $url) {
            curl_multi_remove_handle($mh, $curl_array[$i]);
        }
        curl_multi_close($mh);

        return $res;
    }

}