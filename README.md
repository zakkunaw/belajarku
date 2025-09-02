# BelajarKu - AI-Powered Learning Management System

![Laravel](https://img.shields.io/badge/Laravel-11.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

**BelajarKu** adalah aplikasi Learning Management System (LMS) yang didukung oleh AI untuk membantu pengguna mengelola pembelajaran mereka dengan lebih efektif. Aplikasi ini menyediakan fitur roadmap pembelajaran yang dipersonalisasi, pelacakan tujuan, dan integrasi AI untuk rekomendasi belajar.

## 🚀 Fitur Utama

-   **🎯 Goal Management**: Kelola tujuan pembelajaran dengan milestone dan tasks
-   **🗺️ AI Roadmap**: Dapatkan roadmap pembelajaran yang dipersonalisasi menggunakan AI (Gemini)
-   **📚 Study Sessions**: Lacak sesi belajar dengan mood tracking
-   **📝 Journal**: Catat refleksi dan progress pembelajaran
-   **📊 Progress Tracking**: Visualisasi kemajuan learning dengan grafik dan statistik
-   **🔐 User Authentication**: Sistem registrasi dan login yang aman
-   **📱 Responsive Design**: Antarmuka yang responsif menggunakan Tailwind CSS

## 🛠️ Tech Stack

-   **Backend**: Laravel 11.x
-   **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
-   **Database**: SQLite (development) / MySQL (production)
-   **AI Integration**: Google Gemini API
-   **Authentication**: Laravel Breeze
-   **Build Tools**: Vite

## 📦 Instalasi

### Prerequisites

-   PHP 8.2 atau lebih tinggi
-   Composer
-   Node.js & NPM
-   SQLite atau MySQL

### Langkah Instalasi

1. **Clone repository**

    ```bash
    git clone https://github.com/yourusername/belajarku.git
    cd belajarku
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Setup environment**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Konfigurasi database**

    - Edit file `.env` dan sesuaikan pengaturan database
    - Untuk development, sudah dikonfigurasi menggunakan SQLite

5. **Jalankan migrasi**

    ```bash
    php artisan migrate
    ```

6. **Build assets**

    ```bash
    npm run dev
    ```

7. **Jalankan server**
    ```bash
    php artisan serve
    ```

## 🔧 Konfigurasi AI

Untuk menggunakan fitur AI Roadmap, tambahkan API key Gemini di file `.env`:

```env
GEMINI_API_KEY=your_gemini_api_key_here
```

## 📁 Struktur Database

### Models

-   **User**: Data pengguna
-   **Goal**: Tujuan pembelajaran
-   **Milestone**: Pencapaian dalam goal
-   **Task**: Tugas-tugas spesifik
-   **StudySession**: Sesi belajar dengan durasi
-   **Mood**: Tracking mood selama belajar
-   **Journal**: Catatan refleksi

## 🎮 Cara Penggunaan

1. **Registrasi/Login** ke aplikasi
2. **Buat Goal** baru untuk pembelajaran
3. **Generate Roadmap** menggunakan AI
4. **Lacak Progress** melalui tasks dan milestones
5. **Record Study Sessions** dengan mood tracking
6. **Tulis Journal** untuk refleksi

## 🧪 Testing

Jalankan unit tests:

```bash
php artisan test
```

## 📝 API Endpoints

### AI Endpoints

-   `POST /api/ai/roadmap` - Generate learning roadmap
-   `POST /api/ai/assistant` - AI assistant chat

### Goal Management

-   `GET /api/goals` - List all goals
-   `POST /api/goals` - Create new goal
-   `PUT /api/goals/{id}` - Update goal
-   `DELETE /api/goals/{id}` - Delete goal

## 🤝 Contributing

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 License

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

## 👥 Authors

-   **Your Name** - _Initial work_ - [YourGitHub](https://github.com/yourusername)

## 🙏 Acknowledgments

-   Laravel framework untuk foundation yang solid
-   Google Gemini untuk AI capabilities
-   Tailwind CSS untuk styling yang elegant
-   Komunitas open source yang supportif
