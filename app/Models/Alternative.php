<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternative extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'goal_id',
        'name',
        'index',
    ];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function performanceScores()
    {
        return $this->hasMany(PerformanceScore::class, 'alternative_id', 'id');
    }
}
