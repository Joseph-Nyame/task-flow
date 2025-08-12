<?php

namespace App\DTOs;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;

class ProjectDTO
{
    public string $name;
    public ?string $description;

    public static function fromRequest(CreateProjectRequest $request): self
    {
        $dto = new self();
        $dto->name = $request->name;
        $dto->description = $request->description;
        return $dto;
    }

    public static function fromUpdateRequest(UpdateProjectRequest $request, Project $project): self
    {
        $dto = new self();
        $dto->name = $request->name ?? $project->name;
        $dto->description = $request->description ?? $project->description ;
        return $dto;
    }
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }   
}