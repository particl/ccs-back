<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\VoteResource
 *
 * @property int $id
 * @property int $block_height_start
 * @property int $block_height_end
 * @property int $finished
 * @property int $blocks_yes
 * @property int $blocks_no
 * @property int $blocks_abstain
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vote whereId($value)

 * @method static \Illuminate\Database\Eloquent\Builder|\App\Vote whereUpdatedAt($value)
 */
class Vote extends Model
{
    protected $table = 'votes';
    protected $guarded = ['id'];

    protected $dates = ['created_at', 'updated_at'];

}
