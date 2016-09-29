<?php namespace Jiko\Social\Instagram;

use Jiko\Api\CacheableApiTrait;

class InstagramRepository
{
  use CacheableApiTrait;

  protected $instagram;

  public function __construct()
  {
    $this->instagram = new Instagram([
      'apiKey' => env("INSTAGRAM_CLIENT_ID"),
      'apiSecret' => env("INSTAGRAM_CLIENT_SECRET"),
      'apiCallback' => env("INSTAGRAM_REDIRECT_URI")
    ]);
    $this->instagram->setAccessToken('30589941.ffa73be.fd1c0a339a594d0aa3dab44be0863c3d');
  }

  public function user()
  {
    return $this->instagram->user();
  }

  public function notFollowingBack()
  {
    return Relationship::where('user_id', $this->user()->data->id)
      ->whereNotNull('follows')
      ->whereNull('following')
      ->whereNull('whitelist')// never unfollow
      ->whereNull('reserve')// unfollow later maybe
      ->get();
  }

  public function setRelationships()
  {
    $start_time = date("c");
    $follows = $this->follows(false);
    $followers = $this->followers(false);

    $user_id = $this->user()->data->id;

    foreach ($follows as $user) {
      $row = Relationship::firstOrCreate([
        'user_id' => $user_id,
        'f_user_id' => $user->id
      ]);
      $row->update([
        'username' => $user->username,
        'profile_picture' => $user->profile_picture,
        'full_name' => $user->full_name,
        'follows' => date("c")
      ]);
    }

    // mark users you unfollowed
    $collection = Relationship::where('updated_at', '<', $start_time)->get();
    $collection->each(function ($item, $key) {
      $item->update(['unfollowed' => date("c")]);
    });

    foreach ($followers as $user) {
      $row = Relationship::firstOrCreate([
        'user_id' => $user_id,
        'f_user_id' => $user->id,
      ]);
      $row->update(['following' => date("c")]);
    }

    // mark users who unfollowed you
    $collection = Relationship::where('updated_at', '<', $start_time)->get();
    $collection->each(function ($item, $key) {
      $item->update(['unfollowing' => date("c")]);
    });

    return ['status' => 'success?'];
  }

  public function followers($useCache = true)
  {
    if ($cache = self::readCache(sprintf("instagram.%s.following", $this->user()->data->id))) {
      if ($useCache) {
        if ($data = self::cacheIsFresh($cache, (3600 * 24 * 7))) { // expires in 1 week
          return $data;
        }
      }
    }

    // Build data from Instagram
    $data = [];
    $result = $this->instagram->getUserFollower();
    do {
      $data = array_merge($data, $result->data);
    } while ($result = $this->next($result));
    $cache->update(['data' => json_encode($data)]);

    return $data;
  }

  public function follows($useCache = true)
  {
    if ($cache = self::readCache(sprintf("instagram.%s.follows", $this->user()->data->id))) {
      if ($useCache) {
        if ($data = self::cacheIsFresh($cache, (3600 * 24 * 7))) { // expires in 1 week
          return $data;
        }
      }
    }

    // Build data from Instagram
    $data = [];
    $result = $this->instagram->getUserFollows();
    do {
      $data = array_merge($data, $result->data);
    } while ($result = $this->next($result));
    $cache->update(['data' => json_encode($data)]);

    return $data;
  }

  public function next($object)
  {
    return $this->instagram->pagination($object);
  }

  public function media($params = [])
  {
    if (empty($params)) $params['count'] = 8;

    // Don't cache with max_id (paginated)
    if (empty($params['max_id'])) {
      if ($cache = self::readCache('instagram.media.' . $params['count'])) {
        if ($data = self::cacheIsFresh($cache)) {
          return $data;
        }
      }
    }

    try {
      $media = $this->user()->media($params);
    } catch (Instagram\Core\ApiException $e) {
      return null;
    }

    $data['next'] = $media->next();

    $data['media'] = [];

    foreach ($media->getData() as $media) {
      $data['media'][] = $media->getData();
    }

    if (empty($params['max_id'])) {
      $cache->update(['data' => json_encode($data)]);
    }

    return (object)$data;
  }

}