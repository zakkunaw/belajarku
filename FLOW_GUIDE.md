# 🗺️ Panduan Flow Aplikasi BelajarKu

## Penjelasan Alur Aplikasi

### 1. 📝 Halaman Roadmap (`/roadmap`)

**Fungsi**: Tempat membuat rencana pembelajaran
**Cara Kerja**:

-   **Manual JSON**: Input roadmap dalam format JSON dengan struktur goals, milestones, dan tasks
-   **Manual Form**: Gunakan form grid untuk input data roadmap secara visual
-   **AI Generated**: Gunakan AI Gemini untuk generate roadmap otomatis berdasarkan topik pembelajaran

**Input yang dibutuhkan**:

```json
{
    "goal_title": "Judul Goal",
    "target_date": "2025-12-31",
    "roadmap_data": {
        "milestones": [
            {
                "title": "Milestone 1",
                "description": "Deskripsi milestone",
                "target_date": "2025-10-15",
                "tasks": [
                    {
                        "title": "Task 1",
                        "description": "Deskripsi task",
                        "due_date": "2025-10-10"
                    }
                ]
            }
        ]
    }
}
```

### 2. 🔄 Proses Import Roadmap

**Yang Terjadi di Backend**:

1. **RoadmapController@import** menerima data roadmap
2. **Membuat Goal** baru dengan title dan target_date
3. **Membuat Milestones** untuk setiap milestone dalam roadmap
4. **Membuat Tasks** untuk setiap task dalam milestone
5. **Menyimpan ke Database** dengan struktur relasi yang benar

**Database Structure yang Dibuat**:

```
Goal (table: goals)
├── id
├── user_id
├── title
├── description
├── target_date
├── status (planned/in_progress/done)
└── progress_percentage

Milestone (table: milestones)
├── id
├── goal_id (FK)
├── title
├── description
├── target_date
├── status (pending/in_progress/done)
└── order_index

Task (table: tasks)
├── id
├── milestone_id (FK)
├── title
├── description
├── due_date
└── status (pending/todo/doing/done)
```

### 3. 📋 Halaman Goals (`/goals`)

**Fungsi**: Menampilkan dan mengelola goals yang sudah dibuat
**Fitur yang Tersedia**:

#### a. **Tampilan Goals**

-   Menampilkan semua goals user dengan status dan progress
-   Badge status goals dengan warna berbeda
-   Progress percentage berdasarkan completed tasks
-   Target date dan description goals

#### b. **Manajemen Milestones**

-   **Tambah Milestone Baru**: Form untuk menambah milestone ke goal
-   **Lihat Progress**: Progress bar berdasarkan tasks completed
-   **Reorder Milestones**: Tombol up/down untuk mengatur urutan
-   **Expand/Collapse**: Toggle untuk melihat detail milestone

#### c. **Manajemen Tasks**

-   **Tambah Task Baru**: Quick add form untuk menambah task ke milestone
-   **Update Status Task**: Klik checkbox untuk mengubah status (pending → todo → doing → done)
-   **Visual Indicators**: Checkbox dengan warna berbeda per status
-   **Task Details**: Tampilkan description dan due_date jika ada

#### d. **Status Management**

-   **Goal Status**: planned → in_progress → done
-   **Milestone Status**: pending → in_progress → done
-   **Task Status**: pending → todo → doing → done

### 4. 🔄 Flow Lengkap Penggunaan

```
1. USER BUAT ROADMAP
   ↓
2. PILIH MODE:
   • Manual JSON → Input JSON struktur
   • Manual Form → Isi form grid
   • AI Generated → Input topik, AI generate
   ↓
3. SUBMIT ROADMAP
   ↓
4. BACKEND PROSES:
   • Validasi data
   • Buat Goal baru
   • Buat Milestones
   • Buat Tasks
   • Simpan ke database
   ↓
5. REDIRECT KE /goals
   ↓
6. USER KELOLA GOALS:
   • Lihat progress
   • Update status
   • Tambah milestone/task
   • Reorder items
   ↓
7. TRACKING PROGRESS:
   • Task completion
   • Milestone progress
   • Goal achievement
```

### 5. 🚀 Contoh Penggunaan

#### Scenario: Belajar Laravel

1. **Di Roadmap**: User input "Belajar Laravel" sebagai topik
2. **AI Generate**: Menghasilkan roadmap 8 minggu belajar Laravel
3. **Import ke Goals**: Roadmap jadi 1 Goal dengan 8 Milestones (per minggu)
4. **Di Goals**: User bisa:
    - Lihat milestone "Week 1: Laravel Basics"
    - Tambah tasks seperti "Install Laravel", "Buat first route"
    - Update status task menjadi "doing" ketika sedang dikerjakan
    - Mark "done" ketika selesai
    - Lihat progress overall goal

### 6. 🔧 Fitur Teknis

#### Controllers:

-   **RoadmapController**: Handle import roadmap
-   **GoalController**: CRUD goals dan update status
-   **MilestoneController**: Add milestone, reorder
-   **TaskController**: Add task, update status

#### Models & Relationships:

-   **User** hasMany **Goals**
-   **Goal** hasMany **Milestones**
-   **Milestone** hasMany **Tasks**

#### Frontend:

-   **Alpine.js**: Reactive UI components
-   **Tailwind CSS**: Styling dan responsive design
-   **Fetch API**: AJAX calls untuk form submissions

### 7. 🎯 Keunggulan Flow Ini

1. **Separation of Concerns**: Roadmap creation terpisah dari goal management
2. **Flexibility**: User bisa buat roadmap manual atau AI-generated
3. **Progressive Enhancement**: Dari roadmap → goals → tracking
4. **Real-time Updates**: Status changes langsung tersimpan
5. **Visual Feedback**: Progress bars, status badges, checkbox states
6. **User Experience**: Flow yang intuitif dari planning ke execution

### 8. 🐛 Troubleshooting

**Jika Roadmap Tidak Muncul di Goals**:

1. Cek apakah import berhasil (ada notifikasi success)
2. Refresh halaman goals
3. Pastikan user sudah login dengan akun yang sama
4. Cek database apakah data tersimpan dengan benar

**Jika Error saat Import**:

1. Validasi format JSON jika menggunakan manual JSON
2. Pastikan semua field required sudah diisi
3. Cek console browser untuk error messages
4. Cek field names sesuai dengan yang diexpect controller

## Kesimpulan

Flow BelajarKu dirancang untuk memberikan experience yang smooth dari planning (roadmap) hingga execution (goals tracking). User dapat membuat rencana pembelajaran dengan berbagai cara, kemudian mengelola dan track progress secara detail di halaman goals.
