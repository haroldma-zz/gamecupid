<?php
/**
 * Created by PhpStorm.
 * User: harry
 * Date: 4/22/15
 * Time: 2:03 PM
 */

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Http\Requests\ConnectFormRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class PlatformValidatorController extends Controller
{
    public function validatePsn(ConnectFormRequest $request)
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
                // parsing the online id from response
                $json = $response->json();

                $profile = new Profile;

                $profile->user_id         = Auth::user()->id;
                $profile->online_id       = "idontknow";
                $profile->online_username = $json['onlineId'];
                $profile->platform_id     = 2;

                $profile->save();
            }
        }

        if ($valid)
            return redirect('/account')->with('notice', ['success', 'Your PSN account has been connected.']);
        return view('pages.connect.psn', ['error' => 'Couldn\'t verify PSN account.']);
    }

    public function validateXbl(ConnectFormRequest $request)
    {
        // xbox is tricky, can't use oauth because the client that has
        // access to xbox live scopes redirect uri can't be tamper with
        // so we'll simulate browser logging manually

        // let's start by creating a Guzzle Client to make http request
        $client = new Client();

        $data = ['client_id'=>'0000000048093EE3',
            'redirect_uri'=>'https://login.live.com/oauth20_desktop.srf',
            'response_type'=>'token',
            'scope'=>'service::user.auth.xboxlive.com::MBI_SSL',
            'locale'=>'en'];

        // Make a get request to the login form,
        // we need to extract some data from the form to post the credentials later
        $response = $client->get('https://login.live.com/oauth20_authorize.srf?' . http_build_query($data), [
            'exceptions' => false,
            'cookies' => true
        ]);

        $url_re = '%urlPost:\\\'([A-Za-z0-9:\?_\-\.&/=\%]+)%';
        $ppft_re = '%sFTTag:\\\'.*value="(.*)"/>%';
        $body = $response->getBody();

        // To post to the login endpoint we need:
        // 1. The url to post to
        // 2. The PPFT value
        // 3. and the Cookies
        // otherwise it will fail
        preg_match($url_re, $body, $matches);
        $post_url = $matches[1];
        preg_match($ppft_re, $body, $matches);
        $ppft = $matches[1];

        // Now we simulate a post to the login form
        $response = $client->post($post_url,
            [
                'body' =>
                    [
                        'PPFT'         => $ppft,
                        'login'        => $request->get('email'),
                        'passwd'       => $request->get('password'),
                        'SI'           => 'Sign in',
                        'type'         => '11',
                        'PPSX'         => 'Passpor',
                        'NewUser'      => '1',
                        'LoginOptions' => '1',
                        'i3'           => '36728',
                        'm1'           => '1080',
                        'm2'           => '1920',
                        'm3'           => '0',
                        'i12'          => '1',
                        'i17'          => '0',
                        'i18'          => '__Login_Host|1,'
                    ],
                'exceptions' => false,
                'cookies' => true,
                'allow_redirects' => false
            ]);

        $valid = $response->hasHeader('Location');
        if ($valid)
        {
            // parse the redirect url and fragment to get access token
            $location = $response->getHeader('Location');
            $url = parse_url($location);
            $fragment = $url["fragment"];
            parse_str($fragment, $fragment_query);
            $access_token = $fragment_query["access_token"];

            // now we need to get the authentication token
            $users_token = $this->authenticateXbox($client, $access_token);

            $valid = $users_token != null;
            if ($valid)
            {
                $gamertag = $this->authorizeXbox($client, $users_token);
                
                $valid = $gamertag != null;
                if ($valid)
                {
                    $profile = new Profile;

                    $profile->user_id         = Auth::user()->id;
                    $profile->online_id       = "idontknow";
                    $profile->online_username = $gamertag;
                    $profile->platform_id     = 1;

                    $profile->save();
                }
            }
        }

        if ($valid)
            return redirect('/account')->with('notice', ['success', 'Your Xbox Live account has been connected.']);
        return view('pages.connect.xbl', ['error' => 'Couldn\'t verify Xbox Live account.']);
    }

    public function validateSteam(ConnectFormRequest $request)
    {
        // The Steam validation method
    }



    function authenticateXbox(Client $client, $token)
    {
        // Make a post request to the psn oauth api, if successful then we're golden!
        $response = $client->post('https://user.auth.xboxlive.com/user/authenticate', [
            'json' => [
                    'RelyingParty' => 'http://auth.xboxlive.com',
                    'TokenType' => 'JWT',
                    'Properties' => [
                    'AuthMethod' => 'RPS',
                    'SiteName' => 'user.auth.xboxlive.com',
                    'RpsTicket' => $token,
                ]
            ],
            'exceptions' => false
        ]);

        if ($response->getStatusCode() < 400)
        {
            $json = $response->json();
            return $json['Token'];
        }
        else
            return null;
    }

    function authorizeXbox(Client $client, $token)
    {
        // Make a post request to the psn oauth api, if successful then we're golden!
        $response = $client->post('https://xsts.auth.xboxlive.com/xsts/authorize', [
            'json' => [
                    'RelyingParty' => 'http://xboxlive.com',
                    'TokenType' => 'JWT',
                    'Properties' => [
                    'UserTokens' => [ $token ],
                    'SandboxId' => 'RETAIL',
                ]
            ],
            'exceptions' => false
        ]);

        if ($response->getStatusCode() < 400)
        {
            $json = $response->json();
            return $json['DisplayClaims']['xui'][0]['gtg'];
        }
        else
            return null;
    }
}