<?php namespace Jiko\Social\Instagram;
class InstagramUser
{
  protected $client;

  public function __construct(\Larabros\Elogram\Client $client)
  {
    $this->client = $client;
  }

  public function media($params = [])
  {
    $limit = isset($params['count']) ? $params['count'] : 8;
    $id = isset($params['id']) ? $params['id'] : 'self';
    return $this->client->users()->getMedia($id, $limit);
  }
}