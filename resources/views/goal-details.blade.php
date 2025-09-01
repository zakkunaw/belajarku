@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Goal Details
        </h2>
        <a href="{{ route('goals') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            Back to Goals
        </a>
    </div>
@endsection

@section('content')
<div class="py-12" x-data="goalDetails()">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Goal Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex items-start justify-between mb-6">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">{{ $goal->title }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">{{ $goal->description }}</p>
                    
                    <div class="flex items-center space-x-8 text-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-400">Target: {{ \Carbon\Carbon::parse($goal->target_date)->format('M d, Y') }}</span>
                        </div>
                        
                        <div class="flex items-center">
                            <span class="px-3 py-1 text-sm font-medium rounded-full
                                {{ $goal->status === 'done' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 
                                   ($goal->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 
                                   'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                {{ ucfirst(str_replace('_', ' ', $goal->status)) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $goal->progress_percentage }}%</div>
                            <span class="text-gray-600 dark:text-gray-400 ml-1">Complete</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <button @click="toggleView()" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <span x-text="viewMode === 'roadmap' ? 'List View' : 'Roadmap View'"></span>
                    </button>
                    <button onclick="window.history.back()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Edit Goal
                    </button>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $goal->progress_percentage }}%"></div>
            </div>
        </div>

        <!-- View Toggle Content -->
        <div>
            <!-- List View -->
            <div x-show="viewMode === 'list'" class="space-y-6">
                @forelse($goal->milestones as $milestone)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $milestone->title }}</h3>
                            @if($milestone->description)
                            <p class="text-gray-600 dark:text-gray-400 mb-3">{{ $milestone->description }}</p>
                            @endif
                            
                            @php $progress = $milestone->tasks_progress @endphp
                            @if($progress['total'] > 0)
                            <div class="mb-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Tasks Progress</span>
                                    <span class="font-medium">{{ $progress['completed'] }}/{{ $progress['total'] }} completed</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="ml-4">
                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                Milestone {{ $loop->iteration }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Tasks -->
                    @if($milestone->tasks->count() > 0)
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900 dark:text-white">Tasks:</h4>
                        @foreach($milestone->tasks as $task)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-5 h-5 rounded-full flex items-center justify-center
                                    {{ $task->status === 'done' ? 'bg-green-500' : 
                                       ($task->status === 'doing' ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600') }}">
                                    @if($task->status === 'done')
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium {{ $task->status === 'done' ? 'line-through text-gray-500 dark:text-gray-400' : 'text-gray-900 dark:text-white' }}">
                                    {{ $task->title }}
                                </p>
                                @if($task->description)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $task->description }}</p>
                                @endif
                            </div>
                            <div class="flex-shrink-0">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $task->status === 'done' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 
                                       ($task->status === 'doing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 
                                       'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6">
                        <p class="text-gray-500 dark:text-gray-400">No tasks added to this milestone yet.</p>
                    </div>
                    @endif
                </div>
                @empty
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Milestones Yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Start by adding your first milestone to this goal.</p>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Add Milestone
                        </button>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Roadmap View (roadmap.sh style) -->
            <div x-show="viewMode === 'roadmap'" class="relative">
                @if($goal->milestones->count() > 0)
                <div class="flex flex-col space-y-8">
                    @foreach($goal->milestones as $milestone)
                    <div class="relative">
                        <!-- Milestone Box -->
                        <div class="bg-white dark:bg-gray-800 border-2 border-blue-200 dark:border-blue-700 rounded-xl p-6 shadow-lg relative z-10 max-w-lg mx-auto">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                                        {{ $loop->iteration }}
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $milestone->title }}</h3>
                                </div>
                                
                                @php $progress = $milestone->tasks_progress @endphp
                                @if($progress['total'] > 0)
                                <div class="text-right">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $progress['completed'] }}/{{ $progress['total'] }}</div>
                                    <div class="text-xs text-gray-500">tasks</div>
                                </div>
                                @endif
                            </div>
                            
                            @if($milestone->description)
                            <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">{{ $milestone->description }}</p>
                            @endif
                            
                            <!-- Progress for this milestone -->
                            @if($progress['total'] > 0)
                            <div class="mb-4">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-500">Progress</span>
                                    <span class="font-medium">{{ $progress['percentage'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progress['percentage'] }}%"></div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Key Tasks Preview -->
                            @if($milestone->tasks->count() > 0)
                            <div class="space-y-2">
                                @foreach($milestone->tasks->take(3) as $task)
                                <div class="flex items-center space-x-2 text-sm">
                                    <div class="w-3 h-3 rounded-full {{ $task->status === 'done' ? 'bg-green-500' : ($task->status === 'doing' ? 'bg-blue-500' : 'bg-gray-300') }}"></div>
                                    <span class="{{ $task->status === 'done' ? 'line-through text-gray-500' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ Str::limit($task->title, 40) }}
                                    </span>
                                </div>
                                @endforeach
                                @if($milestone->tasks->count() > 3)
                                <div class="text-xs text-gray-500 text-center">
                                    +{{ $milestone->tasks->count() - 3 }} more tasks
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                        
                        <!-- Connection Arrow -->
                        @if(!$loop->last)
                        <div class="flex justify-center mt-4 mb-4">
                            <div class="w-px h-8 bg-blue-300 dark:bg-blue-600 relative">
                                <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2">
                                    <div class="w-0 h-0 border-l-4 border-r-4 border-t-8 border-transparent border-t-blue-300 dark:border-t-blue-600"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                    
                    <!-- Completion Badge -->
                    <div class="flex justify-center mt-8">
                        <div class="bg-{{ $goal->status === 'done' ? 'green' : ($goal->status === 'in_progress' ? 'blue' : 'gray') }}-100 
                                    border-2 border-{{ $goal->status === 'done' ? 'green' : ($goal->status === 'in_progress' ? 'blue' : 'gray') }}-300 
                                    dark:bg-{{ $goal->status === 'done' ? 'green' : ($goal->status === 'in_progress' ? 'blue' : 'gray') }}-900/30 
                                    rounded-full p-4">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-{{ $goal->status === 'done' ? 'green' : ($goal->status === 'in_progress' ? 'blue' : 'gray') }}-600 
                                           text-white rounded-full flex items-center justify-center mx-auto mb-2">
                                    @if($goal->status === 'done')
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    @else
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5 9.293 10.793a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </div>
                                <div class="text-sm font-medium text-{{ $goal->status === 'done' ? 'green' : ($goal->status === 'in_progress' ? 'blue' : 'gray') }}-800 
                                           dark:text-{{ $goal->status === 'done' ? 'green' : ($goal->status === 'in_progress' ? 'blue' : 'gray') }}-200">
                                    {{ $goal->status === 'done' ? 'Goal Completed!' : 'In Progress' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">No Roadmap Yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">Create milestones to see your learning roadmap visualization.</p>
                        <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Add First Milestone
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function goalDetails() {
    return {
        viewMode: 'roadmap', // Default to roadmap view

        toggleView() {
            this.viewMode = this.viewMode === 'roadmap' ? 'list' : 'roadmap';
        }
    }
}
</script>
@endsection
