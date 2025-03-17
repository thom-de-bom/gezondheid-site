# DE GEZONDHEIDSMETER PROJECT GUIDELINES

## Commands
- Build CSS: `npm run build:css` (Tailwind compilation)
- Development: `php -S localhost:8000` (PHP built-in server)
- Watch CSS: `npx tailwindcss -i ./public/input.css -o ./public/output.css --watch`

## Code Style
- **Architecture**: MVC pattern (Model-View-Controller)
- **Classes**: PascalCase (e.g., AuthController, User)
- **Methods/Variables**: camelCase (e.g., getUserData, dailyScore)
- **Files**: Named after primary class/purpose
- **Database**: PDO with prepared statements for security
- **Error Handling**: Try/catch blocks with error_log() for system errors

## Project Organization
- `/controllers`: Request handlers following RESTful patterns
- `/models`: Data models for database interaction
- `/views`: Templates (primarily PHP with HTML/Tailwind)
- `/services`: Business logic separated into service classes
- `/core`: Framework components (Router, Database, base Controller)

## Notes
- Dutch language used for user-facing content and some variables
- Authentication required for protected routes
- Service injection pattern in controllers