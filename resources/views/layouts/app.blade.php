<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task Flow</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <a href="{{ url('/') }}" class="text-xl font-bold">Task Flow</a>
                <div class="space-x-4">
                    <a href="{{ route('projects.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Projects</a>
                    <a href="{{ route('tasks.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Tasks</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-6">
            @yield('content')
        </main>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg">
        {{ session('success') }}
        <button onclick="this.parentElement.remove()" class="ml-2">×</button>
    </div>
    @endif

    @if(session('error'))
    <div class="fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg">
        {{ session('error') }}
        <button onclick="this.parentElement.remove()" class="ml-2">×</button>
    </div>
    @endif
</body>
</html>