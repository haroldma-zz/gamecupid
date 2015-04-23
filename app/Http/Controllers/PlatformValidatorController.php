<?php
/**
 * Created by PhpStorm.
 * User: harry
 * Date: 4/22/15
 * Time: 2:03 PM
 */

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Http\Requests\ConnectPsnFormRequest;

class PlatformValidatorController extends Controller
{

    public function validatePsn(ConnectPsnFormRequest $request)
    {
        // let's start by creating a Guzzle Client to make http request
        $client = new Client();

        // Make a post request to the psn oauth api, if successful then we're golden!
        $response = $client->post('https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/token', [
            'body' => [
                // we're going to be using password for authentication
                'grant_type' => 'password',
                // this are the default the app uses, not even sure if we need all of them...
                'scope' => 'psn:sceapp,user:account.get,user:account.settings.privacy.get,user:account.settings.privacy.update,user:account.realName.get,user:account.realName.update',
                // the username (actually email address but according to oauth it have to use the username field
                'username' => $request->get('email'),
                // self-explanatory
                'password' => $request->get('password')
            ],
            'headers' => [
                // client id+client secret (base64)
                'Authorization' => 'Basic YjBkMGQ3YWQtYmI5OS00YWIxLWIyNWUtYWZhMGM3NjU3N2IwOlpvNHk4ZUdJYTNvYXpJRXA='
            ],
            'exceptions' => false
        ]);

        // 200? then the user has logged in successfully
        $valid = $response->getStatusCode() < 400;

        if ($valid) {
            // parsing the access token from the response
            $json = $response->json();
            $token = $json['access_token'];

            $response = $client->get('https://vl.api.np.km.playstation.net/vl/api/v1/mobile/users/me/info', [
                'headers' => [
                    // To call psn api we use the following header to authenticate
                    'X-NP-ACCESS-TOKEN' => $token
                ],
                'exceptions' => false
            ]);

            $valid = $response->getStatusCode() < 400;

            if ($valid) {
                // parsing the user id and username from response
                $json = $response->json();
                $user_id = $json['accountId'];
                $username = $json['onlineId'];

                // TODO save this in the database
            }
        }

        if ($valid)
            return redirect('/account')->with('notice', ['info', 'Your PSN account has been connected.']);
        return view('pages.connect.psn', ['error' => 'Couldn\'t verify PSN account.']);
    }
}