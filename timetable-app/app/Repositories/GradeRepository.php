<?php

namespace App\Repositories;

use App\Models\Grade;
use App\Repositories\BaseRepository;

class GradeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'grade',
        'created_by'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Grade::class;
    }
}
