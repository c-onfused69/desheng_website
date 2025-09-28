# 🏗️ Desh Engineering E-commerce - Professional Project Structure

This document provides a comprehensive, professional file organization structure for the Desh Engineering e-commerce website, detailing where each file should be placed and its purpose.

## 📁 Complete Project Structure

```
desh-eshop/                                 # 🏠 ROOT DIRECTORY
│
├── 📄 .htaccess                            # 🔧 Apache URL rewriting & security rules
├── 📄 README.md                            # 📖 Project documentation
├── 📄 PROJECT_STRUCTURE.md                 # 📋 This structure guide
│
├── 🌐 PUBLIC PAGES (Root Level)            # User-facing pages
│   ├── 📄 index.php                       # 🏠 Homepage/Landing page
│   │
│   ├── 🛍️ PRODUCT PAGES
│   │   ├── 📄 products.php                # 🛍️ Product listing/catalog
│   │   ├── 📄 product.php                 # 📦 Single product details
│   │   └── 📄 categories.php              # 📂 Product categories browser
│   │
│   ├── 🛒 SHOPPING PAGES
│   │   ├── 📄 cart.php                    # 🛒 Shopping cart
│   │   ├── 📄 checkout.php                # 💳 Checkout process
│   │   └── 📄 order.php                   # 📄 Order details/tracking
│   │
│   ├── 👤 USER ACCOUNT PAGES
│   │   ├── 📄 login.php                   # 🔐 User login
│   │   ├── 📄 signup.php                  # ✍️ User registration
│   │   ├── 📄 profile.php                 # 👤 User profile management
│   │   ├── 📄 orders.php                  # 📋 User order history
│   │   ├── 📄 downloads.php               # ⬇️ User digital downloads
│   │   ├── 📄 logout.php                  # 🚪 Logout handler
│   │   ├── 📄 forgot-password.php         # 🔑 Password reset request
│   │   └── 📄 reset-password.php          # 🔄 Password reset form
│   │
│   ├── ℹ️ INFORMATION PAGES
│   │   ├── 📄 about.php                   # ℹ️ About us/company info
│   │   ├── 📄 contact.php                 # 📞 Contact form
│   │   ├── 📄 faq.php                     # ❓ Frequently asked questions
│   │   └── 📄 support.php                 # 🆘 Support center
│   │
│   ├── 📜 LEGAL PAGES
│   │   ├── 📄 privacy.php                 # 🔒 Privacy policy
│   │   ├── 📄 terms.php                   # 📜 Terms of service
│   │   └── 📄 refund.php                  # 💰 Refund policy
│   │
│   └── 🔍 UTILITY PAGES
│       ├── 📄 search.php                  # 🔍 Search functionality
│       ├── 📄 color-demo.php              # 🎨 Color palette demo
│       ├── 📄 debug.php                   # 🐛 Debug information
│       └── 📄 test-rewrite.php            # 🧪 URL rewrite testing
│
├── 📁 config/                              # ⚙️ CONFIGURATION FILES
│   ├── 📄 config.php                      # 🔧 Main configuration
│   └── 📄 database.php                    # 🗄️ Database connection
│
├── 📁 includes/                            # 🧩 SHARED COMPONENTS
│   ├── 📄 header.php                      # 📄 HTML head & navigation
│   ├── 📄 footer.php                      # 📄 Footer & mobile nav
│   └── 📄 functions.php                   # 🔧 Utility functions
│
├── 📁 assets/                              # 🎨 STATIC ASSETS
│   ├── 📁 css/                            # 🎨 Stylesheets
│   │   └── 📄 style.css                   # 🎨 Main stylesheet
│   ├── 📁 js/                             # ⚡ JavaScript files
│   │   └── 📄 main.js                     # ⚡ Main JavaScript
│   └── 📁 images/                         # 🖼️ Image assets
│       ├── 📄 logo.png                    # 🏷️ Company logo
│       ├── 📄 favicon.ico                 # 🔖 Browser favicon
│       └── 📄 og-image.jpg                # 📱 Social media preview
│
├── 📁 uploads/                             # 📤 USER UPLOADS
│   ├── 📁 products/                       # 🛍️ Product images
│   ├── 📁 avatars/                        # 👤 User profile pictures
│   └── 📁 documents/                      # 📄 Digital products/files
│
├── 📁 admin/                               # 🔐 ADMIN PANEL
│   ├── 📄 index.php                       # 📊 Admin dashboard
│   ├── 📄 login.php                       # 🔐 Admin login
│   ├── 📄 logout.php                      # 🚪 Admin logout
│   ├── 📄 products.php                    # 🛍️ Product management
│   ├── 📄 orders.php                      # 📋 Order management
│   ├── 📄 users.php                       # 👥 User management
│   ├── 📄 settings.php                    # ⚙️ System settings
│   ├── 📄 analytics.php                   # 📊 Analytics dashboard
│   │
│   └── 📁 assets/                         # 🎨 Admin-specific assets
│       ├── 📁 css/                        # 🎨 Admin stylesheets
│       │   └── 📄 admin.css               # 🎨 Admin panel styles
│       └── 📁 js/                         # ⚡ Admin JavaScript
│           └── 📄 admin.js                # ⚡ Admin functionality
│
├── 📁 api/                                 # 🔌 API ENDPOINTS
│   ├── 📄 products.php                    # 🛍️ Product API
│   ├── 📄 cart.php                        # 🛒 Cart operations
│   ├── 📄 orders.php                      # 📋 Order operations
│   ├── 📄 users.php                       # 👤 User operations
│   └── 📄 payments.php                    # 💳 Payment processing
│
├── 📁 database/                            # 🗄️ DATABASE FILES
│   ├── 📄 schema.sql                      # 🏗️ Database structure
│   ├── 📄 sample-data.sql                 # 📊 Sample/test data
│   └── 📁 migrations/                     # 🔄 Database migrations
│       ├── 📄 001_initial_setup.sql       # 🏗️ Initial database
│       └── 📄 002_add_features.sql        # ➕ Feature additions
│
├── 📁 vendor/                              # 📦 THIRD-PARTY LIBRARIES
│   └── 📄 autoload.php                    # 🔄 Composer autoloader
│
├── 📁 docs/                                # 📚 DOCUMENTATION
│   ├── 📄 installation.md                 # 🛠️ Setup instructions
│   ├── 📄 api-documentation.md            # 🔌 API reference
│   └── 📄 user-guide.md                   # 👤 User manual
│
└── 📁 logs/                                # 📝 LOG FILES
    ├── 📄 error.log                       # ❌ Error logs
    ├── 📄 access.log                      # 👁️ Access logs
    └── 📄 payment.log                     # 💳 Payment logs
```

