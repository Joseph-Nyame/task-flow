@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tasks</h1>
        <div class="flex space-x-4">
            <div class="relative">
                <select id="project-filter" class="appearance-none bg-white border border-gray-300 rounded-md px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            <a href="{{ route('tasks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>New Task
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div id="tasks-container" class="divide-y divide-gray-200">
            @foreach($tasks as $task)
                <div class="task-item p-4 hover:bg-gray-50 flex items-center justify-between" data-id="{{ $task->id }}" draggable="true">
                    <div class="flex items-center">
                        <span class="priority-badge bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded mr-3">#{{ $task->priority }}</span>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $task->name }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ $task->project->name ?? 'No Project' }} • 
                                Created: {{ $task->created_at->format('M d, Y H:i') }} • 
                                Updated: {{ $task->updated_at->format('M d, Y H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('tasks.edit', $task->id) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Improved project filter using URL API
    document.getElementById('project-filter').addEventListener('change', function() {
        const projectId = this.value;
        const url = new URL(window.location);
        
        if (projectId) {
            url.searchParams.set('project_id', projectId);
        } else {
            url.searchParams.delete('project_id');
        }
        
        window.location.href = url.toString();
    });

    // Enhanced drag and drop functionality
    const container = document.getElementById('tasks-container');
    let draggedItem = null;
    let draggedIndex = null;

    // Helper function to get the element after which we should place the dragged item
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.task-item:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // Drag start event
    container.addEventListener('dragstart', function(e) {
        if (e.target.classList.contains('task-item')) {
            draggedItem = e.target;
            draggedIndex = [...container.children].indexOf(draggedItem);
            e.target.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', e.target.innerHTML);
        }
    });

    // Drag end event
    container.addEventListener('dragend', function(e) {
        if (e.target.classList.contains('task-item')) {
            e.target.classList.remove('dragging');
            e.target.style.opacity = '1';
        }
    });

    // Drag over event
    container.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        
        const afterElement = getDragAfterElement(container, e.clientY);
        if (afterElement == null) {
            container.appendChild(draggedItem);
        } else {
            container.insertBefore(draggedItem, afterElement);
        }
    });

    // Drop event - using the more reliable version with page reload
    container.addEventListener('drop', function(e) {
        e.preventDefault();
        
        // Get the new order of tasks
        const taskItems = Array.from(container.querySelectorAll('.task-item'));
        const newOrder = taskItems.map((item, index) => ({
            id: item.dataset.id,
            priority: index + 1
        }));
        
        // Send the new order to the server
        fetch('{{ route("tasks.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                order: newOrder,
                project_id: document.getElementById('project-filter').value || null
            })
        }).then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        }).then(data => {
            if (data.success) {
                // Reload the page to reflect changes and maintain filter state
                window.location.reload();
            } else {
                console.error('Server responded with error:', data.message);
                window.location.reload(); // Fallback to reload
            }
        }).catch(error => {
            console.error('Error updating task order:', error);
            window.location.reload(); // Fallback to reload
        });
    });

    // Add a slight delay to prevent accidental drags when clicking
    container.addEventListener('mousedown', function(e) {
        if (e.target.closest('.task-item')) {
            const item = e.target.closest('.task-item');
            let isDragging = false;
            let mouseDownTime = Date.now();
            let startX = e.clientX;
            let startY = e.clientY;
            
            function handleMouseMove(e) {
                const moveX = Math.abs(e.clientX - startX);
                const moveY = Math.abs(e.clientY - startY);
                
                if (moveX > 5 || moveY > 5) {
                    isDragging = true;
                }
            }
            
            function handleMouseUp(e) {
                const clickDuration = Date.now() - mouseDownTime;
                const moveX = Math.abs(e.clientX - startX);
                const moveY = Math.abs(e.clientY - startY);
                
                if (clickDuration > 200 || moveX > 5 || moveY > 5) {
                    isDragging = true;
                }
                
                document.removeEventListener('mousemove', handleMouseMove);
                document.removeEventListener('mouseup', handleMouseUp);
                
                if (!isDragging && e.target.closest('a, button, input, select, textarea')) {
                    e.preventDefault();
                    e.target.click();
                }
            }
            
            document.addEventListener('mousemove', handleMouseMove);
            document.addEventListener('mouseup', handleMouseUp);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    .task-item {
        transition: background-color 0.2s ease, transform 0.2s ease;
        cursor: grab;
    }
    
    .task-item.dragging {
        opacity: 0.5;
        background-color: #f3f4f6;
        transform: scale(1.02);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .priority-badge {
        min-width: 2.5rem;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    #tasks-container {
        min-height: 100px;
    }
</style>
@endpush