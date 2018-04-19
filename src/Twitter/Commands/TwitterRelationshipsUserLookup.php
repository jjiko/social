<?php

namespace Jiko\Social\Twitter\Commands;

use Illuminate\Console\Command;

use Jiko\Models\Twitter\TwitterRelationship;
use Jiko\Auth\User;
use Carbon\Carbon;

class TwitterRelationshipsUserLookup extends Command
{
  protected $signature = 'twitter:relationships-user-lookup';

  protected $description = 'get users following me and not following back';

  public function handle()
  {
    $stored = TwitterRelationship::pluck('tid')->all();
    $ids = array_chunk($stored, 100);

    $me = User::find(2);
    $twitter = $me->twitter->getHttpClient();

    foreach ($ids as $i => $chunk) {
      $resp = $twitter->request('users/lookup', 'GET', ['user_id' => join(",", $chunk)]);
      foreach ($resp as $user) {
        if (!property_exists($user, 'status')) {
          $user->status = (object)[
            'created_at' => null,
            'text' => null
          ];
        }
        $extra = [
          'account_created_at' => (new Carbon($user->created_at))->toDateTimeString(),
          'description' => $user->description,
          'protected' => $user->protected,
          'statuses_count' => $user->statuses_count,
          'favorites_count' => $user->favourites_count,
          'lang' => $user->lang
        ];
        $updateValues = [
          'location' => $user->location,
          'followers_count' => $user->followers_count,
          'following_count' => $user->friends_count,
          'last_status_created_at' => (new Carbon($user->status->created_at))->toDateTimeString(),
          'last_status_text' => $user->status->text,
          'profile_image_url' => $user->profile_image_url_https,
          'extra' => json_encode($extra)
        ];
        $relationship = TwitterRelationship::where('tid', $user->id)->update($updateValues);
      }
    }
  }
}