<?php namespace Jiko\Social\Instagram;
class InstagramUser {
  protected $_instagram;

  public function __construct($client) {
    $this->_instagram = $client;
  }

  public function media($limit=8, $id='self') {
    return $this->_instagram->users()->getMedia($id, $limit);
  }
}