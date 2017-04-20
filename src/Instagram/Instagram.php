<?php namespace Jiko\Social\Instagram;

use Larabros\Elogram\Client as InstagramClient;
use League\OAuth2\Client\Token\AccessToken;

class Instagram
{
  public function __construct($token = null)
  {
    $this->token = $token;
    $this->client = new InstagramClient(
      getenv('INSTAGRAM_CLIENT_ID'),
      getenv('INSTAGRAM_CLIENT_SECRET'),
      getenv('INSTAGRAM_REDIRECT_URI')
    );
    $this->client->setAccessToken(new AccessToken(['access_token' => getenv('INSTAGRAM_ACCESS_TOKEN')]));
  }

  public function user()
  {
    return (new InstagramUser($this->client));
  }
}