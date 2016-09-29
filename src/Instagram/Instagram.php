<?php namespace Jiko\Social\Instagram;
use MetzWeb\Instagram\Instagram as phpInstagramApi;
class Instagram extends phpInstagramApi {
  public function user() {
    return (new InstagramUser($this));
  }
}