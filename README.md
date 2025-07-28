# Recipe-Finder-App

http://localhost/rfs/



Overview:-
The Recipe Finder System is a user-friendly web application designed to help users discover recipes based on ingredients they have at home. The system aims to reduce food waste and simplify cooking by allowing users to search for recipes, save favorites, manage their profiles, and contribute their own recipes. It includes an admin panel for managing users, recipes, and password reset requests. This project was developed as part of the Project II course (ICT-3204) at SMUCT for Winter-2025.
Features

The implemented features include:-

User Authentication:
Sign Up: Users can create accounts with first name, last name, email, password, and gender (add-new.php).
Login/Logout: Secure login with email and password, using session-based authentication (login.php, logout.php).
Password Management: Users can update their password (update-password.php), request a password reset (reset-password-request.php), and reset it via a token-based system (reset-password.php). Admins can also reset user passwords (admin-reset-password.php).


Recipe Management:
Add/Edit Recipes: Users can add or edit recipes with title, cuisine, prep time, instructions, and ingredients (add-recipe.php, edit-recipe.php).
Delete Recipes: Admins can delete recipes (delete-recipe.php).
View Recipes: Detailed recipe views with ingredients, instructions, and prep time (recipe-details.php).
Save/Remove Recipes: Users can save recipes to their profile (save-recipe.php) and remove them from their saved list (remove-saved-recipe.php).


Ingredient-Based Search:
Users can search for recipes by entering a single ingredient, with results displayed in a table (search.php).
Search history is stored and displayed on the dashboard (dashboard.php).


Personalized Dashboard:
Displays saved recipes and recent search history for the logged-in user (dashboard.php).


Admin Panel:
Admins can manage users (edit/delete via edit.php, delete.php), recipes (edit/delete via edit-recipe.php, delete-recipe.php), and password reset requests (admin.php, admin-reset-password.php).
Fixed "Uploaded By" column to display correct user names or "Deleted User" for invalid user IDs.
Formatted recipe instructions as ordered lists with truncation for better readability.


Dynamic Ingredient Matching:
Recipes are matched based on ingredients stored in a relational structure (recipe_ingredients and ingredients tables).



Technologies Used:-
The implemented tech stack differs from the proposed stack (React.js, Node.js, MongoDB) due to the use of PHP and MySQL for simplicity and compatibility with XAMPP.

Frontend:
HTML5 & CSS3: For structure and styling.
Bootstrap 5.2.3: For responsive design and components (tables, forms, alerts).
Font Awesome 6.4.0: For icons (e.g., eye, trash, pen-to-square).
Poppins Font: For consistent typography via Google Fonts.
JavaScript: For password toggle functionality and Bootstrap tooltips.


Backend:
PHP 8.2.12: For server-side logic and API-like functionality.
Session-Based Authentication: For secure user sessions.
bcrypt: For password hashing (via PHP’s password_hash).


Database:
MySQL (MariaDB 10.4.32): For storing user, recipe, and related data.
phpMyAdmin: For database management within XAMPP.


Development Environment:
XAMPP: For local development with Apache and MySQL.


Version Control:
Git & GitHub: For version control and collaboration (assumed based on proposal).



Database Schema:-
The rfs database consists of the following tables:

users (id, first_name, last_name, email, password, gender, is_admin): Stores user information with hashed passwords.
recipes (id, title, instructions, prep_time, cuisine, created_by, user_id): Stores recipe details, with user_id linking to the creator.
ingredients (id, name): Stores unique ingredient names.
recipe_ingredients (recipe_id, ingredient_id, quantity, amount, unit): Links recipes to ingredients with amounts and units.
saved_recipes (user_id, recipe_id, is_favorite): Stores user-saved recipes with a favorite flag.
search_history (id, user_id, ingredients, search_time): Logs user searches.
password_resets (id, email, status, new_password, request_time): Manages password reset requests.


Set Up the Database:

Open phpMyAdmin (http://localhost/phpmyadmin).
Create a database named rfs.
Import the rfs.sql file to create tables and populate sample data.


Deploy the Application:

Copy all PHP files (index.php, dashboard.php, etc.) and styles.css to the XAMPP htdocs directory (e.g., C:\xampp\htdocs\recipe-finder).
Ensure db_conn.php is configured with the correct database credentials (default: root, no password).


Access the Application:

Open a browser and navigate to http://localhost/recipe-finder/index.php.
Sign up or log in with a sample user (e.g., ishratjahanfabiha123@gmail.com).


Test Features:

Sign up, log in, search recipes, save/remove recipes, and access the admin panel (for admin users).



Work Done:-
The following work was completed for the Recipe Finder System:

Core Functionality:
Implemented user authentication (sign up, login, logout, password updates, and resets).
Developed recipe management (add, edit, delete, view, save, and remove recipes).
Added ingredient-based search with search history tracking.
Created a personalized dashboard for saved recipes and search history.
Built an admin panel for managing users, recipes, and password reset requests.


Fixes and Improvements:
Admin Panel (admin.php):
Fixed the "Uploaded By" column to display correct user names (e.g., "Neko Mimi" for user_id=3) or "Deleted User" for invalid user_id values (e.g., 1, 2).
Reformatted recipe instructions as ordered lists with truncation (showing up to 2 steps) and a "View more" link to recipe-details.php.


Remove Saved Recipes:
Added functionality to allow users to remove saved recipes from their dashboard (dashboard.php).
Created remove-saved-recipe.php to securely delete entries from the saved_recipes table using prepared statements.
Added a "Remove" button with a trash icon and tooltip, consistent with the app’s UI.
Displayed success/error messages (e.g., "Recipe removed successfully") via Bootstrap alerts.




Security:
Used prepared statements in most database queries (e.g., search.php, add-recipe.php, remove-saved-recipe.php).
Applied htmlspecialchars for safe output to prevent XSS.
Implemented password hashing with bcrypt for secure storage.


UI/UX:
Used Bootstrap 5 for responsive design, Font Awesome for icons, and custom CSS (styles.css) for styling.
Added password visibility toggles and tooltips for better usability.
Ensured consistent formatting across pages (e.g., tables, forms, alerts).



Future Improvements:-

Security Enhancements:
Address SQL injection vulnerabilities in files like recipe-details.php, save-recipe.php, and update-password.php by using prepared statements.
Add CSRF tokens to forms and links (e.g., for removing saved recipes).
Secure db_conn.php with a non-root user and password for production.


Feature Enhancements:
Implement multi-ingredient search in search.php (e.g., support comma-separated ingredients with AND/OR logic).
Add a "Favorite" toggle for saved recipes using the is_favorite column in saved_recipes.
Allow users to contribute recipes publicly (community contributions from the proposal).


UI Improvements:
Add confirmation prompts or modals for delete actions (e.g., removing saved recipes).
Enhance instruction input in add-recipe.php and edit-recipe.php to enforce numbered steps.


Data Visualization:
Add charts (e.g., recipes by cuisine) using Chart.js, leveraging the recipes table data (e.g., 4 Italian, 2 American recipes).


Deployment:
Deploy the application to a hosting service (e.g., Heroku, DigitalOcean) with a production-ready database.



Contributors:-

Ishrat Jahan Fabiha (ID: 223071125)

Course Information:-

Course Title: Project II
Course Code: ICT-3204
Semester: Winter-2025
Institution: Department of CSE & CSIT, SMUCT
Submitted To: Ishrat Jahan
