<?php

namespace Juksy\Shredder;

use Illuminate\Support\Str;
use Facebook\Authentication\AccessToken;
use Juksy\Shredder\Entities\Plate;
use Juksy\Shredder\Exceptions\NoAccessTokenException;
use Juksy\Shredder\Exceptions\NoPageAccessTokenException;
use Juksy\Shredder\Exceptions\MethodNotFoundException;

class ShredderHandler
{
    /**
     * @var \Illuminate\Console\Application
     */
    private $app;

    /**
     * @var array
     */
    private $config;

    public $fb;

    function __construct($app, $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->fb = new \Facebook\Facebook($config);
    }

    /**
     * get Login Url
     *
     * @param string $backUrl
     * @return string
     */
    public function login($backUrl)
    {
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = $this->config['permissions'];
        return $helper->getLoginUrl($backUrl, $permissions);
    }

    /**
     * return user token throw NoAccessTokenException
     *
     * @param $page_id
     * @return string
     */
    public function getAccessToken($page_id)
    {
        $helper = $this->fb->getRedirectLoginHelper();
        $accessToken = $helper->getAccessToken(); // personal account Logged in!

        if (is_null($accessToken)) {
            throw new NoAccessTokenException();
        }

        return $this->getPageAccessToken($page_id, (string) $accessToken);
    }

    /**
     * return redirect
     *
     * @param string $page_id
     * @param string $access_token
     * @return string
     */
    private function getPageAccessToken($page_id, $access_token)
    {
        // 取得現在用戶對該頁面的發文權限，存到 page_access_token
        $ret = $this->fb->get('/' . $page_id . '?fields=access_token', $access_token);
        $ret_decode = $ret->getDecodedBody();
        $page_access_token = (isset($ret_decode['access_token'])) ? $ret_decode['access_token'] : '';

        if(empty($page_access_token)) {
            return new NoPageAccessTokenException();
        }

        return (string) $page_access_token;
    }

    /**
     * Handle dynamic method calls into the request.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        /*
         * Make calls to Facebook Graph API with CRUD.
         *
         * @param string $access_token
         * @param Plate $object_item
         */
        if (Str::startsWith($method, ['get', 'post', 'delete'])) {
            $access_token = $parameters[0];
            $plate = $parameters[1];

            // Second parameter should be method for Facebook API.
            $values = explode('_', Str::snake($method), 2);

            // Fetching facebook API.
            $url = rtrim($plate->getEndpoint(), "/") . '/' . $values[1];
            $ret = $this->fb->{$values[0]}($url, $plate->getMessages(), $access_token);

            return $ret->getDecodedBody();
        }

        throw new MethodNotFoundException(get_called_class(), $method);
    }

}
