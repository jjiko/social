<?php namespace Jiko\Social\Facebook;

use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\Entities\AccessToken;

use Jiko\Api\CacheableApiTrait;
use Jiko\Users\User;

class FacebookRepository {

  use CacheableApiTrait;

  // @todo cacheabletrait
  protected $session;
  protected $token;

  public function __construct()
  {
    // Start session with Facebook API
    FacebookSession::setDefaultApplication(getenv('FACEBOOK_APP_ID'), getenv('FACEBOOK_APP_SECRET'));
  }

  public function refreshToken()
  {
    // Select access token from database
    $me = User::find(1);
    $this->token = new AccessToken($me->fb_access_token);

    try {
      $code = AccessToken::getCodeFromAccessToken($this->token);
    } catch (FacebookSDKException $e) {
      if (Config::get('app.debug')) {
        echo 'Error getting code from access token: ' . $e->getMessage();
      }
    }

    // Refresh access token
    if(!empty($code)) {
      try {
        $this->token = AccessToken::getAccessTokenFromCode($code);
        $me->update(['fb_access_token' => $this->token]);
      } catch (FacebookSDKException $e) {
        if (Config::get('app.debug')) {
          echo 'Error getting a new long-lived access token: ' . $e->getMessage();
        }
      }
    }

    if(!empty($this->token)) {
      $this->session = new FacebookSession($this->token);
    }
  }

  public function statuses($limit=4)
  {
    if($cache = self::readCache('facebook.statuses.'.$limit)) {
      if($data = self::cacheIsFresh($cache)) {
        return $data;
      }
    }

    $this->refreshToken();
    $data = (new FacebookRequest($this->session, 'GET', '/me/statuses', ['limit' => $limit]))
      ->execute()
      ->getGraphObject()
      ->asArray();

    $cache->update(['data' => json_encode($data)]);

    return (object) $data;
  }
}