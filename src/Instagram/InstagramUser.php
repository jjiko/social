<?php namespace Jiko\Social\Instagram;
class InstagramUser
{
  protected $_instagram;

  public function __construct(\Larabros\Elogram\Client $client)
  {
    $this->_instagram = $client;
  }

  public function media($params = [])
  {
    $limit = isset($params['count']) ? $params['count'] : 8;
    $id = isset($params['id']) ? $params['id'] : 'self';
    return $this->_instagram->users()->getMedia($id, $limit);
  }
}