## 📋 File Organization Guidelines

### 🌐 Root Level Files (Public Pages)
**Location:** `/desh-eshop/`
**Purpose:** User-facing pages accessible via web browser
**Examples:** `index.php`, `products.php`, `login.php`

### ⚙️ Configuration Files
**Location:** `/desh-eshop/config/`
**Purpose:** System configuration and database connections
**Security:** Should be protected from direct web access

### 🧩 Shared Components
**Location:** `/desh-eshop/includes/`
**Purpose:** Reusable PHP components (header, footer, functions)
**Usage:** Included in multiple pages using `require_once`

### 🎨 Static Assets
**Location:** `/desh-eshop/assets/`
**Purpose:** CSS, JavaScript, images, and other static files
**Structure:** Organized by file type (css/, js/, images/)

### 📤 User Uploads
**Location:** `/desh-eshop/uploads/`
**Purpose:** User-generated content and file uploads
**Security:** Proper file validation and access controls required

### 🔐 Admin Panel
**Location:** `/desh-eshop/admin/`
**Purpose:** Administrative interface for managing the system
**Security:** Protected by authentication and authorization

### 🔌 API Endpoints
**Location:** `/desh-eshop/api/`
**Purpose:** RESTful API endpoints for AJAX and mobile app integration
**Format:** Returns JSON responses

### 🗄️ Database Files
**Location:** `/desh-eshop/database/`
**Purpose:** SQL files for database structure and sample data
**Usage:** Import during installation and updates

## 🔒 Security Considerations

### Protected Directories
```
config/         # Configuration files
includes/       # PHP includes
database/       # SQL files
logs/           # Log files
vendor/         # Third-party libraries
```

### .htaccess Protection
```apache
# Protect sensitive directories
<Files ~ "^\.">
    Order allow,deny
    Deny from all
</Files>

# Protect PHP configuration files
<Files ~ "config\.php$">
    Order allow,deny
    Deny from all
</Files>
```

## 📁 Directory Permissions

```
Root Directory:     755
config/:           750 (read-only for web server)
uploads/:          755 (write access for uploads)
logs/:             755 (write access for logging)
assets/:           755 (read-only static files)
admin/:            750 (restricted access)
```

## 🚀 Development Best Practices

1. **Separation of Concerns:** Keep logic, presentation, and data separate
2. **Consistent Naming:** Use kebab-case for files, camelCase for functions
3. **Security First:** Validate inputs, sanitize outputs, protect sensitive files
4. **Documentation:** Comment code and maintain this structure document
5. **Version Control:** Use Git with proper .gitignore for sensitive files

## 📝 File Naming Conventions

- **Pages:** `kebab-case.php` (e.g., `forgot-password.php`)
- **Classes:** `PascalCase.php` (e.g., `UserManager.php`)
- **Functions:** `camelCase` (e.g., `getUserData()`)
- **Constants:** `UPPER_SNAKE_CASE` (e.g., `SITE_URL`)
- **Database:** `snake_case` (e.g., `user_profiles`)

This structure ensures maintainability, security, and scalability for the Desh Engineering e-commerce platform. 🏗️✨
