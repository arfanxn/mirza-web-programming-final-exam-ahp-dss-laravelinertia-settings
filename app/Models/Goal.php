<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    use HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
    ];

    public function criteria()
    {
        return $this->hasMany(Criterion::class, 'goal_id', 'id');
    }

    public function alternatives()
    {
        return $this->hasMany(Alternative::class, 'goal_id', 'id');
    }

    /* // ! Deprecated
    public function alternatives()
    {
        return $this->hasManyDeep(
            Alternative::class,
            [Criterion::class, PerformanceScore::class],
            ['goal_id', 'criterion_id', 'id'],
            ['id', 'id', 'alternative_id'],
        )
            ->groupBy('alternatives.id'); // Group by the related table's primary key to remove duplicates
    }
    */

    public function performanceScores()
    {
        return $this->hasManyDeep(
            PerformanceScore::class,
            [Criterion::class],
        );
    }

    public function pairwiseComparisons()
    {
        return $this->hasManyDeep(
            PairwiseComparison::class,
            [Criterion::class],
            ['goal_id', 'primary_criterion_id'],
            ['id', 'id'],
        );
    }
}
