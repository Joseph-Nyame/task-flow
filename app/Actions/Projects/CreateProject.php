<?php

namespace App\Actions\Projects;

use App\DTOs\ProjectDTO;
use App\Models\Project;

class CreateProject
{
    public function execute(ProjectDTO $dto): Project
    {
        return Project::create($dto->toArray());
    }
}