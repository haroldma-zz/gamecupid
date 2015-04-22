<?php
/**
 * Created by PhpStorm.
 * User: harry
 * Date: 4/22/15
 * Time: 2:03 PM
 */

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class PlatformValidatorController extends Controller {

    function validatePsn(LoginFormRequest $request) {
        // let's start by creating a Guzzle Client to make http request
        $client = new Client();

        // Make a post request to the psn oauth api, if successful then we're golden!
        $response = $client.post('https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/token', [
                'body' => [
                    // we're going to be using password for authentication
                    'grant_type' => 'password',
                    // this comes from the PS app on Android
                    'client_id' => 'b0d0d7ad-bb99-4ab1-b25e-afa0c76577b0',
                    // really wasn't that hard to get ;)
                    'client_secret' => 'Zo4y8eGIa3oazIEp',
                    // this are the default the app uses, not even sure if we need all of them...
                    'scope' => 'psn%3Asceapp%2Cuser%3Aaccount.get%2Cuser%3Aaccount.settings.privacy.get%2Cuser%3Aaccount.settings.privacy.update%2Cuser%3Aaccount.realName.get%2Cuser%3Aaccount.realName.update',
                    // have to test if we even need this for password grant
                    'code' => 'INXh7h&service_entity=urn%3Aservice-entity%3Apsn&duid=0000000700020128cc4b8184a6e77d6f3a4c4745202020202020203a68616d6d65726865616400000000000000',
                    // the username (actually email address but according to oauth it have to use the username field
                    'username' => $request->get('email'),
                    // self-explanatory
                    'password' => $request->get('password')
                ],
                'headers' => [
                    // client id+client secret (base64)
                    'Authorization' => 'Basic YjBkMGQ3YWQtYmI5OS00YWIxLWIyNWUtYWZhMGM3NjU3N2IwOlpvNHk4ZUdJYTNvYXpJRXA='
                ]
            ]);

        // 200? then the user has logged in successfully
        $valid = $response->getStatusCode() == 200;

        if ($valid) {
            // parsing the access token from the response
            $json = $response->json();
            $token = $json->access_token;

            $response = $client.get('https://vl.api.np.km.playstation.net/vl/api/v1/mobile/users/me/info', [
                    'headers' => [
                        // To call psn api we use the following header to authenticate
                        'X-NP-ACCESS-TOKEN' => $token
                    ]
                ]);

            $valid = $response->getStatusCode() == 200;

            if ($valid) {
                // parsing the user id and username from response
                $json = $response->json();
                $user_id = $json->accountId;
                $username = $json->onlineId;

                // TODO save this in the database
            }
        }
        return view('user.profile.link', ['valid' => $valid]);
    }
}