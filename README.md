# Lincotic

A clean, lightweight, open-source bookmark manager for organizing and accessing your favorite links.

Repository: [github.com/tomgineer/lincotic](https://github.com/tomgineer/lincotic)

## Tech stack

- [CodeIgniter 4](https://codeigniter.com/) with [Shield](https://github.com/codeigniter4/shield)
- [Tailwind CSS](https://tailwindcss.com/) with [daisyUI](https://daisyui.com/)
- [esbuild](https://esbuild.github.io/)
- MySQL

## Requirements

- PHP 8.5 or newer with `intl`, `mbstring`, and `mysqli`
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) and npm
- MySQL or MariaDB

## Installation

Clone the repository and install the PHP and frontend dependencies:

```bash
git clone https://github.com/tomgineer/lincotic.git
cd lincotic
composer install
npm install
```

Copy `env` to `.env`, then configure at least the application URL and database connection:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.database = lincotic
database.default.username = root
database.default.password =
```

Create the database, run all application and package migrations, and build the frontend assets:

```bash
php spark migrate --all
npm run build
```

Start the local development server:

```bash
php spark serve
```

The application will be available at `http://localhost:8080/` by default.

## Frontend development

Run the CSS and JavaScript watchers while developing:

```bash
npm run dev
```

Create minified production assets with:

```bash
npm run build
```

## Production

Point the web server document root to the `public/` directory, set `CI_ENVIRONMENT` to `production`, and configure the production URL and database credentials in `.env`.

## License

This project is released under the [MIT License](LICENSE).
