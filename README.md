# E-Commerce Website

An e-commerce platform developed using **HTML**, **CSS**, **PHP**, **JavaScript**, **Bootstrap**, and **Tailwind CSS**. This project also integrates the **M-Pesa API** to facilitate secure and efficient mobile payments for customers.

---

## Features

### User Features:
- **Responsive Design**: Built with a combination of Bootstrap and Tailwind CSS to provide a seamless experience across devices.
- **Product Browsing**: Users can view products with categories, descriptions, and images.
- **Shopping Cart**: Add, update, or remove items from the cart.
- **Order Management**: Users can place and view their order history.
- **Secure Payments**: Integrated with the **M-Pesa API** for real-time mobile payment processing.

### Admin Features:
- **Product Management**: Add, edit, and delete products.
- **Order Tracking**: Manage and view customer orders.
- **Payment Monitoring**: Confirm and track successful payments via M-Pesa.

---

## Technologies Used

### Frontend:
- **HTML**: For building the structure of the pages.
- **CSS**: Tailored styles with custom **CSS**, **Bootstrap**, and some **Tailwind CSS** classes.
- **JavaScript**: For interactive elements, validation, and dynamic content updates.

### Backend:
- **PHP**: Server-side logic to handle user authentication, order processing, and integration with the M-Pesa API.
- **MySQL**: Database for storing user information, orders, and product details.

### API Integration:
- **M-Pesa API**: For processing mobile payments, including:
  - Generating STK Push requests.
  - Receiving payment confirmation callbacks.

---

## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/prollymanu/ecommerce-project.git
   cd ecommerce-project
   Set Up the Database
2. **Setting up the data base**
Import the provided SQL file (database.sql) into your MySQL database.
Update the database credentials in connection.php:

3. **Configure the M-Pesa API**

Update the M-Pesa API credentials in payment.php and callback.php:
$businessShortCode = "YOUR_BUSINESS_SHORT_CODE";
$passKey = "YOUR_PASSKEY";
$consumerKey = "YOUR_CONSUMER_KEY";
$consumerSecret = "YOUR_CONSUMER_SECRET";
$callbackUrl = "YOUR_CALLBACK_URL";

4. **Starting up the server**
 Use a local server like XAMPP or WAMP:
Place the project folder in the htdocs directory.
Start Apache and MySQL services.
Visit http://localhost/ecommerce-project in your browser.

**Future Enhancements**
Add support for additional payment gateways (e.g., PayPal, Stripe).
Implement advanced search and filtering options for products.
Integrate user reviews and ratings for products.
Add an admin dashboard with analytics and reports.

**Contributions**
Contributions are welcome! If you'd like to contribute, please fork the repository and create a pull request.

**License**
This project is open-source and available under the MIT License.

**Contact**
For questions or feedback, please contact:

Email: pemmanuel218@gmail.com
GitHub: prollymanu
