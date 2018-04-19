<?php

namespace Jiko\Social\Twitter\Commands;

use Illuminate\Console\Command;
use Twitter;

class TwitterSend extends Command
{
  protected $signature = 'twitter:send {message} {path?} {--test}';

  protected $description = 'send a message to twitter';

  public function handle()
  {
    if($this->option("test") === true) {
      $this->info("[TEST] " . $this->argument('message') . " " . $this->argument('path'));
      return;
    }

    $me = \Jiko\Auth\User::find(2);
    try {
      $twitter = new Twitter(env('TWITTER_API_KEY'), env('TWITTER_API_SECRET'), $me->twitter->getToken(), $me->twitter->getSecret());
      $twitter->send($this->argument('message'), $this->argument('path'));
    } catch (\TwitterException $e) {
      $this->error($e->getMessage());
    }

    $this->info("Message sent: " . $this->argument('message'));
  }
}