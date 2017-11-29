<?php
/**
 * Created by PhpStorm.
 * User: driesc
 * Date: 16/11/2017
 * Time: 19:35
 */

class Service
{

    //get data form dataservice
    //returns data as php array
    //TODO: error handling
    public static function get($location) {
        $url = "10.3.50.22/api/{$location}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);
        // set http header token for authorization
        curl_setopt($ch, CURLOPT_HTTPHEADER,["Authorization: Bearer {$_SESSION["token"]}"]);
        $jsonstring = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //201: created, 409: conflict, 500: internal server error (bad data), 404: not found,  401: foute credentials, 200: OK, 400: foute variabelen

        curl_close($ch);

        //catch request error
        if ($jsonstring === false || $jsonstring == "") {
            return false;
        }

        $result = json_decode($jsonstring, true);

        if ($result == false || $result == NULL) {
            return false;
        }

        if ($httpcode == 200 || $httpcode == 201 || $httpcode == 204) {
            return $result;
        } else {
            return false;
        }
    }

    public static function post($location, $curl_post_data) {
        $data = json_encode($curl_post_data);
        $url = "10.3.50.22/api/{$location}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['Content-Type: application/json']);
        // set http header token for authorization
        curl_setopt($ch, CURLOPT_HTTPHEADER,["Authorization: Bearer {$_SESSION["token"]}"]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);
        $jsonstring = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //201: created, 409: conflict, 500: internal server error (bad data), 404: not found,  401: foute credentials, 200: OK, 400: foute variabelen

        curl_close($ch);

        //catch request error
        if ($jsonstring === false || $jsonstring == "") {
            return false;
        }

        $result = json_decode($jsonstring, true);

        if ($result == false || $result == NULL) {
            return false;
        }

        if ($httpcode == 200 || $httpcode == 201 || $httpcode == 204) {
            return $result;
        } else {
            return false;
        }
    }

    public static function put($location, $curl_put_data) {
        $data = json_encode($curl_put_data);
        $url = "10.3.50.22/api/{$location}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['Content-Type: application/json']);
        // set http header token for authorization
        curl_setopt($ch, CURLOPT_HTTPHEADER,["Authorization: Bearer {$_SESSION["token"]}"]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //201: created, 409: conflict, 500: internal server error (bad data), 404: not found,  401: foute credentials, 200: OK, 400: foute variabelen

        curl_close($ch);

        if ($httpcode == 204) {
            return true;
        } else {
            return false;
        }
    }
}