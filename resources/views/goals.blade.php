@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="goalsPage()">
    
    <!-- Flash Messages -->
    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Success!</p>
                    <p class="text-sm">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">Error!</p>
                    <ul class="text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Goals & Roadmaps</h1>
            <p class="text-gray-600 dark:text-gray-400">Track your learning journey with AI-powered insights</p>
        </div>
        <div class="flex space-x-3">
            <button @click="showAIAssistant = true" 
                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                AI Assistant
            </button>
            <button @click="showAddGoalModal = true" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Goal
            </button>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="mb-6 flex items-center space-x-4">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">View Mode:</span>
        <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
            <button @click="viewMode = 'list'" 
                    :class="viewMode === 'list' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors">
                📋 List View
            </button>
            <button @click="viewMode = 'flowchart'" 
                    :class="viewMode === 'flowchart' ? 'bg-white dark:bg-gray-600 shadow-sm' : ''"
                    class="px-3 py-1 text-sm font-medium rounded-md transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Flowchart View
            </button>
        </div>
    </div>

    <!-- Goals List -->
    <div x-show="viewMode === 'list'" class="grid gap-6">
        @forelse($goals as $goal)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $goal->title }}</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $goal->status_badge_color }}">
                                {{ $goal->status_label }}
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-3">{{ $goal->description }}</p>
                        
                        <!-- Goal Progress Overview -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                                <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">Target Date</div>
                                <div class="text-lg font-semibold text-blue-800 dark:text-blue-300">{{ $goal->target_date->format('M d, Y') }}</div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                                <div class="text-sm text-green-600 dark:text-green-400 font-medium">Progress</div>
                                <div class="text-lg font-semibold text-green-800 dark:text-green-300">{{ $goal->progress_percentage }}%</div>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3">
                                <div class="text-sm text-purple-600 dark:text-purple-400 font-medium">Milestones</div>
                                <div class="text-lg font-semibold text-purple-800 dark:text-purple-300">{{ $goal->milestones->count() }}</div>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3">
                                <div class="text-sm text-orange-600 dark:text-orange-400 font-medium">Total Tasks</div>
                                <div class="text-lg font-semibold text-orange-800 dark:text-orange-300">{{ $goal->milestones->sum(function($m) { return $m->tasks->count(); }) }}</div>
                            </div>
                        </div>

                        <!-- Overall Progress Bar -->
                        @if($goal->progress_percentage > 0)
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Progress</span>
                                <span class="text-sm text-blue-600 dark:text-blue-400 font-semibold">{{ $goal->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-500 ease-out" style="width: {{ $goal->progress_percentage }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Delete Goal Button -->
                        <div class="relative" x-data="{ showDelete: false }">
                            <button @click="showDelete = true" 
                                    class="p-2 text-red-400 hover:text-red-600 dark:hover:text-red-300 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            <!-- Delete Confirmation Modal -->
                            <div x-show="showDelete" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 @click.outside="showDelete = false"
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-sm mx-4 shadow-2xl">
                                    <div class="flex items-center mb-4">
                                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delete Goal</h3>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                        Are you sure you want to delete "{{ $goal->title }}"? This will also delete all milestones and tasks. This action cannot be undone.
                                    </p>
                                    <div class="flex space-x-3">
                                        <button @click="showDelete = false" 
                                                class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                            Cancel
                                        </button>
                                        <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Update Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </button>
                            <div x-show="open" 
                                 @click.outside="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-20">
                                <form action="{{ route('goals.updateStatus', $goal) }}" method="POST" class="py-1">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="status" value="planned" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        📋 Mark as Planned
                                    </button>
                                    <button type="submit" name="status" value="in_progress" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Mark as In Progress
                                    </button>
                                    <button type="submit" name="status" value="done" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Mark as Done
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Toggle Details -->
                        <button @click="toggleGoalDetails({{ $goal->id }})" 
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md">
                            <svg x-show="!openGoals.includes({{ $goal->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            <svg x-show="openGoals.includes({{ $goal->id }})" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Goal Details Panel -->
            <div x-show="openGoals.includes({{ $goal->id }})" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 max-h-0"
                 x-transition:enter-end="opacity-100 max-h-screen"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 max-h-screen"
                 x-transition:leave-end="opacity-0 max-h-0"
                 class="px-6 py-4">
                
                <!-- Add Milestone Form -->
                <div class="mb-6 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-blue-800 dark:text-blue-200">Add New Milestone</h4>
                    </div>
                    <form action="{{ route('milestones.store', $goal) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-blue-700 dark:text-blue-300 mb-1">Milestone Title *</label>
                                <input type="text" 
                                       name="title" 
                                       placeholder="e.g., Master Portfolio Building Basics"
                                       class="block w-full px-4 py-3 border border-blue-300 dark:border-blue-600 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-blue-700 dark:text-blue-300 mb-1">Target Date</label>
                                <input type="date" 
                                       name="target_date"
                                       class="block w-full px-4 py-3 border border-blue-300 dark:border-blue-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
                                       required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-blue-700 dark:text-blue-300 mb-1">Description</label>
                            <textarea name="description" 
                                      placeholder="Describe what you'll learn and achieve in this milestone..."
                                      rows="3"
                                      class="block w-full px-4 py-3 border border-blue-300 dark:border-blue-600 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"></textarea>
                        </div>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Milestone
                        </button>
                    </form>
                </div>

                <!-- Milestones List -->
                <div class="space-y-6">
                    @forelse($goal->milestones as $milestone)
                    <div class="border border-gray-200 dark:border-gray-600 rounded-xl p-6 bg-gray-50 dark:bg-gray-700">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        {{ $loop->iteration }}
                                    </div>
                                    <h5 class="text-lg font-bold text-gray-900 dark:text-white">{{ $milestone->title }}</h5>
                                    <span class="text-sm text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded">
                                        Due: {{ $milestone->target_date ? $milestone->target_date->format('M d, Y') : 'No date set' }}
                                    </span>
                                </div>
                                @if($milestone->description)
                                    <div class="ml-11 bg-white dark:bg-gray-700 rounded-lg p-4 mb-3 border-l-4 border-blue-500">
                                        <h6 class="font-semibold text-gray-900 dark:text-white mb-2">Description:</h6>
                                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $milestone->description }}</p>
                                    </div>
                                @endif
                                
                                @php $progress = $milestone->tasks_progress @endphp
                                @if($progress['total'] > 0)
                                    <div class="ml-11 mb-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Milestone Progress</span>
                                            <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $progress['completed'] }}/{{ $progress['total'] }} tasks ({{ $progress['percentage'] }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Milestone Order Controls -->
                                <form action="{{ route('milestones.move', $milestone) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" 
                                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 bg-white dark:bg-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                </form>
                                <form action="{{ route('milestones.move', $milestone) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" 
                                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 bg-white dark:bg-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Add Task Form -->
                        <div class="ml-11 mb-4 p-4 bg-white dark:bg-gray-700 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                            <form action="{{ route('tasks.store', $milestone) }}" method="POST" class="flex gap-3">
                                @csrf
                                <input type="text" 
                                       name="title" 
                                       placeholder="Add new task for this milestone..."
                                       class="flex-1 px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
                                       required>
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Task
                                </button>
                            </form>
                        </div>

                        <!-- Tasks List -->
                        <div class="ml-11 space-y-2">
                            @forelse($milestone->tasks as $task)
                            <div class="flex items-start space-x-3 p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 group hover:shadow-md transition-all">
                                <form action="{{ route('tasks.updateStatus', $task) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            name="status" 
                                            value="{{ $task->next_status }}"
                                            class="flex items-center mt-1">
                                        <div class="{{ $task->checkbox_class }} hover:scale-110 transition-transform">
                                            @if($task->status === 'done')
                                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @elseif($task->status === 'doing')
                                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                            @endif
                                        </div>
                                    </button>
                                </form>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <span class="text-sm font-medium {{ $task->status === 'done' ? 'text-gray-500 dark:text-gray-400 line-through' : 'text-gray-800 dark:text-gray-200' }}">
                                            {{ $task->title }}
                                        </span>
                                        <span class="text-xs {{ $task->status_badge_color }} px-2 py-1 rounded-full ml-2">{{ $task->status_label }}</span>
                                    </div>
                                    @if($task->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $task->description }}</p>
                                    @endif
                                    @if($task->due_date)
                                        <div class="flex items-center mt-1">
                                            <svg class="w-3 h-3 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Due: {{ $task->due_date->format('M d, Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-8 bg-white dark:bg-gray-700 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <p class="text-sm">No tasks yet. Add your first task above!</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 dark:text-gray-400 py-12 bg-gray-50 dark:bg-gray-700 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h4 class="text-lg font-semibold mb-2">No Milestones Created Yet</h4>
                        <p class="mb-4">Start breaking down your goal into manageable milestones!</p>
                        <div class="text-sm text-gray-400">
                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            Tip: Each milestone should represent a significant step toward your goal
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-500 dark:text-gray-400 py-12">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="text-lg font-medium mb-2">No Goals Yet</h3>
            <p class="mb-4">Start by creating your first goal or import a roadmap!</p>
            <div class="space-y-2">
                <button @click="showAddGoalModal = true" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Goal
                </button>
                <a href="{{ route('roadmap') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md ml-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3a4 4 0 1 1 8 0v3"></path>
                    </svg>
                    Generate Roadmap
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Flowchart View -->
    <div x-show="viewMode === 'flowchart'" class="space-y-8">
        @forelse($goals as $goal)
        <div class="relative">
            <!-- Goal Node -->
            <div class="text-center mb-8">
                <div class="inline-block bg-blue-600 text-white px-8 py-4 rounded-2xl shadow-lg relative">
                    <h3 class="text-lg font-bold mb-1">{{ $goal->title }}</h3>
                    <div class="text-sm opacity-90">{{ $goal->progress_percentage }}% Complete</div>
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-blue-600 rotate-45"></div>
                </div>
            </div>

            <!-- Connector Line -->
            @if($goal->milestones->count() > 0)
            <div class="absolute left-1/2 transform -translate-x-1/2 w-0.5 h-8 bg-gray-300 dark:bg-gray-600"></div>
            @endif

            <!-- Milestones Flowchart -->
            <div class="space-y-6">
                @foreach($goal->milestones as $milestone)
                <div class="relative flex items-center justify-center">
                    @if(!$loop->last)
                    <!-- Vertical connector to next milestone -->
                    <div class="absolute left-1/2 transform -translate-x-1/2 top-full w-0.5 h-6 bg-gray-300 dark:bg-gray-600 z-0"></div>
                    @endif
                    
                    <!-- Milestone Node -->
                    <div class="relative bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-xl p-6 max-w-md shadow-lg z-10">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                {{ $loop->iteration }}
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $milestone->title }}</h4>
                        </div>
                        
                        @if($milestone->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ Str::limit($milestone->description, 100) }}</p>
                        @endif
                        
                        @php $progress = $milestone->tasks_progress @endphp
                        @if($progress['total'] > 0)
                        <div class="mb-3">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-gray-600 dark:text-gray-400">Progress</span>
                                <span class="font-medium">{{ $progress['completed'] }}/{{ $progress['total'] }} tasks</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Tasks Preview -->
                        @if($milestone->tasks->count() > 0)
                        <div class="space-y-1">
                            @foreach($milestone->tasks->take(3) as $task)
                            <div class="flex items-center text-xs">
                                <div class="w-3 h-3 mr-2 {{ $task->status === 'done' ? 'bg-green-500' : ($task->status === 'doing' ? 'bg-yellow-500' : 'bg-gray-300') }} rounded-full"></div>
                                <span class="{{ $task->status === 'done' ? 'line-through text-gray-500' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ Str::limit($task->title, 40) }}
                                </span>
                            </div>
                            @endforeach
                            @if($milestone->tasks->count() > 3)
                            <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                +{{ $milestone->tasks->count() - 3 }} more tasks
                            </div>
                            @endif
                        </div>
                        @endif
                        
                        <!-- Quick Actions -->
                        <div class="flex space-x-2 mt-4">
                            <button @click="openGoalDetails({{ $goal->id }})" 
                                    class="flex-1 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs rounded-md hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                View Details
                            </button>
                            <button @click="askAI('milestone', {{ $milestone->id }})" 
                                    class="flex-1 px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs rounded-md hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors">
                                Ask AI
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($goal->milestones->count() == 0)
            <div class="text-center py-8">
                <div class="inline-block bg-gray-100 dark:bg-gray-700 px-6 py-4 rounded-xl">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No milestones yet</p>
                    <button @click="openGoalDetails({{ $goal->id }})" 
                            class="mt-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                        Add Milestones
                    </button>
                </div>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-12">
            <div class="inline-block bg-gray-100 dark:bg-gray-700 px-8 py-6 rounded-2xl">
                <h3 class="text-lg font-medium mb-2 text-gray-700 dark:text-gray-300">No Goals Yet</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Create your first goal to see the flowchart</p>
                <button @click="showAddGoalModal = true" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create First Goal
                </button>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Add Goal Modal -->
    <div x-show="showAddGoalModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showAddGoalModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showAddGoalModal = false"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <!-- Modal panel -->
            <div x-show="showAddGoalModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add New Goal</h3>
                    <button @click="showAddGoalModal = false" 
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Add Goal Form -->
                <form action="/goals" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Goal Title
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="e.g., Master Laravel Development"
                               required>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3" 
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                  placeholder="Describe what you want to achieve..."
                                  required></textarea>
                    </div>

                    <div>
                        <label for="target_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Target Date
                        </label>
                        <input type="date" 
                               id="target_date" 
                               name="target_date" 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               required>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" 
                                @click="showAddGoalModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Create Goal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AI Assistant Modal -->
    <div x-show="showAIModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="showAIModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            <!-- Header -->
            <div class="bg-purple-600 text-white p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">AI Assistant</h3>
                            <p class="text-purple-100 text-sm">Get smart suggestions for your roadmap</p>
                        </div>
                    </div>
                    <button @click="showAIModal = false" 
                            class="text-white hover:text-purple-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <!-- Context Display -->
                <div x-show="aiContext.type" class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-blue-900 dark:text-blue-100">Context</span>
                    </div>
                    <p class="text-blue-800 dark:text-blue-200 text-sm" x-text="aiContext.description"></p>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <button @click="askAIQuestion('How can I break down this milestone into smaller tasks?')"
                            class="p-4 text-left bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="font-medium text-gray-900 dark:text-white mb-1">Break Down Tasks</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Get suggestions for smaller, manageable tasks</div>
                    </button>
                    
                    <button @click="askAIQuestion('What are the best practices for achieving this milestone?')"
                            class="p-4 text-left bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="font-medium text-gray-900 dark:text-white mb-1">Best Practices</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Learn optimal approaches and strategies</div>
                    </button>
                    
                    <button @click="askAIQuestion('What potential challenges should I expect?')"
                            class="p-4 text-left bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="font-medium text-gray-900 dark:text-white mb-1">Identify Challenges</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Anticipate obstacles and prepare solutions</div>
                    </button>
                    
                    <button @click="askAIQuestion('How can I optimize my timeline for this goal?')"
                            class="p-4 text-left bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="font-medium text-gray-900 dark:text-white mb-1">Optimize Timeline</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Get timeline and scheduling suggestions</div>
                    </button>
                </div>

                <!-- Custom Question Input -->
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ask a Custom Question</label>
                    <div class="flex space-x-3">
                        <input type="text" 
                               x-model="customAIQuestion"
                               @keyup.enter="askAIQuestion(customAIQuestion)"
                               placeholder="Type your question here..."
                               class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <button @click="askAIQuestion(customAIQuestion)"
                                :disabled="!customAIQuestion.trim()"
                                class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            Ask
                        </button>
                    </div>
                </div>

                <!-- AI Response -->
                <div x-show="aiResponse" class="mt-6">
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium text-green-900 dark:text-green-100">AI Suggestion</span>
                        </div>
                        <div class="text-green-800 dark:text-green-200 text-sm whitespace-pre-wrap" x-text="aiResponse"></div>
                    </div>
                </div>

                <!-- Loading State -->
                <div x-show="aiLoading" class="mt-6">
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="animate-spin w-5 h-5 text-purple-600 dark:text-purple-400 mr-3" fill="none" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                                <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-purple-700 dark:text-purple-300">AI is thinking...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                <div class="flex justify-between items-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Powered by AI • Get personalized roadmap advice</p>
                    <button @click="showAIModal = false" 
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Goal Modal -->
    <div x-show="showDeleteModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="showDeleteModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Delete Goal</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Are you sure you want to delete this goal? This action cannot be undone and will also delete all associated milestones and tasks.
                    </p>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex space-x-3">
                <button @click="showDeleteModal = false" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <button @click="confirmDelete()" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function goalsPage() {
    return {
        // Modal states
        showAddGoalModal: false,
        showDeleteModal: false,
        showAIModal: false,
        
        // View modes
        viewMode: 'list', // 'list' or 'flowchart'
        
        // Goal management
        openGoals: [],
        goalToDelete: null,
        
        // AI Assistant
        aiContext: {
            type: null,
            id: null,
            description: ''
        },
        customAIQuestion: '',
        aiResponse: '',
        aiLoading: false,

        // Toggle goal details in list view
        toggleGoalDetails(goalId) {
            if (this.openGoals.includes(goalId)) {
                this.openGoals = this.openGoals.filter(id => id !== goalId);
            } else {
                this.openGoals.push(goalId);
            }
        },

        // Open goal details (for flowchart view)
        openGoalDetails(goalId) {
            // You can implement this to open a detailed modal or navigate to goal detail page
            window.location.href = `/goals/${goalId}`;
        },

        // View mode switching
        setViewMode(mode) {
            this.viewMode = mode;
        },

        // Delete functionality
        deleteGoal(goalId) {
            this.goalToDelete = goalId;
            this.showDeleteModal = true;
        },

        confirmDelete() {
            if (this.goalToDelete) {
                // Create a form and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/goals/${this.goalToDelete}`;
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                const tokenField = document.createElement('input');
                tokenField.type = 'hidden';
                tokenField.name = '_token';
                tokenField.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                form.appendChild(methodField);
                form.appendChild(tokenField);
                document.body.appendChild(form);
                form.submit();
            }
        },

        // AI Assistant functionality
        openAIAssistant() {
            this.aiContext = {
                type: 'general',
                id: null,
                description: 'Get general advice about your goals and roadmaps'
            };
            this.aiResponse = '';
            this.customAIQuestion = '';
            this.showAIModal = true;
        },

        askAI(type, id) {
            // Set context based on type and id
            if (type === 'milestone') {
                this.aiContext = {
                    type: 'milestone',
                    id: id,
                    description: `Getting advice for milestone #${id}`
                };
            } else if (type === 'goal') {
                this.aiContext = {
                    type: 'goal',
                    id: id,
                    description: `Getting advice for goal #${id}`
                };
            }
            
            this.aiResponse = '';
            this.customAIQuestion = '';
            this.showAIModal = true;
        },

        async askAIQuestion(question) {
            if (!question.trim()) return;
            
            this.aiLoading = true;
            this.aiResponse = '';
            
            try {
                const response = await fetch('/api/ai-assistant', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        question: question,
                        context: this.aiContext
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.aiResponse = data.response;
                } else {
                    this.aiResponse = 'Sorry, I encountered an error while processing your question. Please try again.';
                }
            } catch (error) {
                console.error('AI Assistant Error:', error);
                this.aiResponse = 'Sorry, I encountered an error while processing your question. Please try again.';
            } finally {
                this.aiLoading = false;
                this.customAIQuestion = '';
            }
        }
    }
}
</script>
@endpush
@endsection
