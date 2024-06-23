<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeLearningArea extends Model
{
    protected $table = 'grade_learning_areas';

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function learningArea()
    {
        return $this->belongsTo(LearningArea::class);
    }
}
