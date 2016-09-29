<?php namespace Jiko\Social\Instagram;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relationship extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];
  protected $guarded = [];
  protected $table = 'instagram_relationships';

  public $timestamps = true;
}