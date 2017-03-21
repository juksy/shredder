<?php
namespace Juksy\Shredder;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\RedirectResponse;
use Juksy\Shredder\Exception\NoAccessTokenException;
use Juksy\Shredder\Entities\SchedulePost;

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
    private   $fb;

    function __construct()
    {
        $this->fb = new \Facebook\Facebook([
            'app_id'                => Config::get('shredder.app_id'),
            'app_secret'            => Config::get('shredder.app_secret'),
            'default_graph_version' => Config::get('shredder.graph_api_version'),
        ]);
    }

    /**
     * get Login Url
     * @return [type] [description]
     */
    public function login($backUrl)
    {
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = ['email', 'user_likes', 'manage_pages', 'publish_pages']; // optional
        $loginUrl = $helper->getLoginUrl($backUrl, $permissions);

        return new RedirectResponse($loginUrl);
    }

    /**
     * return user token throw NoAccessTokenException
     * @return [type] [description]
     */
    public function getAccessToken()
    {
        $helper = $this->fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken(); // personal account Logged in!
            $accessToken = $this->getPageAccessToken($accessToken); //page access token

            return (string) $accessToken;
        } catch(\Exception $e) {
            return new NoAccessTokenException();
        }

    }

    /**
     * return redirect
     * @param  str callback_url
     * @return string
     */
    private function getPageAccessToken($accessToken)
    {
        // 取得現在用戶對該頁面的發文權限，存到 page_access_token
        $ret = $this->fb->get('/' . $this->page_id . '?fields=access_token', $accessToken);
        $ret_decode = $ret->getDecodedBody();
        $page_access_token = (isset($ret_decode['access_token'])) ? $ret_decode['access_token'] : '';

        if(empty($page_access_token)) {
            return new NoPageAccessTokenException();
        }

        return (string)$page_access_token;
    }

    /**
     * throw Exception
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
     * @param  [type] $pageId [description]
     * @return [type]         [description]
     */
    public function getPosts($access_token, Plate $plate, $fields = [])
    {
        $endpoint = rtrim($plate->getEndpoint(), "/") . '/promotable_posts';

        if (count($fields)) {
            $endpoint += '?fields=' . implode(',', $fields);
        }

        $ret = $this->fb->get($endpoint, $access_token);

        return $ret->getDecodedBody();
    }
}
