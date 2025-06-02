To get your InspireFrame application up and running, you'll need a local server environment and to set up your database. Here are the step-by-step instructions:

### Step 1: Set Up a Local Server Environment (XAMPP/WAMP/MAMP)

PHP applications require a web server (like Apache) and a database server (like MySQL/MariaDB). The easiest way to get these is by using a pre-packaged solution:

* **For Windows:** Download and install [XAMPP](https://www.apachefriends.org/index.html) or [WampServer](https://www.wampserver.com/en/).
* **For macOS:** Download and install [MAMP](https://www.mamp.info/en/downloads/) or XAMPP.
* **For Linux:** Download and install [XAMPP](https://www.apachefriends.org/index.html).

Once installed, start the Apache and MySQL (or MariaDB) services from the control panel of your chosen software.

### Step 2: Place Your Project Files

1.  Locate the web server's document root directory:
    * **XAMPP:** `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (macOS)
    * **WAMP:** `C:\wamp64\www\`
    * **MAMP:** `/Applications/MAMP/htdocs/`
2.  Inside this `htdocs` (or `www`) directory, create a new folder for your project, for example, `inspireframe`.
3.  Inside the `inspireframe` folder, place your `frontend`, `backend`, and `uploads` directories as they were structured in our conversation. Your structure should look like this:

    ```
    inspireframe/
    ├── frontend/
    │   ├── index.html
    │   ├── login.html
    │   ├── profile.html
    │   ├── register.html
    │   ├── style.css
    │   └── upload.html
    ├── backend/
    │   ├── admin_panel.php
    │   ├── approve_quote.php
    │   ├── check_login.php
    │   ├── db.php             <-- Your database connection file
    │   ├── delete_quote.php
    │   ├── get_daily_quote.php
    │   ├── get_next_quote.php
    │   ├── get_random_quote.php
    │   ├── get_user_data.php
    │   ├── login.php
    │   ├── logout.php
    │   ├── register.php
    │   ├── reject_quote.php
    │   └── upload_quote.php
    └── uploads/             <-- This folder will store uploaded images
    ```

### Step 3: Create the Database

1.  Open your web browser and go to `http://localhost/phpmyadmin` (or `http://localhost:8888/phpmyadmin` if using MAMP with default port).
2.  Log in (default username is `root`, password is often empty or `root` for MAMP).
3.  In the left sidebar, click on **New** or **Databases**.
4.  Enter the database name `inspireframe` (this is what is defined in your `db.php` file) and click **Create**.

### Step 4: Create Database Tables (SQL Schema)

After creating the `inspireframe` database, click on its name in the left sidebar to select it. Then, go to the **SQL** tab and paste the following SQL commands. Execute them one by one, or all at once.

#### 1. `users` Table:

```sql
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(255) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `registered_on` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 2. `quotes` Table:

```sql
CREATE TABLE `quotes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `quote_text` TEXT NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `image_path` VARCHAR(255) NULL, -- Stores the relative path to the image
  `user_id` INT(11) NOT NULL,     -- User who uploaded the quote
  `approval_status` VARCHAR(50) DEFAULT 'pending', -- 'pending', 'approved', 'rejected'
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
```

#### 3. `favorites` Table:

```sql
CREATE TABLE `favorites` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT(11) NOT NULL,
  `quote_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `user_quote` (`user_id`, `quote_id`), -- Prevents duplicate favorites
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`quote_id`) REFERENCES `quotes`(`id`) ON DELETE CASCADE
);
```

### Step 5: Configure `db.php`

Ensure your `backend/db.php` file has the correct database connection details. Based on our conversation, it should already be configured like this:

```php
<?php
$servername = "localhost";
$username = "root"; // Default username for XAMPP/WAMP/MAMP
$password = "";     // Default password for XAMPP/WAMP is empty, for MAMP it's often 'root'
$dbname = "inspireframe"; // The database name you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
```
**Important:** If you set a password for your MySQL `root` user, make sure to update the `$password` variable in `db.php` accordingly. For MAMP users, it's often `root` by default.

### Step 6: Run the Application

1.  Make sure your Apache and MySQL services are running from your XAMPP/WAMP/MAMP control panel.
2.  Open your web browser and navigate to:
    `http://localhost/inspireframe/frontend/index.html`

You should now see your InspireFrame homepage! You can then register a new user, log in, upload quotes, and approve them through the admin panel (`http://localhost/inspireframe/backend/admin_panel.php`).
