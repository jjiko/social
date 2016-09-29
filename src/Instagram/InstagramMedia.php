<?php namespace Jiko\Social\Instagram;
class InstagramMedia
{
  protected $_instagram;

  public function __construct($context)
  {
    $this->_instagram = $context;

    $media = $instagram->getUserMedia();
    foreach ($user as $k => $v) {
      $this->$k = $v;
    }
  }

  public function get($id, $limit)
  {

  }

  public function all()
  {

  }

  public function next()
  {

  }
}