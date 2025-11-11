# No BS URL Shortener

[![CI](https://github.com/santhoshjanan/no-bs-urlshortener/actions/workflows/ci.yml/badge.svg)](https://github.com/santhoshjanan/no-bs-urlshortener/actions/workflows/ci.yml)
[![SAST Security Scanning](https://github.com/santhoshjanan/no-bs-urlshortener/actions/workflows/sast.yml/badge.svg)](https://github.com/santhoshjanan/no-bs-urlshortener/actions/workflows/sast.yml)
[![DAST Security Scanning](https://github.com/santhoshjanan/no-bs-urlshortener/actions/workflows/dast.yml/badge.svg)](https://github.com/santhoshjanan/no-bs-urlshortener/actions/workflows/dast.yml)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4.svg?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20.svg?logo=laravel&logoColor=white)](https://laravel.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A straightforward, no-nonsense URL shortener built with Laravel. Just shorten your URLs - no account required, no tracking beyond basic analytics, no BS!

## Features

- **Simple URL Shortening**: Enter a URL, get a shortened version
- **Privacy-Focused**: No personal data collection, minimal analytics
- **reCAPTCHA Protection**: Spam prevention on web interface
- **API Support**: RESTful API for programmatic access
- **Rate Limiting**: Built-in protection against abuse
- **Caching**: Fast redirects with 14-day caching
- **Analytics**: Basic, privacy-friendly click tracking
- **Custom 404 Pages**: User-friendly error handling

## Tech Stack

- **Backend**: Laravel 11
- **Database**: PostgreSQL
- **Cache**: Redis/Database
- **Queue**: Database-backed queue system
- **Frontend**: Vibe Brutalism CSS Framework (Neo-Brutalist Design), Vanilla JavaScript
- **Build Tool**: Vite
- **Security**: reCAPTCHA, rate limiting, XSS protection

## Design System

This project uses **Vibe Brutalism**, a neo-brutalist CSS framework featuring:

- **Bold Aesthetics**: Thick 3px borders, strong offset shadows, and high contrast
- **Vibrant Colors**: Yellow (#FFD700), Pink (#FF6B9D), Cyan (#00F5FF), and more
- **Typography**: Space Grotesk font with uppercase headings for impact
- **Accessibility**: WCAG 2.1 AA compliant with full keyboard navigation support
- **Interactive Components**: Buttons, cards, forms, toasts, modals, and more
- **Responsive Design**: Mobile-first approach with brutalist aesthetics maintained across devices

The brutalist design perfectly embodies the "No BS" philosophy with its raw, honest, and unapologetic visual style.

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- PostgreSQL
- Redis (optional, but recommended)
- Node.js & NPM

### Setup

1. Clone the repository:
```bash
git clone <repository-url>
cd no-bs-urlshortener
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node dependencies:
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

6. Configure your `.env` file:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=url_shortener_server
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Google reCAPTCHA v3 (required for web form)
RECAPTCHA_SITE_KEY=your_recaptcha_site_key
RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key
RECAPTCHA_SCORE_THRESHOLD=0.5

# Cache (recommended)
CACHE_STORE=redis
```

7. Run migrations:
```bash
php artisan migrate
```

8. Build frontend assets:
```bash
npm run build
```

9. Start the development server:
```bash
composer run dev
```

This will start:
- Laravel development server (port 8000)
- Queue worker
- Log viewer
- Vite dev server

## Usage

### Web Interface

Visit `http://localhost:8000` and enter a URL to shorten.

### API Endpoints

#### Shorten a URL

```bash
POST /api/shorten
Content-Type: application/json

{
  "original_url": "https://example.com/very/long/url"
}
```

Response:
```json
{
  "original_url": "https://example.com/very/long/url",
  "shortened_url": "http://localhost:8000/abc123"
}
```

#### Access Shortened URL

```bash
GET /{shortened_code}
```

Redirects to the original URL.

### Rate Limiting

- Web form: 10 requests per minute
- API: 10 requests per minute

## Security Features

- ✅ Mass assignment protection with explicit `$fillable` fields
- ✅ XSS protection - only HTTP/HTTPS protocols allowed
- ✅ CSRF protection on web forms
- ✅ reCAPTCHA spam prevention
- ✅ Rate limiting on all endpoints
- ✅ URL collision handling with retry logic
- ✅ Input validation on all endpoints
- ✅ Privacy-friendly analytics (no personal data)

## Testing

Run the test suite:
```bash
php artisan test
```

Or with coverage:
```bash
php artisan test --coverage
```

## Database Schema

### `urls` Table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| original_url | string | The original long URL |
| shortened_url | string (unique) | The shortened code |
| clicks | integer | Number of times accessed |
| analytics | json | Privacy-friendly click data |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

## Configuration

### Environment Variables

- `RECAPTCHA_SITE_KEY` - Google reCAPTCHA v3 site key
- `RECAPTCHA_SECRET_KEY` - Google reCAPTCHA v3 secret key
- `RECAPTCHA_SCORE_THRESHOLD` - Minimum acceptable score (default 0.5)
- `CACHE_STORE` - Cache driver (redis, database, etc.)
- `DB_*` - Database configuration

### Caching Strategy

- Shortened URL lookups are cached for 14 days
- Cache key format: `shortened_url:{code}`
- Reduces database load for frequently accessed URLs

### Analytics Privacy

The application tracks minimal, privacy-friendly analytics:
- Click timestamp
- Referrer domain (not full URL)
- No IP addresses
- No user agents
- Last 100 clicks per URL (prevents unbounded growth)

## Deployment

### Production Checklist

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure production database
4. Set up Redis for caching
5. Configure queue workers
6. Set up SSL certificate
7. Add reCAPTCHA keys
8. Optimize application:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Queue Worker

In production, run the queue worker as a supervised process:
```bash
php artisan queue:work --tries=3 --timeout=90
```

Consider using Laravel Horizon for advanced queue management.

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new features
5. Ensure all tests pass
6. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For bugs, feature requests, or questions:
- Email: viscous.buys4y@icloud.com
- Twitter: [@santhoshj](https://x.com/santhoshj)

## Acknowledgments

Built with ❤️ and Laravel in New Jersey!
