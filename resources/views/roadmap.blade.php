@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Learning Roadmap') }}
    </h2>
@endsection

@section('content')
<div class="py-12" x-data="roadmapManager()">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Toast Notifications -->
        <div x-show="notification.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             class="fixed top-4 right-4 z-50 max-w-sm w-full">
            <div :class="notification.type === 'success' ? 'bg-green-500' : 'bg-red-500'" 
                 class="text-white px-6 py-4 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg x-show="notification.type === 'success'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <svg x-show="notification.type === 'error'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="font-medium" x-text="notification.message"></p>
                    </div>
                    <button @click="hideNotification()" class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Flow Explanation Banner -->
        <div class="bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 rounded-xl p-6 mb-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3a4 4 0 1 1 8 0v3"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">� Buat Roadmap Pembelajaran</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Roadmap yang Anda buat akan otomatis dikonversi menjadi Goals dengan struktur Milestones dan Tasks yang dapat dikelola di halaman Goals.
                    </p>
                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <span class="text-green-600 dark:text-green-400 font-bold">1</span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Input Manual</div>
                                <p class="text-gray-600 dark:text-gray-400">Buat roadmap dengan form yang mudah</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                <span class="text-purple-600 dark:text-purple-400 font-bold">2</span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">Saran AI</div>
                                <p class="text-gray-600 dark:text-gray-400">AI generate roadmap otomatis</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('goals') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Lihat Goals Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 dark:border-gray-700 mb-8">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="activeTab = 'manual'" 
                                :class="activeTab === 'manual' ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30'"
                                class="py-3 px-6 border-b-2 font-medium text-sm whitespace-nowrap rounded-t-lg transition-all duration-200">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span>Manual Input</span>
                            </div>
                        </button>
                        <button @click="activeTab = 'ai'" 
                                :class="activeTab === 'ai' ? 'border-blue-500 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/30'"
                                class="py-3 px-6 border-b-2 font-medium text-sm whitespace-nowrap rounded-t-lg transition-all duration-200">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                <span>AI Generated</span>
                            </div>
                        </button>
                    </nav>
                </div>

                <!-- Manual Roadmap Tab -->
                <div x-show="activeTab === 'manual'" x-transition>
                    <div class="space-y-8">
                        <!-- Header -->
                        <div class="text-center">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Buat Roadmap Pribadi</h3>
                            <p class="text-gray-600 dark:text-gray-400">Susun rencana pembelajaran Anda step by step dengan mudah</p>
                        </div>

                        <!-- Goal Information -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Informasi Goal
                            </h4>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Judul Goal *</label>
                                    <input type="text" x-model="formData.goal_title" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors"
                                           placeholder="Contoh: Menguasai Laravel dalam 3 bulan" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Target Selesai *</label>
                                    <input type="date" x-model="formData.target_date" 
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors" required>
                                </div>
                            </div>
                        </div>

                        <!-- Milestones -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                    Milestones
                                </h4>
                                <button @click="addMilestone" type="button"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah Milestone
                                </button>
                            </div>

                            <div class="space-y-6">
                                <template x-for="(milestone, mIndex) in formData.milestones" :key="mIndex">
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30">
                                        <!-- Milestone Header -->
                                        <div class="flex justify-between items-start mb-4">
                                            <h5 class="text-md font-medium text-gray-900 dark:text-white" x-text="`Milestone ${mIndex + 1}`"></h5>
                                            <button @click="removeMilestone(mIndex)" type="button"
                                                    class="text-red-400 hover:text-red-600 p-1 rounded transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Milestone Fields -->
                                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Milestone *</label>
                                                <input type="text" x-model="milestone.title" 
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
                                                       placeholder="Contoh: Laravel Basics" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Tanggal</label>
                                                <input type="date" x-model="milestone.target_date" 
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white">
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                                            <textarea x-model="milestone.description" rows="2"
                                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white"
                                                      placeholder="Jelaskan apa yang akan dicapai di milestone ini..."></textarea>
                                        </div>

                                        <!-- Tasks -->
                                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Tasks
                                                </h6>
                                                <button @click="addTask(mIndex)" type="button"
                                                        class="text-sm bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-md hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                                    + Task
                                                </button>
                                            </div>

                                            <div class="space-y-3">
                                                <template x-for="(task, tIndex) in milestone.tasks" :key="tIndex">
                                                    <div class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-600">
                                                        <div class="flex-1 grid md:grid-cols-3 gap-3">
                                                            <input type="text" x-model="task.title" 
                                                                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                                   placeholder="Nama task..." required>
                                                            <input type="text" x-model="task.description" 
                                                                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                                   placeholder="Deskripsi (opsional)">
                                                            <input type="date" x-model="task.due_date" 
                                                                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                        </div>
                                                        <button @click="removeTask(mIndex, tIndex)" type="button"
                                                                class="text-red-400 hover:text-red-600 p-1 rounded transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                                
                                                <div x-show="milestone.tasks.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                                                    Belum ada tasks. Klik "+ Task" untuk menambah.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="formData.milestones.length === 0" class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada milestone</p>
                                    <button @click="addMilestone" type="button"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Tambah Milestone Pertama
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-center">
                            <button @click="submitManualForm" :disabled="loading || !formData.goal_title || !formData.target_date || formData.milestones.length === 0"
                                    :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'"
                                    class="inline-flex items-center px-8 py-3 bg-blue-600 text-white font-medium rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
                                <svg x-show="!loading" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <svg x-show="loading" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="loading ? 'Membuat Roadmap...' : 'Buat Roadmap'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- AI Roadmap Tab -->
                <div x-show="activeTab === 'ai'" x-transition>
                    <div class="space-y-6">
                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-purple-800 dark:text-purple-200 mb-2">🤖 Generate Roadmap dengan AI</h3>
                            <p class="text-purple-600 dark:text-purple-300 text-sm">Biarkan AI membuatkan roadmap pembelajaran yang disesuaikan dengan tujuan dan level Anda.</p>
                        </div>

                        <!-- AI Input Form -->
                        <form @submit.prevent="generateAIRoadmap" x-show="!aiResult">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Tujuan Akhir</label>
                                    <input type="text" x-model="aiData.goal_title" 
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                           placeholder="Contoh: Menjadi Full Stack Developer" required>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Target Date</label>
                                        <input type="date" x-model="aiData.target_date" 
                                               class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Level Saat Ini</label>
                                        <select x-model="aiData.self_assessed_level" 
                                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required>
                                            <option value="">Pilih level</option>
                                            <option value="beginner">Pemula (Beginner)</option>
                                            <option value="intermediate">Menengah (Intermediate)</option>
                                            <option value="advanced">Mahir (Advanced)</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Keterbatasan/Constraints</label>
                                    <textarea x-model="aiData.constraints" rows="3"
                                              class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                              placeholder="Contoh: Hanya bisa belajar 2 jam per hari, lebih suka pembelajaran praktikal, memiliki laptop dengan spec terbatas, dll."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-2">Topik Prioritas (Opsional)</label>
                                    <input type="text" x-model="aiData.top_topics" 
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                           placeholder="Contoh: React, Node.js, Database, Testing">
                                </div>

                                <button type="submit" :disabled="loading"
                                        class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded disabled:opacity-50">
                                    <span x-show="!loading">🤖 Generate dari AI</span>
                                    <span x-show="loading">⏳ AI sedang membuat roadmap...</span>
                                </button>
                            </div>
                        </form>

                        <!-- AI Results -->
                        <div x-show="aiResult" x-transition>
                            <div class="space-y-6">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-medium">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Roadmap Generated by AI
                                    </h3>
                                    <div class="space-x-2">
                                        <button @click="aiResult = null" 
                                                class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                                            </svg>
                                            Kembali
                                        </button>
                                        <button @click="acceptAIRoadmap" :disabled="loading"
                                                class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded disabled:opacity-50">
                                            <span x-show="!loading">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Terima Roadmap
                                            </span>
                                            <span x-show="loading">⏳ Membuat...</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Weekly Roadmap Table -->
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-800">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Minggu</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tema</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Target Outcomes</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tasks</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Resources</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                            <template x-for="(week, index) in aiResult?.weeks || []" :key="index">
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        Week <span x-text="week.week"></span>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                        <div class="font-medium" x-text="week.theme"></div>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                        <ul class="list-disc list-inside space-y-1">
                                                            <template x-for="outcome in week.outcomes" :key="outcome">
                                                                <li x-text="outcome" class="text-xs"></li>
                                                            </template>
                                                        </ul>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                        <ul class="list-disc list-inside space-y-1">
                                                            <template x-for="task in week.tasks" :key="task">
                                                                <li x-text="task" class="text-xs"></li>
                                                            </template>
                                                        </ul>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                        <ul class="list-disc list-inside space-y-1">
                                                            <template x-for="resource in week.resources" :key="resource">
                                                                <li x-text="resource" class="text-xs"></li>
                                                            </template>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function roadmapManager() {
    return {
        activeTab: 'manual',
        loading: false,
        aiResult: null,
        
        // Notification system
        notification: {
            show: false,
            message: '',
            type: 'success' // 'success' or 'error'
        },
        
        // Manual Form data
        formData: {
            goal_title: '',
            target_date: '',
            milestones: []
        },
        
        // AI data
        aiData: {
            goal_title: '',
            target_date: '',
            self_assessed_level: '',
            constraints: '',
            top_topics: ''
        },
        
        init() {
            // Initialize with one milestone
            this.addMilestone();
        },
        
        addMilestone() {
            this.formData.milestones.push({
                title: '',
                description: '',
                target_date: '',
                tasks: [{ title: '', description: '', due_date: '' }]
            });
        },
        
        removeMilestone(index) {
            this.formData.milestones.splice(index, 1);
        },
        
        addTask(milestoneIndex) {
            this.formData.milestones[milestoneIndex].tasks.push({
                title: '',
                description: '',
                due_date: ''
            });
        },
        
        removeTask(milestoneIndex, taskIndex) {
            this.formData.milestones[milestoneIndex].tasks.splice(taskIndex, 1);
        },
        
        async submitManualForm() {
            // Validate form data before submitting
            if (!this.formData.goal_title || !this.formData.goal_title.trim()) {
                this.showNotification('Goal title tidak boleh kosong!', 'error');
                return;
            }
            
            if (!this.formData.target_date) {
                this.showNotification('Target date tidak boleh kosong!', 'error');
                return;
            }
            
            if (this.formData.milestones.length === 0) {
                this.showNotification('Minimal harus ada 1 milestone!', 'error');
                return;
            }
            
            // Validate each milestone
            for (let i = 0; i < this.formData.milestones.length; i++) {
                const milestone = this.formData.milestones[i];
                if (!milestone.title || !milestone.title.trim()) {
                    this.showNotification(`Milestone ${i + 1} title tidak boleh kosong!`, 'error');
                    return;
                }
                
                // Trim and limit milestone title length
                milestone.title = milestone.title.trim().substring(0, 500);
                
                // Validate tasks
                for (let j = 0; j < milestone.tasks.length; j++) {
                    const task = milestone.tasks[j];
                    if (task.title && task.title.trim()) {
                        // Trim and limit task title length
                        task.title = task.title.trim().substring(0, 400);
                    } else {
                        // Remove empty tasks
                        milestone.tasks.splice(j, 1);
                        j--; // Adjust index after removal
                    }
                }
                
                // Ensure each milestone has at least one task
                if (milestone.tasks.length === 0) {
                    milestone.tasks.push({ title: 'Default task untuk ' + milestone.title, description: '', due_date: '' });
                }
            }
            
            this.loading = true;
            try {
                const response = await fetch('/roadmap/import', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        type: 'manual_form',
                        goal_title: this.formData.goal_title.trim(),
                        target_date: this.formData.target_date,
                        roadmap_data: { milestones: this.formData.milestones }
                    })
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    this.showNotification('Roadmap berhasil dibuat dan diimport ke Goals! Anda akan diarahkan ke halaman Goals.', 'success');
                    setTimeout(() => {
                        window.location.href = '/goals';
                    }, 2000);
                } else {
                    this.showNotification(result.message || 'Terjadi kesalahan saat membuat roadmap', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Error: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async generateAIRoadmap() {
            this.loading = true;
            try {
                const response = await fetch('/api/ai/roadmap', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.aiData)
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('API Error:', errorText);
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const result = await response.json();
                this.aiResult = result;
                
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Error: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async acceptAIRoadmap() {
            this.loading = true;
            try {
                const response = await fetch('/roadmap/import', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        type: 'ai_generated',
                        goal_title: this.aiData.goal_title,
                        target_date: this.aiData.target_date,
                        roadmap_data: this.aiResult
                    })
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    this.showNotification('Roadmap AI berhasil dibuat dan diimport ke Goals! Anda akan diarahkan ke halaman Goals.', 'success');
                    setTimeout(() => {
                        window.location.href = '/goals';
                    }, 2000);
                } else {
                    this.showNotification(result.message || 'Terjadi kesalahan saat membuat roadmap AI', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Error: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        // Notification methods
        showNotification(message, type = 'success') {
            this.notification.message = message;
            this.notification.type = type;
            this.notification.show = true;
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                this.hideNotification();
            }, 5000);
        },
        
        hideNotification() {
            this.notification.show = false;
        }
    }
}
</script>
@endpush
@endsection
