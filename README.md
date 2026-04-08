# Eco-Track 🌱

A carbon footprint tracking app for students in the Philippines.

## Features

- 📊 *Carbon Calculator* - Track daily emissions from transport, food, and gadgets
- 🏆 *Classroom Leaderboard* - Compete with classmates to be the greenest
- 🎖️ *Achievements* - Earn badges for eco-friendly habits
- 🤖 *AI Predictions* - Get personalized forecasts and tips
- 💬 *Eco Chatbot* - Ask questions about reducing your footprint
- ⚙️ *Admin Panel* - Manage users, factors, and announcements

## Tech Stack

- *Backend:* Laravel 13, PHP 8.4
- *Database:* Firebase Firestore
- *Frontend:* Livewire, Tailwind CSS, Alpine.js
- *Authentication:* Laravel Fortify

## Installation

```bash
git clone https://github.com/jonathan9347/eco-track.git
cd eco-track
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve