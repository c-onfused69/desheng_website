# Desh Engineering - Digital Product Ecommerce Website

A complete, responsive digital product selling ecommerce website built with PHP, MySQL, and Bootstrap. Features both user-facing storefront and admin dashboard with modern UI/UX design.

## 🚀 Features

### User Features
- **Responsive Design**: Mobile-first design with desktop optimization
- **Dark/Light Mode**: Toggle between themes
- **User Authentication**: Login, signup, forgot password, email verification
- **Product Browsing**: Advanced filtering, search, categories
- **Shopping Cart**: Add/remove items, quantity management
- **Secure Checkout**: Multiple payment gateways (Stripe, PayPal, Razorpay)
- **Digital Downloads**: Secure file delivery with download limits
- **Order Management**: Order history, tracking, invoices
- **User Profile**: Account management, address book
- **Wishlist**: Save favorite products
- **Support System**: Contact forms, FAQ, ticket system

### Admin Features
- **Dashboard**: Sales analytics, reports, charts
- **Product Management**: CRUD operations, image gallery, categories
- **Order Management**: Process orders, refunds, status updates
- **User Management**: View users, manage accounts
- **Content Management**: FAQs, pages, settings
- **Coupon System**: Create discount codes, promotions
- **Payment Settings**: Configure payment gateways
- **Email Templates**: Customizable notifications

### Technical Features
- **Security**: SQL injection protection, XSS prevention, CSRF tokens
- **SEO Optimized**: Clean URLs, meta tags, structured data
- **Performance**: Optimized queries, caching, image optimization
- **Mobile App Experience**: Bottom navigation, native-style UI
- **API Ready**: RESTful endpoints for future mobile apps
- **Multi-currency**: Support for different currencies
- **Tax Management**: Configurable tax rates
- **Email System**: SMTP integration for notifications

## 🛠️ Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependencies)

### Step 1: Download and Setup
1. Clone or download the project to your web server directory
2. If using XAMPP, place in `htdocs/desh-eshop`
3. Ensure proper file permissions (755 for directories, 644 for files)

### Step 2: Database Setup
1. Create a new MySQL database named `desh_eshop`
2. Import the database schema:
   ```sql
   mysql -u root -p desh_eshop < database/schema.sql
   ```
3. Or manually run the SQL commands from `database/schema.sql`

### Step 3: Configuration
1. Update database credentials in `config/database.php`:
   ```php
   private $host = 'localhost';
   private $db_name = 'desh_eshop';
   private $username = 'root';
   private $password = 'your_password';
   ```

2. Update site settings in `config/config.php`:
   ```php
   define('SITE_URL', 'http://localhost/desh-eshop');
   define('SITE_NAME', 'Your Site Name');
   define('SITE_EMAIL', 'your-email@domain.com');
   ```

### Step 4: File Permissions
Create upload directories and set permissions:
```bash
mkdir uploads/products uploads/users uploads/general
chmod 755 uploads uploads/products uploads/users uploads/general
```

### Step 5: Admin Access
Default admin credentials:
- Email: `admin@deshengineering.com`
- Password: `password` (change immediately after first login)

### Step 6: Payment Gateway Setup
Configure payment gateways in the admin panel:

**Stripe:**
1. Get API keys from Stripe Dashboard
2. Add to Settings > Payment Gateways

**PayPal:**
1. Create PayPal app and get Client ID/Secret
2. Configure in admin settings

**Razorpay:**
1. Get API keys from Razorpay Dashboard
2. Configure in admin settings

## 📱 Mobile Experience

The website provides a native app-like experience on mobile devices:

- **App Bar**: Clean header with logo and menu
- **Bottom Navigation**: Quick access to Home, Products, Cart, Profile
- **Touch-Friendly**: Large buttons and touch targets
- **Swipe Gestures**: Natural mobile interactions
- **Offline-Ready**: Progressive Web App features

## 🎨 Customization

### Themes
- Modify CSS variables in `assets/css/style.css`
- Dark/light mode toggle included
- Responsive breakpoints for all devices

### Colors
Update the primary color scheme:
```css
:root {
  --primary-color: #6f42c1; /* Change this */
  --secondary-color: #6c757d;
}
```

### Layout
- Header: `includes/header.php`
- Footer: `includes/footer.php`
- Main styles: `assets/css/style.css`
- JavaScript: `assets/js/app.js`

## 🔧 Configuration Options

### Email Settings
Configure SMTP in admin panel or `config/config.php`:
```php
$email_settings = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'your-email@gmail.com',
    'smtp_password' => 'your-app-password',
];
```

### Payment Settings
- Default currency
- Tax rates
- Payment gateway preferences
- Checkout options

### Security Settings
- Session timeout
- Password requirements
- File upload restrictions
- Rate limiting

## 📊 Database Schema

Key tables:
- `users` - Customer accounts
- `admin_users` - Admin accounts
- `products` - Product catalog
- `categories` - Product categories
- `orders` - Order information
- `order_items` - Order line items
- `cart` - Shopping cart items
- `downloads` - Download tracking
- `coupons` - Discount codes
- `settings` - Site configuration

## 🔐 Security Features

- **SQL Injection Protection**: Prepared statements
- **XSS Prevention**: Input sanitization
- **CSRF Protection**: Token validation
- **File Upload Security**: Type and size validation
- **Session Security**: Secure session handling
- **Password Hashing**: bcrypt encryption
- **Rate Limiting**: Brute force protection

## 🚀 Performance Optimization

- **Database Indexing**: Optimized queries
- **Image Optimization**: Automatic resizing
- **Caching**: File and query caching
- **Minification**: CSS/JS compression
- **CDN Ready**: External asset support
- **Lazy Loading**: Images and content

## 📱 API Endpoints

RESTful API for future mobile app development:

- `GET /api/products` - Product listing
- `POST /api/cart/add` - Add to cart
- `GET /api/cart/count` - Cart item count
- `POST /api/orders` - Create order
- `GET /api/user/profile` - User profile

## 🔄 Updates and Maintenance

### Regular Tasks
1. **Database Backup**: Regular automated backups
2. **Security Updates**: Keep PHP and dependencies updated
3. **Log Monitoring**: Check error logs regularly
4. **Performance Monitoring**: Monitor site speed and uptime

### Version Updates
1. Backup database and files
2. Test updates on staging environment
3. Deploy during low-traffic periods
4. Monitor for issues post-deployment

## 🆘 Troubleshooting

### Common Issues

**Database Connection Error:**
- Check database credentials
- Verify MySQL service is running
- Check database exists

**File Upload Issues:**
- Verify upload directory permissions
- Check PHP upload limits
- Ensure disk space available

**Email Not Sending:**
- Verify SMTP settings
- Check firewall/security settings
- Test with different email provider

**Payment Gateway Issues:**
- Verify API credentials
- Check webhook URLs
- Review gateway documentation

## 📞 Support

For technical support or customization requests:
- Email: support@deshengineering.com
- Documentation: Check inline code comments
- Issues: Create detailed bug reports

## 📄 License

This project is proprietary software developed by Desh Engineering. All rights reserved.

## 🤝 Contributing

This is a commercial project. For feature requests or bug reports, please contact the development team.

---

**Built with ❤️ by Desh Engineering**

*Creating digital solutions that drive business growth*
