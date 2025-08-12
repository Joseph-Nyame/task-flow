<?php

namespace App\DTOs;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;

class TaskDTO
{
    public string $name;
    public int $priority;
    public int $project_id;

    public static function fromRequest(CreateTaskRequest $request): self
    {
        $dto = new self();
        $dto->name = $request->name;
        $dto->priority = $request->priority;
        $dto->project_id = $request->project_id;
        return $dto;
    }

    public static function fromUpdateRequest(UpdateTaskRequest $request, Task $task): self
    {
        $dto = new self();
        $dto->name = $request->name ?? $task->name;
        $dto->priority = $request->priority ?? $task->priority;
        $dto->project_id = $request->project_id ?? $task->project_id ;
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'priority' => $this->priority,
            'project_id' => $this->project_id,
        ];
    }   
}