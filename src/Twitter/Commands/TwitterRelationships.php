<?php

namespace Jiko\Social\Twitter\Commands;

use Illuminate\Console\Command;

use Jiko\Models\Twitter\TwitterRelationship;
use Jiko\Auth\User;

class TwitterRelationships extends Command
{
  protected $signature = 'twitter:relationships';

  protected $description = 'get users following me and not following back';

  public function handle()
  {
    $stored = TwitterRelationship::pluck('tid')->all();

    $me = User::find(2);
    $twitter = $me->twitter->getHttpClient();
    $resp = $twitter->request('friends/ids', 'GET');

    $current = $resp->ids;
    $ids = array_chunk($resp->ids, 100);

    // Cleanup
    foreach ($stored as $tid) {
      if (array_search($tid, $current) === false) {
        TwitterRelationship::where('tid', $tid)->delete();
      }
    }

    foreach ($ids as $i => $chunk) {
      $resp = $twitter->request('friendships/lookup', 'GET', ['user_id' => join(",", $chunk)]);
      foreach ($resp as $j => $friend) {
        $key = array_search($friend->id, $stored);
        $updateValues = [];

        if ($key === false) { // might return 0
          $this->info("Creating new relationship.. {$friend->id}");
          $relationship = new TwitterRelationship([
            'tid' => $friend->id
          ]);
          $relationship->save();
        } else {
          $relationship = TwitterRelationship::where('tid', $friend->id)->first();
        }

        if ($friend->name !== $relationship->name || $friend->screen_name !== $relationship->screen_name) {
          $this->info("(!) Name or screen name changed {$friend->name} {$relationship->name} {$friend->screen_name} {$relationship->screen_name}");
          $updateValues['name'] = $friend->name;
          $updateValues['screen_name'] = $friend->screen_name;
        }

        if (in_array("followed_by", $friend->connections)) {
          if (!$relationship->followed_by) {
            $this->info("(!) Followed by value changed");
            $updateValues['followed_by'] = true;
          }
        } else {
          if ($relationship->followed_by) {
            $this->info("(!) Followed by value changed");
            $updatedValues['followed_by'] = false;
          }
        }

        if (!$relationship->following) {
          $this->info("(!) Following value changed");
          $updateValues['following'] = true;
        }

        if (count($updateValues)) {
          $this->info("Something changed.. updating values");
          $relationship->update($updateValues);
        }
        $updated[] = $key;
      }
    }
  }
}