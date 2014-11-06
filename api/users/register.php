<?php
/**
 * Staging API server:      https://api-staging.vietnamworks.com
 * Production API server:   https://api.vietnamworks.com
 *
 * Request:
 *      POST https://{api_server_domain}/users/register
 *      headers:
 *          CONTENT-MD5: your_api_key
 *      Parameters:
 *          email                       string      required
 *          password                    string      required
 *          first_name                  string      required
 *          last_name                   string      required
 *          birthday                    string      optional    format (dd/mm/yyy)
 *          gender                      integer     optional    1: Male ; 2: Female
 *          nationality                 integer     optional    country_id
 *          residence                   string      optional    address
 *          home_phone                  string      optional
 *          cell_phone                  string      optional
 *          language                    integer     optional    1: Vietnamese ; 2: English; 3: Japanese
 *              values reference at /general/configuration API with path data->languages->language_id
 *
 * Response on error:
 *      HTTP/1.1 400 Bad Request
 *      Content-Type: application/json
 *      body: {"message": "explain about happened error"}
 *
 * Response on success:
 *      HTTP/1.1 200 OK
 *      Content-Type: application/json
 *      body: { "userID": "new id of user"}
 *          an activation email is sent to email address of user
 */

$apiKey     = 'your_api_key';
$apiHost    = 'https://api-staging.vietnamworks.com';
$apiPath    = '/users/register';

$jsonString = json_encode(array(
    'email'             => 'test_api@yopmail.com',
    'password'          => 'This is PaSSword',
    'first_name'        => 'Test',
    'last_name'         => 'User',
    'birthday'          => '14/02/2001',
    'gender'            => 1,               // Male
    'nationality'       => 1,               // Vietnam
    'residence'         => "123 Truong Dinh street, Ho Chi Minh city",
    'home_phone'        => '0839258456',
    'cell_phone'        => '0901234567',
    'language'          => 1                // Vietnamese
));

$ch = curl_init();

curl_setopt_array($ch, array(
    CURLOPT_URL             => $apiHost.$apiPath,
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_SSL_VERIFYPEER  => false, // ignore SSL verification
    CURLOPT_POST            => true,  // http request method is POST
    CURLOPT_HTTPHEADER      => array(
        "CONTENT-MD5: $apiKey",
        'Content-Type: application/json',
        'Content-Length: '.strlen($jsonString)
    ),
    CURLOPT_POSTFIELDS      => $jsonString
));

$response = curl_exec($ch);
$responseArray = (array)json_decode($response, true);;

if ($responseArray['meta']['code'] == 400) { // error happened
    echo 'Server returned an error with message: '.$responseArray['meta']['message'];
} elseif ($responseArray['meta']['code'] == 200)  {
    echo "Response status: ".$responseArray['meta']['message']."\nNew userID: ".$responseArray['data']['userID'];
} else {
    //unknown error
    $info = curl_getinfo($ch);
    echo "An error happened: \n".curl_error($ch)."\nInformation: ".print_r($info, true)."\nResponse: $response";
}

curl_close($ch);