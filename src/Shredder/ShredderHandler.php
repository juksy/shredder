<?php

namespace Juksy\Shredder;

use Facebook\Authentication\AccessToken;
use Juksy\Shredder\Entities\Plate;
use Juksy\Shredder\Exceptions\NoAccessTokenException;
use Juksy\Shredder\Exceptions\NoPageAccessTokenException;

/**
 *
 * interface {
 *     public function getLoginUrl(); // return login url
 *     public function getAccessToken(); //return user token throw NoAccessTokenException
 *     public function feedPost(); //throw NoAccessTokenException
 *     public function getPosts(); // throw NoAccessTokenException
 * }
 */
class ShredderHandler
{
    /**
     * @var \Illuminate\Console\Application
     */
    private $app;

    public $fb;

    function __construct($app, $config)
    {
        $this->app = $app;
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
        $permissions = ['email', 'user_likes', 'manage_pages', 'publish_pages']; // optional
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
     * throw Exception
     *
     * @param AccessToken|string $access_token
     * @param Plate $plate
     * @return array
     */
    public function feedPost($access_token, Plate $plate)
    {
        $endpoint = rtrim($plate->getEndpoint(), "/") . '/feed';

        $params = $plate->getMessages();

        $ret = $this->fb->post($endpoint, $params, $access_token);

        return $ret->getDecodedBody();
    }

    /**
     * throw NoAccessTokenException
     *
     * @param AccessToken|string $access_token
     * @param Plate $plate
     * @param array $fields
     * @return array
     */
    public function getPosts($access_token, Plate $plate, $fields = [])
    {
        $endpoint = rtrim($plate->getEndpoint(), "/") . '/promotable_posts';

        if (count($fields)) {
            $endpoint .= '?fields=' . implode(',', $fields);
        }

        $ret = $this->fb->get($endpoint, $access_token);

        return $ret->getDecodedBody();
    }
}
