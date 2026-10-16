# Chat-app

A real-time chat application built with Laravel, Livewire, Alpine.js, and Laravel Reverb.

---

## Prerequisites

Before installing the project, ensure you have the following installed on your local machine:

* **PHP** >= 8.2
* **Composer**
* **Node.js** & **NPM**
* **MySQL** or any supported database engine

---

## Installation & Setup Guide

Follow these sequential steps to get the application up and running locally:

### 1. Clone the Repository
```sh
git clone https://github.com/CodeWithAtwaa/Chat-app.git
cd Chat-app
```

### 2. Install Dependencies
Install both backend PHP packages and frontend JavaScript dependencies:
```sh
composer install
npm install
```

### 3. Environment Configuration
Copy the example environment file and generate your application encryption key:
```sh
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
* Open your `.env` file and configure your database connection details (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
* Run the migrations and seeders to create tables and dummy data:
```sh
php artisan migrate --seed
```

---

## Development & Execution Workflow

To run the entire ecosystem locally, you need to start the following three services concurrently (in separate terminal windows or using a process manager):

### 1. Application Server
Run the local PHP development server:
```sh
php artisan serve
```

### 2. Frontend Assets Compiling
Run Vite to compile and hot-reload your Tailwind CSS and JavaScript assets:
```sh
npm run dev
```

### 3. Real-Time Broadcast Server (Reverb)
The `.env.example` Reverb settings work seamlessly for local development. Start the WebSocket server using:
```sh
php artisan reverb:start
```

---

## Tech Stack

* **Backend:** Laravel (Eloquent, Broadcasting)
* **Frontend:** Livewire, Alpine.js, Tailwind CSS
* **WebSockets:** Laravel Reverb
