# De Gezondheidsmeter

A health tracking application that allows users to monitor daily health metrics, answer health-related questions, and track progress over time.

## Features

- User registration and authentication system
- Daily health check questionnaires
- Health score calculation and visualization
- Weekly health summaries and insights
- BMI calculator and health tracking
- Admin dashboard for monitoring users and application data

## Requirements

- PHP 8.2+ 
- MySQL/MariaDB 10.4+
- Node.js and npm (for Tailwind CSS)
- Web server (Apache recommended) with mod_rewrite enabled

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/de-gezondheidsmeter.git
   cd de-gezondheidsmeter
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Database setup**
   - Create a MySQL database named `gezondheidsmeter`
   - Import the provided SQL file to set up the database schema and sample data:
     ```bash
     mysql -u username -p gezondheidsmeter < gezondheidsmeter.sql
     ```
   - Update the database configuration in `core/Database.php` with your credentials

4. **Configure web server**
   - For Apache, ensure the `.htaccess` file is properly set up for URL rewriting
   - For development, you can use PHP's built-in server:
     ```bash
     php -S localhost:8000
     ```

5. **Build CSS**
   ```bash
   npm run build:css
   # or for development with watch mode:
   npx tailwindcss -i ./public/input.css -o ./public/output.css --watch
   ```

## Usage

1. **User Registration and Login**
   - Navigate to `/auth/register` to create a new account
   - Use `/auth/login` to sign in with existing credentials

2. **Onboarding**
   - New users will be prompted to complete the onboarding process
   - Enter personal details like age, gender, weight, and height for BMI calculation

3. **Daily Health Check**
   - Complete daily health questionnaires to track your well-being
   - Answer questions about sleep, stress, hydration, exercise, mood, and more

4. **Dashboard**
   - View your health scores and metrics visualizations
   - Access your health history and weekly summaries
   - Monitor your progress over time

5. **Admin Access**
   - Admin users can access `/admin/dashboard` to monitor user statistics
   - Manage users and view application-wide health metrics

## Database Structure

The application uses four main tables:
- `users`: User accounts and profile information
- `daily_questions`: Configurable health questions presented to users
- `daily_health_checks`: User responses to daily health questionnaires
- `weekly_summaries`: Aggregated weekly health data and insights

## Development

- CSS: Tailwind is used for styling. Edit `public/input.css` and rebuild with npm
- Routes: Configure routes in `index.php`
- Controllers: Create or modify controllers in the `/controllers` directory
- Views: Update templates in the `/views` directory

## License

[MIT License](LICENSE)