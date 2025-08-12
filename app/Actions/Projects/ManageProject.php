<?php

namespace App\Actions\Projects;

use App\DTOs\ProjectDTO;
use App\Models\Project;
use Exception;

class ManageProject
{
    public function update(Project $project, ProjectDTO $dto): bool
    {
        return $project->update($dto->toArray());
       
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }
}