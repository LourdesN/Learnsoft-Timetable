<?php

namespace App\Repositories;

use App\Models\Timetable;
use App\Repositories\BaseRepository;

class TimetableRepository extends BaseRepository
{
    protected $fieldSearchable = [
        
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Timetable::class;
    }
}
