@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="todayPage()">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Today's Learning</h1>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    <!-- Main Form -->
    <form action="{{ route('sessions.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Learning Session Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Apa yang kupelajari?</h2>
            </div>
            <div class="card-body space-y-4">
                <!-- Source -->
                <div>
                    <label for="source" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Sumber Belajar
                    </label>
                    <input type="text" 
                           id="source" 
                           name="source" 
                           class="input" 
                           placeholder="Contoh: YouTube - Crash Course Python, Buku Laravel, dll"
                           required
                           value="{{ old('source') }}">
                    @error('source')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration and Difficulty Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Duration -->
                    <div>
                        <label for="duration_min" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Durasi (menit)
                        </label>
                        <input type="number" 
                               id="duration_min" 
                               name="duration_min" 
                               class="input" 
                               min="1" 
                               max="600"
                               placeholder="30"
                               required
                               value="{{ old('duration_min') }}">
                        @error('duration_min')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Difficulty -->
                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Tingkat Kesulitan: <span x-text="difficulty"></span>/5
                        </label>
                        <input type="range" 
                               id="difficulty" 
                               name="difficulty" 
                               min="1" 
                               max="5" 
                               x-model="difficulty"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 slider">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <span>Sangat Mudah</span>
                            <span>Sangat Sulit</span>
                        </div>
                        @error('difficulty')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Topics -->
                <div>
                    <label for="topics" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Topik yang Dipelajari
                    </label>
                    <input type="text" 
                           id="topics" 
                           name="topics" 
                           class="input" 
                           placeholder="Contoh: PHP Variables, Laravel Routes, Database Migration"
                           required
                           value="{{ old('topics') }}">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pisahkan dengan koma (,)</p>
                    @error('topics')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- What Learned -->
                <div>
                    <label for="what_learned" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Apa yang Dipelajari?
                    </label>
                    <textarea id="what_learned" 
                              name="what_learned" 
                              rows="4" 
                              class="textarea" 
                              placeholder="Jelaskan secara detail apa yang kamu pelajari hari ini..."
                              required>{{ old('what_learned') }}</textarea>
                    @error('what_learned')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Mood Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Mood hari ini</h2>
            </div>
            <div class="card-body space-y-4">
                <!-- Mood Score -->
                <div>
                    <label for="mood_score" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Mood Score: <span x-text="moodScore"></span>/5 <span x-text="getMoodEmoji(moodScore)"></span>
                    </label>
                    <input type="range" 
                           id="mood_score" 
                           name="mood_score" 
                           min="1" 
                           max="5" 
                           x-model="moodScore"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span>😞 Buruk</span>
                        <span>😐 Biasa</span>
                        <span>😊 Bagus</span>
                    </div>
                    @error('mood_score')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mood Note -->
                <div>
                    <label for="mood_note" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Catatan Mood (Opsional)
                    </label>
                    <input type="text" 
                           id="mood_note" 
                           name="mood_note" 
                           class="input" 
                           placeholder="Ceritakan perasaanmu hari ini..."
                           value="{{ old('mood_note') }}">
                    @error('mood_note')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Journal Card (Optional) -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Jurnal Singkat (Opsional)</h2>
            </div>
            <div class="card-body space-y-4">
                <!-- Journal Content -->
                <div>
                    <label for="journal_content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Isi Jurnal
                    </label>
                    <textarea id="journal_content" 
                              name="journal_content" 
                              rows="3" 
                              class="textarea" 
                              placeholder="Tulis refleksi atau catatan pribadi tentang pembelajaran hari ini...">{{ old('journal_content') }}</textarea>
                    @error('journal_content')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Private Checkbox -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="journal_is_private" 
                           name="journal_is_private" 
                           value="1"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                           {{ old('journal_is_private') ? 'checked' : '' }}>
                    <label for="journal_is_private" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Jadikan jurnal ini privat
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <button type="submit" class="btn-primary flex-1">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Simpan
            </button>
            
            <button type="button" 
                    @click="openAiReviewModal()" 
                    class="btn-secondary flex-1">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                Review AI
            </button>
        </div>
    </form>

    <!-- AI Review Modal -->
    <div x-show="showAiModal" 
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
            <div x-show="showAiModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="closeAiReviewModal()"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <!-- Modal panel -->
            <div x-show="showAiModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-6">
                
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        🤖 AI Review - Analisis Pembelajaran
                    </h3>
                    <button @click="closeAiReviewModal()" 
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Loading State -->
                <div x-show="aiLoading" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">Menganalisis pembelajaran Anda...</p>
                </div>

                <!-- AI Review Content -->
                <div x-show="!aiLoading && aiReview" class="space-y-6">
                    <!-- Summary -->
                    <div x-show="aiReview?.summary">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Ringkasan
                        </h4>
                        <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                            <template x-for="item in aiReview.summary" :key="item">
                                <li class="flex items-start">
                                    <span class="text-blue-500 mr-2">•</span>
                                    <span x-text="item"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Misconceptions -->
                    <div x-show="aiReview?.misconceptions?.length > 0">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Potensi Miskonsepsi
                        </h4>
                        <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                            <template x-for="item in aiReview.misconceptions" :key="item">
                                <li class="flex items-start">
                                    <span class="text-yellow-500 mr-2">•</span>
                                    <span x-text="item"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Practice Recommendations -->
                    <div x-show="aiReview?.recommendations">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            Rekomendasi Latihan
                        </h4>
                        <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                            <template x-for="item in aiReview.recommendations" :key="item">
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">•</span>
                                    <span x-text="item"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Level & Next Step -->
                    <div x-show="aiReview?.level_assessment" class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Level & Langkah Selanjutnya
                        </h4>
                        <p class="text-gray-700 dark:text-gray-300" x-text="aiReview.level_assessment"></p>
                    </div>
                </div>

                <!-- Error State -->
                <div x-show="aiError" class="text-center py-8">
                    <div class="text-red-500 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400" x-text="aiError"></p>
                    <button @click="retryAiReview()" class="btn-primary mt-4">
                        Coba Lagi
                    </button>
                </div>

                <!-- Modal Footer -->
                <div class="mt-6 flex justify-end">
                    <button @click="closeAiReviewModal()" class="btn-secondary">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Custom slider styling */
