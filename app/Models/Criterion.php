<?php

namespace App\Models;

use App\Enums\Criterion\ImpactType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'criteria';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'goal_id',
        'name',
        'impact_type',
        'index',
        'weight',
        'weight_percentage'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['impact_type_description'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'impact_type' => ImpactType::class,
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'impact_type' => ImpactType::class,
        ];
    }

    public function getImpactTypeDescriptionAttribute(): string
    {
        return ImpactType::getDescription($this->attributes['impact_type']);
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function performanceScores()
    {
        return $this->hasMany(PerformanceScore::class, 'criterion_id', 'id');
    }
}
