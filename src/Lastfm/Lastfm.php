<?php namespace Jiko\Social\Lastfm;

use Jiko\Api\CacheableApiTrait;

class Lastfm
{
  use CacheableApiTrait;

  protected $endpoint = 'http://ws.audioscrobbler.com/2.0/';

  public function recent_tracks($page=1)
  {
    if($cache = self::readCache('lastfm.recenttracks.' . $page)) {
      if($data = self::cacheIsFresh($cache)) {
        return $data;
      }
    }

    $querystring = http_build_query([
      'method' => 'user.getrecenttracks',
      'user' => 'joejiko',
      'api_key' => getenv('LASTFM_API_KEY'),
      'format' => 'json'
    ]);

    if($data = getJson("{$this->endpoint}?$querystring")) {
      $cache->update(['data' => json_encode($data)]);

      return $data;
    }

    return false;
  }
}