.slider::-webkit-slider-thumb {
    appearance: none;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
}

.slider::-moz-range-thumb {
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
    border: none;
}
</style>
@endpush

@push('scripts')
<script>
function todayPage() {
    return {
        difficulty: parseInt('{{ old("difficulty") }}') || 3,
        moodScore: parseInt('{{ old("mood_score") }}') || 3,
        showAiModal: false,
        aiLoading: false,
        aiReview: null,
        aiError: null,

        getMoodEmoji(score) {
            const emojis = ['😞', '😟', '😐', '😊', '😄'];
            return emojis[score - 1] || '😐';
        },

        async openAiReviewModal() {
            const source = document.getElementById('source').value.trim();
            const whatLearned = document.getElementById('what_learned').value.trim();
            const topics = document.getElementById('topics').value.trim();

            if (!source || !whatLearned || !topics) {
                alert('Mohon lengkapi field wajib (Sumber, Topik, dan Apa yang Dipelajari) sebelum melakukan AI Review.');
                return;
            }

            this.showAiModal = true;
            this.aiLoading = true;
            this.aiReview = null;
            this.aiError = null;

            await this.fetchAiReview();
        },

        closeAiReviewModal() {
            this.showAiModal = false;
            this.aiLoading = false;
        },

        async fetchAiReview() {
            try {
                const formData = {
                    source: document.getElementById('source').value,
                    duration_min: document.getElementById('duration_min').value,
                    what_learned: document.getElementById('what_learned').value,
                    difficulty: this.difficulty,
                    topics: document.getElementById('topics').value,
                    mood_score: this.moodScore,
                    mood_note: document.getElementById('mood_note').value
                };

                const response = await fetch('/api/ai/review', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    this.aiReview = data.data;
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat menganalisis');
                }
            } catch (error) {
                console.error('AI Review Error:', error);
                this.aiError = 'Maaf, terjadi kesalahan saat menganalisis pembelajaran Anda. Silakan coba lagi.';
            } finally {
                this.aiLoading = false;
            }
        },

        async retryAiReview() {
            this.aiError = null;
            this.aiLoading = true;
            await this.fetchAiReview();
        }
    }
}
</script>
@endpush
@endsection
