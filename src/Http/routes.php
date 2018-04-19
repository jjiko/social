<?php
Route::get('instagram/sandbox', ['as' => 'instagram.sandbox', 'uses' => function () {
  $user = \Jiko\Auth\User::find(2);
  $iguser = $user->instagram->getClient();
  dd($iguser->media()->get('1573618186652368166_30589941'));
}]);
Route::get('instagram/follows', ['as' => 'instagram.user.follows', 'uses' => function () {
  $user = \Jiko\Auth\User::find(2);
  $iguser = $user->instagram->getClient();
  dd($iguser->users()->follows());

}]);

Route::get('instagram/followed-by', ['as' => 'instagram.user.followers', 'uses' => function () {
  $user = \Jiko\Auth\User::find(2);
  dd($user->instagram->followedBy());
}]);

Route::get('instagram/relationship/{f_user_id}', function ($f_user_id) {
  $instagram = new Jiko\Instagram\InstagramRepository();
  $data = $instagram->relationship($f_user_id);
  return view('instagram.relationship.show', ['data' => $data]);
})
  ->where('f_user_id', '[0-9]+');

Route::get('instagram/relationship/{f_user_status}', function ($f_user_status) {
  switch ($f_user_status) {
    case "not-following-back":
      $instagram = new Jiko\Instagram\InstagramRepository();
      $data = $instagram->notFollowingBack();
      return view('instagram.relationship', ['data' => $data]);
    default:
      return "Status view not found.";
  }
});

Route::get('instagram/relationships', function () {
  $instagram = new Jiko\Instagram\InstagramRepository();
  return $instagram->setRelationships();
});

Route::get('instagram/following-back', ['as' => 'instagram.following-back', 'uses', function () {
  $instagram = new Jiko\Instagram\InstagramRepository();
  // add data from follows
  // add data from followers
  // compare?
}]);