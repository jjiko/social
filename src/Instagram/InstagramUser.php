<?php namespace Jiko\Social\Instagram;
class InstagramUser {
  protected $_instagram;

  public function __construct($instagram) {
    $this->_instagram = $instagram;

    $user = $instagram->getUser();
    foreach($user as $k => $v) {
      $this->$k = $v;
    }
  }

  public function media($limit=8, $id='self') {
    return $this->_instagram->getUserMedia($id, $limit);
  }
}