# BanyumaSportHub

<div align="center">
  <img src="assets/img/logo.png" alt="BanyumaSportHub Logo" width="200">
  
  [![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net)
  [![Bootstrap Version](https://img.shields.io/badge/Bootstrap-5.0-blueviolet.svg)](https://getbootstrap.com)
  [![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
  [![Contributors](https://img.shields.io/github/contributors/username/BanyumaSportHub)](https://github.com/username/BanyumaSportHub/graphs/contributors)
</div>

## 📋 Daftar Isi
- [Tentang BanyumaSportHub](#-tentang-banyumasporthub)
- [Fitur Utama](#-fitur-utama)
- [Teknologi](#-teknologi)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Panduan Instalasi](#-panduan-instalasi)
- [Struktur Database](#-struktur-database)
- [API Documentation](#-api-documentation)
- [Keamanan](#-keamanan)
- [Panduan Pengembangan](#-panduan-pengembangan)
- [Kontribusi](#-kontribusi)
- [Tim Pengembang](#-tim-pengembang)
- [Lisensi](#-lisensi)
- [Kontak](#-kontak)
- [Dokumentasi](#-dokumentasi)
- [🔍 SEO & Analytics](#-seo-&-analytics)
- [📧 Email Marketing System](#-email-marketing-system)

## 🎯 Tentang BanyumaSportHub

BanyumaSportHub adalah platform digital terdepan yang berfungsi sebagai pusat informasi, inspirasi, dan kolaborasi di bidang olahraga untuk masyarakat Kabupaten Banyumas. Platform ini menghubungkan komunitas olahraga, fasilitas olahraga, dan penggemar olahraga dalam satu ekosistem digital yang terintegrasi.

### Visi
Menjadi platform olahraga terdepan yang memfasilitasi pengembangan olahraga di Kabupaten Banyumas dan sekitarnya.

### Misi
1. Memperluas akses informasi olahraga
2. Memfasilitasi kolaborasi antar komunitas olahraga
3. Meningkatkan partisipasi masyarakat dalam kegiatan olahraga
4. Mengembangkan ekosistem olahraga digital yang terintegrasi

## ✨ Fitur Utama

### 1. Marketplace / admin
- **Katalog Produk**: Menampilkan produk olahraga dengan detail lengkap
- **Keranjang Belanja**: Sistem keranjang belanja yang intuitif
- **Checkout**: Proses pembayaran yang aman dan mudah
- **Manajemen Stok**: Sistem manajemen stok real-time
- **Preview Produk**: Modal preview dengan detail lengkap
- **Riwayat Transaksi**: Melacak semua transaksi pengguna

### 2. Event Olahraga
- **Kalender Event**: Menampilkan jadwal event olahraga
- **Detail Event**: Informasi lengkap tentang setiap event
- **Pendaftaran**: Sistem pendaftaran event online
- **Notifikasi**: Pengingat event dan update status
- **Galeri**: Dokumentasi event dalam bentuk foto dan video

### 3. Lapangan Olahraga
- **Direktori**: Daftar lengkap lapangan olahraga di Banyumas
- **Booking Online**: Sistem pemesanan lapangan online
- **Rating & Review**: Sistem penilaian dan ulasan pengguna
- **Peta Lokasi**: Integrasi dengan Google Maps
- **Jam Operasional**: Informasi waktu buka-tutup

### 4. Komunitas Olahraga
- **Profil Komunitas**: Informasi detail setiap komunitas
- **Media Sosial**: Integrasi dengan platform sosial media
- **Forum Diskusi**: Wadah diskusi antar anggota
- **Event Komunitas**: Pengumuman dan pendaftaran event
- **Galeri Aktivitas**: Dokumentasi kegiatan komunitas

### 5. Artikel Olahraga
- **Konten Edukatif**: Artikel kesehatan dan olahraga
- **Tips & Trik**: Panduan olahraga untuk berbagai level
- **Berita Olahraga**: Update terkini seputar olahraga
- **Kategori**: Pengelompokan artikel berdasarkan tema
- **Search Engine**: Pencarian artikel yang powerful

## 🛠 Teknologi

### Backend
- PHP 7.4+
- MySQL/MariaDB
- Apache/Nginx

### Frontend
- HTML5
- CSS3
- JavaScript (ES6+)
- Bootstrap 5
- jQuery
- Font Awesome
- Swiper.js

### Development Tools
- Git
- VS Code
- XAMPP

## 💻 Persyaratan Sistem

### Server
- PHP >= 7.4
- MySQL >= 5.7
- Apache >= 2.4
- mod_rewrite enabled
- SSL Certificate (untuk production)

### Client
- Browser modern (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- Cookies enabled

## 📥 Panduan Instalasi

### 1. Persiapan
```bash
# Clone repository
git clone https://github.com/username/BanyumaSportHub.git

# Masuk ke direktori proyek
cd BanyumaSportHub

# Install dependencies
composer install
npm install
```

### 2. Konfigurasi Database
```bash
# Import database
mysql -u username -p database_name < database.sql

# Konfigurasi koneksi database
cp config/database.example.php config/database.php
# Edit file database.php sesuai dengan konfigurasi Anda
```

### 3. Konfigurasi Web Server
```apache
# Apache (.htaccess)
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /BanyumaSportHub/
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /BanyumaSportHub/index.php [L]
</IfModule>
```

### 4. Build Assets
```bash
# Compile CSS dan JavaScript
npm run build
```

### 5. Jalankan Aplikasi
```bash
# Development server
php -S localhost:8000

# Atau gunakan XAMPP/WAMP

# Akses melalui browser: http://localhost/BanyumaSportHub
```

## 📊 Struktur Database

### Diagram ERD
```
[Diagram ERD akan ditambahkan di sini]
```

### Tabel Utama
```sql
-- Users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    category VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    shipping_address TEXT,
    tracking_number VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

## 🔒 Keamanan

### Implementasi Keamanan
1. **SQL Injection Prevention**
   - Prepared Statements
   - Parameter Binding
   - Input Validation

2. **XSS Protection**
   - Output Encoding
   - Content Security Policy
   - Input Sanitization

3. **Authentication & Authorization**
   - Password Hashing (Argon2id)
   - Session Management
   - Role-based Access Control

4. **Data Protection**
   - HTTPS/SSL
   - Data Encryption
   - Secure Headers

5. **Security Headers**
   ```php
   header("X-Frame-Options: DENY");
   header("X-XSS-Protection: 1; mode=block");
   header("X-Content-Type-Options: nosniff");
   header("Referrer-Policy: strict-origin-when-cross-origin");
   header("Content-Security-Policy: default-src 'self'");
   ```

## 👥 Tim Pengembang

### Core Team
- **Project Manager**: [Kharisma Nurhayati, Laura Anisa MR, Nuurul Azizah]
- **Lead Developer**: [Nuurul Azizah]
- **UI/UX Designer**: [Laura Anisa MR]
- **Database Administrator**: [Kharisma Nurhayati]
- **Security Specialist**: [Kharisma Nurhayati, Laura Anisa MR, Nuurul Azizah]

### Kontributor
- [Daftar kontributor akan ditambahkan di sini]

## 📝 Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT - lihat file [LICENSE](LICENSE) untuk detail.

## 📞 Kontak

### Support
- Email: support@banyumasporthub.com
- Website: https://banyumasporthub.com
- Phone: +62 XXX-XXXX-XXXX

### Social Media
- Instagram: [@banyumasporthub](https://instagram.com/banyumasporthub)
- Twitter: [@banyumasporthub](https://twitter.com/banyumasporthub)
- Facebook: [BanyumaSportHub](https://facebook.com/banyumasporthub)

### Office
```
BanyumaSportHub
Jl. Contoh No. 123
Purwokerto, Banyumas
Jawa Tengah, Indonesia
```

## 📚 Dokumentasi

### API Documentation
```markdown
### Base URL
```
https://api.banyumasporthub.com/v1
```

### Authentication
```http
POST /auth/login
Content-Type: application/json

{
    "username": "string",
    "password": "string"
}
```

### Endpoints

#### Users
```http
GET /users
GET /users/{id}
POST /users
PUT /users/{id}
DELETE /users/{id}
```

#### Products
```http
GET /products
GET /products/{id}
POST /products
PUT /products/{id}
DELETE /products/{id}
```

#### Orders
```http
GET /orders
GET /orders/{id}
POST /orders
PUT /orders/{id}
DELETE /orders/{id}
```

### Response Format
```json
{
    "status": "success",
    "data": {},
    "message": "string",
    "errors": []
}
```

### Error Codes
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 500: Internal Server Error
```

### User Manual
```markdown
## Panduan Pengguna

### 1. Registrasi & Login
1. Klik tombol "Register" di pojok kanan atas
2. Isi formulir dengan data lengkap
3. Verifikasi email
4. Login dengan username dan password

### 2. Marketplace
1. Pilih kategori produk
2. Filter produk sesuai kebutuhan
3. Klik produk untuk detail
4. Tambahkan ke keranjang
5. Checkout dan pembayaran

### 3. Event Olahraga
1. Lihat kalender event
2. Pilih event yang diminati
3. Daftar sebagai peserta
4. Terima konfirmasi via email

### 4. Lapangan Olahraga
1. Cari lapangan berdasarkan lokasi
2. Pilih tanggal dan waktu
3. Booking lapangan
4. Bayar booking fee
5. Terima konfirmasi booking

### 5. Komunitas
1. Cari komunitas olahraga
2. Bergabung dengan komunitas
3. Ikuti event komunitas
4. Berpartisipasi dalam forum
```

### Technical Documentation
```markdown
## Arsitektur Sistem

### Frontend
- Bootstrap 5 untuk UI components
- jQuery untuk DOM manipulation
- Swiper.js untuk carousel
- Custom CSS untuk styling

### Backend
- PHP 7.4+ dengan OOP
- MySQL untuk database
- Apache sebagai web server
- RESTful API architecture

### Database Schema
[Diagram ERD terlampir]

### File Structure
```
BanyumaSportHub/
├── api/
│   ├── v1/
│   │   ├── controllers/
│   │   ├── models/
│   │   └── routes/
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── config/
├── includes/
├── tests/
└── vendor/
```

### Coding Standards
- PSR-12 coding style
- PHPDoc documentation
- Unit testing dengan PHPUnit
- Code review process
```

### Deployment Guide
```markdown
## Panduan Deployment

### 1. Prasyarat
- Server dengan PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- SSL Certificate
- Domain name

### 2. Server Setup
```bash
# Install dependencies
apt-get update
apt-get install apache2 mysql-server php7.4

# Configure Apache
a2enmod rewrite
a2enmod ssl

# Configure PHP
php.ini settings:
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
```

### 3. Application Deployment
```bash
# Clone repository
git clone https://github.com/username/BanyumaSportHub.git

# Setup environment
cp .env.example .env
composer install --no-dev
npm install
npm run build

# Database setup
mysql -u root -p < database.sql

# Set permissions
chmod -R 755 storage/
chown -R www-data:www-data storage/
```

### 4. Monitoring
- Setup error logging
- Configure backup system
- Setup monitoring tools
```

## 🧪 Testing & Quality Assurance

### Unit Testing
```php
// tests/Unit/ProductTest.php
class ProductTest extends TestCase
{
    public function test_can_create_product()
    {
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100.00
        ]);
        
        $this->assertEquals('Test Product', $product->name);
    }
}
```

### Integration Testing
```php
// tests/Feature/OrderTest.php
class OrderTest extends TestCase
{
    public function test_can_create_order()
    {
        $response = $this->postJson('/api/orders', [
            'user_id' => 1,
            'items' => [
                ['product_id' => 1, 'quantity' => 2]
            ]
        ]);
        
        $response->assertStatus(201);
    }
}
```

### Performance Testing
- Load testing dengan Apache JMeter
- Stress testing untuk concurrent users
- Response time monitoring
- Database query optimization

### Security Testing
- Penetration testing
- Vulnerability scanning
- Security audit
- Code security review

## 💾 Backup & Recovery

### Automated Backup System
```bash
#!/bin/bash
# backup.sh

# Database backup
mysqldump -u root -p banyumasporthub > backup/db_$(date +%Y%m%d).sql

# File backup
tar -czf backup/files_$(date +%Y%m%d).tar.gz public/uploads/

# Upload to cloud storage
aws s3 cp backup/db_$(date +%Y%m%d).sql s3://banyumasporthub-backups/
```

### Disaster Recovery Plan
1. **Identifikasi Risiko**
   - Server failure
   - Data corruption
   - Natural disasters
   - Cyber attacks

2. **Recovery Procedures**
   - Database restoration
   - Application redeployment
   - Data recovery
   - Service restoration

3. **Recovery Time Objectives (RTO)**
   - Critical systems: 1 hour
   - Non-critical systems: 4 hours

4. **Recovery Point Objectives (RPO)**
   - Database: 15 minutes
   - Files: 1 hour

### Version Control
```bash
# Git workflow
git checkout -b feature/new-feature
git add .
git commit -m "feat: add new feature"
git push origin feature/new-feature

# Create pull request
# Code review
# Merge to main branch
```

## 📜 Compliance

### GDPR Compliance
- Data protection measures
- User consent management
- Data access rights
- Data portability
- Right to be forgotten

### Terms & Conditions
```markdown
## Terms of Service

1. Acceptance of Terms
2. User Responsibilities
3. Intellectual Property
4. Limitation of Liability
5. Termination
6. Governing Law
```

### Privacy Policy
```markdown
## Privacy Policy

1. Information Collection
2. Information Usage
3. Information Sharing
4. Data Security
5. User Rights
6. Cookie Policy
```

### Return Policy
```markdown
## Return Policy

1. Eligibility
2. Return Process
3. Refund Timeline
4. Shipping Costs
5. Exceptions
```

## 🔍 SEO & Analytics

### Meta Tags Optimization
```html
<!-- Primary Meta Tags -->
<title>BanyumaSportHub - Pusat Olahraga Digital Banyumas</title>
<meta name="title" content="BanyumaSportHub - Pusat Olahraga Digital Banyumas">
<meta name="description" content="Platform digital terdepan untuk informasi, inspirasi, dan kolaborasi olahraga di Kabupaten Banyumas. Temukan event olahraga, lapangan, komunitas, dan produk olahraga.">
<meta name="keywords" content="olahraga banyumas, event olahraga, lapangan olahraga, komunitas olahraga, produk olahraga, banyumas sport">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://banyumasporthub.com/">
<meta property="og:title" content="BanyumaSportHub - Pusat Olahraga Digital Banyumas">
<meta property="og:description" content="Platform digital terdepan untuk informasi, inspirasi, dan kolaborasi olahraga di Kabupaten Banyumas.">
<meta property="og:image" content="https://banyumasporthub.com/assets/img/og-image.jpg">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://banyumasporthub.com/">
<meta property="twitter:title" content="BanyumaSportHub - Pusat Olahraga Digital Banyumas">
<meta property="twitter:description" content="Platform digital terdepan untuk informasi, inspirasi, dan kolaborasi olahraga di Kabupaten Banyumas.">
<meta property="twitter:image" content="https://banyumasporthub.com/assets/img/twitter-image.jpg">

<!-- Additional Meta Tags -->
<meta name="robots" content="index, follow">
<meta name="language" content="Indonesian">
<meta name="revisit-after" content="7 days">
<meta name="author" content="BanyumaSportHub">
```

### Sitemap Structure
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <!-- Homepage -->
  <url>
    <loc>https://banyumasporthub.com/</loc>
    <lastmod>2024-03-20</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  
  <!-- Marketplace -->
  <url>
    <loc>https://banyumasporthub.com/marketplace</loc>
    <lastmod>2024-03-20</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
  
  <!-- Events -->
  <url>
    <loc>https://banyumasporthub.com/events</loc>
    <lastmod>2024-03-20</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
  
  <!-- Sports Fields -->
  <url>
    <loc>https://banyumasporthub.com/fields</loc>
    <lastmod>2024-03-20</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  
  <!-- Communities -->
  <url>
    <loc>https://banyumasporthub.com/communities</loc>
    <lastmod>2024-03-20</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  
  <!-- Articles -->
  <url>
    <loc>https://banyumasporthub.com/articles</loc>
    <lastmod>2024-03-20</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.7</priority>
  </url>
</urlset>
```

### Google Analytics Integration
```javascript
// Google Analytics 4 Configuration
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX', {
    'page_title': 'BanyumaSportHub',
    'send_page_view': true,
    'cookie_flags': 'SameSite=None;Secure'
  });
</script>

// Custom Event Tracking
function trackEvent(category, action, label) {
  gtag('event', action, {
    'event_category': category,
    'event_label': label
  });
}

// Track User Actions
document.addEventListener('DOMContentLoaded', function() {
  // Track Product Views
  document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', () => {
      trackEvent('Product', 'View', card.dataset.productId);
    });
  });
  
  // Track Add to Cart
  document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', () => {
      trackEvent('Cart', 'Add', button.dataset.productId);
    });
  });
  
  // Track Checkout Steps
  document.querySelectorAll('.checkout-step').forEach(step => {
    step.addEventListener('click', () => {
      trackEvent('Checkout', 'Step', step.dataset.step);
    });
  });
});
```

## 📧 Email Marketing System

### Email Templates
```php
// templates/welcome.php
<!DOCTYPE html>
<html>
<head>
    <title>Selamat Datang di BanyumaSportHub</title>
</head>
<body>
    <h1>Selamat Datang, {{name}}!</h1>
    <p>Terima kasih telah bergabung dengan BanyumaSportHub.</p>
    <a href="{{verification_link}}">Verifikasi Email Anda</a>
</body>
</html>

// templates/newsletter.php
<!DOCTYPE html>
<html>
<head>
    <title>Newsletter BanyumaSportHub</title>
</head>
<body>
    <h1>Event Olahraga Minggu Ini</h1>
    <div class="events">
        {{#each events}}
        <div class="event">
            <h2>{{title}}</h2>
            <p>{{description}}</p>
            <a href="{{link}}">Daftar Sekarang</a>
        </div>
        {{/each}}
    </div>
</body>
</html>
```

### Email Campaign System
```php
class EmailCampaign {
    private $db;
    private $mailer;
    
    public function __construct() {
        $this->db = new Database();
        $this->mailer = new PHPMailer();
    }
    
    public function sendWelcomeEmail($user) {
        $template = $this->loadTemplate('welcome');
        $content = $this->parseTemplate($template, [
            'name' => $user['name'],
            'verification_link' => $this->generateVerificationLink($user)
        ]);
        
        return $this->sendEmail($user['email'], 'Selamat Datang di BanyumaSportHub', $content);
    }
    
    public function sendNewsletter($subscribers) {
        $events = $this->getUpcomingEvents();
        $template = $this->loadTemplate('newsletter');
        
        foreach ($subscribers as $subscriber) {
            $content = $this->parseTemplate($template, [
                'events' => $events,
                'unsubscribe_link' => $this->generateUnsubscribeLink($subscriber)
            ]);
            
            $this->sendEmail($subscriber['email'], 'Event Olahraga Minggu Ini', $content);
        }
    }
    
    public function trackEmailMetrics($campaign_id) {
        return [
            'sent' => $this->getSentCount($campaign_id),
            'opened' => $this->getOpenCount($campaign_id),
            'clicked' => $this->getClickCount($campaign_id),
            'bounced' => $this->getBounceCount($campaign_id)
        ];
    }
}
```

### Email List Management
```sql
-- Email Subscribers Table
CREATE TABLE email_subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(100),
    status ENUM('active', 'unsubscribed', 'bounced') DEFAULT 'active',
    preferences JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Email Campaigns Table
CREATE TABLE email_campaigns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    template_id INT,
    status ENUM('draft', 'scheduled', 'sending', 'completed') DEFAULT 'draft',
    scheduled_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Email Metrics Table
CREATE TABLE email_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    campaign_id INT,
    subscriber_id INT,
    event_type ENUM('sent', 'opened', 'clicked', 'bounced'),
    event_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES email_campaigns(id),
    FOREIGN KEY (subscriber_id) REFERENCES email_subscribers(id)
);
```

---

<div align="center">
  Made with ❤️ by Kelompok 3 IPPL
</div> 
