
# InspireFrame Project Documentation

## Overview
InspireFrame is a motivational quote web application that displays a new inspiring image and quote every day. Users can upload quotes with images, browse submissions, and save favorites.

## Features
- Daily rotating quote based on calendar day
- User uploads quotes with images (AJAX upload)
- Admin panel for approving uploads
- User registration and login (not included in this package)
- Save favorite quotes (not included in this package)

## Setup Instructions
1. Install XAMPP and start Apache & MySQL servers.
2. Create a MySQL database named 'inspireframe'.
3. Create the following tables:

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote_text TEXT NOT NULL,
    author VARCHAR(100),
    image_path VARCHAR(255),
    approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE favorites (
    user_id INT,
    quote_id INT,
    PRIMARY KEY (user_id, quote_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (quote_id) REFERENCES quotes(id)
);

4. Place the InspireFrame folder in your XAMPP 'htdocs' directory.
5. Modify 'backend/db.php' if your MySQL credentials differ.
6. Access the site at http://localhost/InspireFrame/frontend/index.html.
7. Admin panel available at http://localhost/InspireFrame/backend/admin_panel.php to approve uploads.

## How to Use
- Upload new quotes via the upload page.
- Approved quotes appear daily on the homepage.
- Admin can approve or reject uploads through the admin panel.

## Notes
- Image uploads are saved in the uploads directory.
- Admin authentication is currently a placeholder; implement secure login for admin access.
