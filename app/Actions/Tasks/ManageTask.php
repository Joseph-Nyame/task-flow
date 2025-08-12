<?php

namespace App\Actions\Tasks;

use App\DTOs\TaskDTO;
use App\Models\Task;
use Exception;

class ManageTask
{
    public function update(Task $task, TaskDTO $dto): bool
    {
        return $task->update($dto->toArray());
        
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}