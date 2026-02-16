# 📚 Digital Library Management System

A comprehensive digital library platform built with Laravel, Filament PHP, and Livewire. This system allows users to purchase digital resources, subscribe to membership plans, and manage their downloads seamlessly.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4E56A2?style=for-the-badge&logo=livewire&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-FFAA00?style=for-the-badge&logo=filament&logoColor=black)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

## 📋 Table of Contents
- [Features](#-features)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [M-PESA Configuration](#-m-pesa-configuration)
- [Project Structure](#-project-structure)
- [Database Schema](#-database-schema)
- [Key Features Explained](#-key-features-explained)
- [Configuration](#-configuration)
- [Running Tests](#-running-tests)
- [Dependencies](#-dependencies)
- [Security](#-security)
- [Deployment](#-deployment)
- [Contributing](#-contributing)
- [License](#-license)
- [Authors](#-authors)
- [Acknowledgments](#-acknowledgments)
- [Contact](#-contact)
- [Troubleshooting](#-troubleshooting)
- [Roadmap](#-roadmap)

## 🚀 Features

### For Users
- **User Dashboard**: Overview of orders, subscriptions, and downloads
- **Resource Library**: Browse and search digital resources
- **Shopping Cart**: Purchase individual resources
- **Membership Plans**: Subscribe to monthly, quarterly, yearly, or lifetime plans
- **M-PESA Integration**: Secure payments via M-PESA STK push
- **Download Management**: Track and re-download purchased resources
- **Subscription Management**: View and manage active subscriptions

### For Administrators (Filament Admin Panel)
- **Order Management**: View, edit, and process orders
- **Resource Management**: CRUD operations for digital resources
- **Membership Package Management**: Create and manage membership plans
- **User Management**: View and manage users
- **Download Analytics**: Track download statistics
- **Payment Processing**: Manual payment verification and status updates

## 📋 Prerequisites

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & NPM (for frontend assets)
- M-PESA Developer Account (for payment integration)

## 🛠 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/digital-library.git
   cd digital-library
   ```

1. **Install PHP dependencies

    ```bash
    composer install
    ```