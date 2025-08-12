@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-8 text-center">
    <h1 class="text-4xl font-bold text-blue-600 mb-6">Welcome to Task Flow</h1>
    <p class="text-gray-700 mb-8">Efficiently manage your tasks and projects with our intuitive drag-and-drop interface.</p>
    
    <div class="flex justify-center space-x-6">
        <a href="{{ route('projects.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
            View Projects
        </a>
        <a href="{{ route('tasks.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
            View Tasks
        </a>
    </div>
</div>
@endsection