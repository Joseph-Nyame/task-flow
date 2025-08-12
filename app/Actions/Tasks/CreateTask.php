<?php

namespace App\Actions\Tasks;

use App\DTOs\TaskDTO;
use App\Models\Task;

class CreateTask
{
    public function execute(TaskDTO $dto): Task
    {
        return Task::create([
            'name' => $dto->name,
            'priority' => $dto->priority,
            'project_id' => $dto->project_id,
        ]);
    }
}