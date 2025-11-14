# Bus Booking System

## Project Overview
The **Bus Booking System** is a web-based application that allows users to browse bus routes, book tickets, and manage reservations. Admins can manage buses, routes, and bookings through a separate dashboard.

## Demo
## Demo

Try it here [Live Demo](https://jyotsna-bus-booking.rf.gd/public/)

![Bus Booking](https://github.com/NJyotsna/bus-booking-system/blob/86e0c329f9ed17abcc89787105ac5ab2d076bffa/screenshots/busbooking.png)


## Features

### User Features
- Browse available buses.
- Book tickets online.
- View the ticket.
- View booking history.
- Cancel booked tickets.

### Admin Features
- Admin has seperate access to `admin.html`.
- Manage buses, routes, and bookings via the admin dashboard.

## Technologies Used
- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP  
- **Database:** MySQL  
- **Hosting:** InfinityFree (`.rf.gd` domain)

## 📸*Screenshots*
<img width="1919" height="887" alt="image" src="https://github.dev/NJyotsna/bus-booking-system/blob/main/screenshots/bookings.png" />

## Local Setup Using XAMPP

1. **Install XAMPP** and run it.
2. Start **Apache** and **MySQL** from the XAMPP control panel.
3. Place your project folder inside the `htdocs` directory:
   ``C:\xampp\htdocs\bus-booking``
   
4. Open your browser and navigate to:
   ``http://localhost/bus-booking/public/index.html``

5. Open **phpMyAdmin**:
   ``http://localhost/phpmyadmin``

6. Create a new database (e.g., `bus_booking`).
7. Import the `db.sql` file into the database.
8. Update your database credentials in `api.php` if necessary.

> Now, user actions like bookings and admin actions like adding buses will be reflected in your local database.

## Usage
1. Open `index.html` in your browser..  
2. Register and login with your credentials and you will be redirected to `home.html` (user view).  
3. Users can browse buses, select seats, complete bookings, view tickets and cancel tickets if needed.
4. Admin can have seperate access to (admin dashboard) `admin.html` (admin view).

## Deployment
- Upload your project folder to a PHP + MySQL hosting platform (like InfinityFree).  
- Update database credentials as required.  
- Ensure `index.html`, `home.html`, and `admin.html` are accessible through URLs.

## Live Project Link
[Bus Booking System](https://jyotsna-bus-booking.rf.gd/public/index.html)

## Author
**Jyotsna Nadisetti**  
- GitHub: [https://github.com/NJyotsna](https://github.com/NJyotsna)




   


   
      

