<?php

namespace App\MyClasses;

class Mediafire
{
    private $app_id = 48222;
    private $api_key = "test";
    private $email = "test@mail.ru";
    private $password = "test";
    private $response_format = "json";
    private $url = [
        "USER_GET_SESSION_TOKEN" => "https://www.mediafire.com/api/user/get_session_token.php",
        "USER_GET_INFO" => "http://www.mediafire.com/api/user/get_info.php",
        "FOLDER_GET_CONTENT" => "http://www.mediafire.com/api/folder/get_content.php",
        "FILE_GET_LINKS" => "http://www.mediafire.com/api/file/get_links.php",
    ];

    private function getContents($url, $data, $curl = false)
    {
        if ($curl) {
            $handle = curl_init();

            curl_setopt($handle, CURLOPT_URL, $url);
            curl_setopt($handle, CURLOPT_POST, 1);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 60); // Количество секунд ожидания при попытке соединения. 0 - беск.
            curl_setopt($handle, CURLOPT_TIMEOUT, 60); // Максимально позволенное количество секунд для выполнения cURL-функций.

            $response = curl_exec($handle);
            $errno = curl_errno($handle);

            if ($errno) {
                $tmp = 'cUrl error №' . $errno . ' - ' . curl_error($handle) . '; ' . json_encode(curl_getinfo($handle));
                curl_close($handle);
                $this->showError($tmp);

            } else {
                curl_close($handle);
            }

        } else {
            $br = "\r\n";
            $tmp = parse_url($url);
            $query = $response = "";
            $tmp['host'] = str_replace("www.", "", $tmp['host']);

            if ($tmp['scheme'] == "https") {
                $handle = fsockopen("ssl://" . $tmp['host'], 443, $errno, $errstr);

            } else {
                $handle = fsockopen($tmp["host"], 80, $errno, $errstr);
            }

            if (!$handle) {
                $this->showError("Socket error: " . $errstr . " (" . $url . ")");
            }

            if (!empty($data) && is_array($data)) {
                $query = "?" . http_build_query($data);
            }

            $tmp_headers = apache_request_headers();
            $user_agent = $tmp_headers['User-Agent'];

            $header = "POST " . $tmp['scheme'] . "://" . $tmp["host"] . $tmp["path"] . $query . " HTTP/1.1" . $br;
            $header .= "Host: " . $tmp["host"] . $br;
            $header .= "Connection: Close" . $br;
            $header .= "User-agent: " . $user_agent . $br;
            $header .= $br;

            fwrite($handle, $header);

            while (!feof($handle)) {
                $tmp = fgets($handle, 32768);

                if ($tmp !== false) {
                    $response .= $tmp;
                }
            }

            fclose($handle);

            $aResponse = explode($br, $response);
            foreach ($aResponse as $val) {
                if (substr($val, 0, strlen('{"response":')) == '{"response":') {
                    $response = $val;
                    break;
                }
            }
        }


        return json_decode($response, true);
    }

    private function showError($msg)
    {
        throw new \Exception($msg);
    }

    private function getSignature()
    {
        return sha1($this->email . $this->password . $this->app_id . $this->api_key);
    }

    public function getSession()
    {
        $data = [
            "application_id" => $this->app_id,
            "signature" => $this->getSignature(),
            "email" => $this->email,
            "password" => $this->password,
            'token_version' => 2,
            "response_format" => $this->response_format,
        ];
        $res = $this->getContents($this->url["USER_GET_SESSION_TOKEN"], $data, false); // тут нужно строго через сокет

        if (empty($res['response']) || $res['response']['result'] == "Error") {
            if ($res['response']['result'] == "Error" && !empty($res['response']['message'])) {
                $this->showError($res['response']['message']);
            }

            $this->showError("Empty response");
        }

        return $res['response'];
    }

    public function getFiles($quick_key, $session_token, $secret_key, $time)
    {
        $aFiles = [];
        $array = [
            "folder_key" => $quick_key,
            "session_token" => $session_token,
            "content_type" => "files",
            "response_format" => $this->response_format,
            "details" => "no",
            "order_by" => "created",
        ];
        $array['signature'] = md5(((int)$secret_key % 256) . $time . "/api/folder/get_content.php?" . http_build_query($array));
        $res = $this->getContents($this->url["FOLDER_GET_CONTENT"], $array, true); // тут лучше ставить через curl

        if (!empty($res["response"]["folder_content"]["files"]) && sizeof($res["response"]["folder_content"]["files"])) {

            foreach ($res["response"]["folder_content"]["files"] as $val) {
                if (empty($val['links']['normal_download'])) {
                    continue;
                }

                $filename = $val['links']['normal_download'];
                $filename = basename($filename);
                $filename = str_replace("_", " ", $filename);

                // й
                if (strripos($filename, "%D0%B8%CC%86") !== false) {
                    $filename = str_replace("%D0%B8%CC%86", "%D0%B8", $filename); // й на и
                }
                // ё - %D1%91

                $filename = urldecode($filename);
                $aFiles[] = [
                    "quickkey" => $val["quickkey"],
                    "filename" => $val["filename"],
                    "filename2" => $filename,
                    "size" => (int)$val["size"],
                ];
            }
        }

        return $aFiles;
    }

    public function getLinks($quick_key, $session_token, $secret_key, $time)
    {
        $aLinks = [];
        $array = [
            "quick_key" => $quick_key,
            "session_token" => $session_token,
            "link_type" => "",
            "response_format" => $this->response_format,
        ];
        $array['signature'] = md5(((int)$secret_key % 256) . $time . "/api/file/get_links.php?" . http_build_query($array));
        $res = $this->getContents($this->url["FILE_GET_LINKS"], $array, true); // тут лучше ставить через curl

        if (!empty($res["response"]["links"][0]) && sizeof($res["response"]["links"][0])) {
            $aLinks = $res["response"]["links"][0];
        }

        return $aLinks;
    }
}