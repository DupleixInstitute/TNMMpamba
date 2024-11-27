# TNMMpamba Client Management System

A modern web application for managing client information, built with Laravel 9 and Vue.js 3.

## Features

- Simple client management interface
- Streamlined client creation with essential fields:
  - Customer ID
  - Name
  - Phone Number (with validation)
- Modern, responsive UI with Tailwind CSS
- Clean and intuitive data tables
- Easy-to-use edit functionality

## Tech Stack

- **Backend:** Laravel 9.x
- **Frontend:** Vue.js 3.x with Inertia.js
- **CSS Framework:** Tailwind CSS
- **Development Environment:** XAMPP

## Prerequisites

- PHP >= 8.0
- Composer
- Node.js & NPM
- XAMPP or similar local development environment
- Git

## Installation

1. Clone the repository:
```bash
git clone [your-repository-url]
cd TNMMpamba
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in the .env file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tnmmpamba
DB_USERNAME=root
DB_PASSWORD=
```

7. Run migrations:
```bash
php artisan migrate
```

8. Build assets:
```bash
npm run dev
```

## Usage

1. Start your local development server:
```bash
php artisan serve
```

2. Access the application at `http://localhost:8000`

## Development

For hot-reloading during development:
```bash
npm run watch
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
