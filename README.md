# 🖥️ PC SHOP - Website Thương Mại Điện Tử

Website bán PC & linh kiện máy tính được xây dựng bằng PHP thuần (không framework), chạy hoàn toàn trên Docker.

## 📋 Mục Lục

- [Tính năng](#-tính-năng)
- [Công nghệ](#-công-nghệ)
- [Cài đặt](#-cài-đặt)
- [Cấu trúc dự án](#-cấu-trúc-dự-án)
- [Sử dụng](#-sử-dụng)
- [API Endpoints](#-api-endpoints)
- [Database Schema](#-database-schema)
- [Tài khoản demo](#-tài-khoản-demo)
- [Troubleshooting](#-troubleshooting)

## ✨ Tính năng

### Người dùng (User)
- ✅ Trang chủ với banner slider tự động 5s
- ✅ Danh mục sản phẩm với bộ lọc (giá, hãng) và sắp xếp
- ✅ Chi tiết sản phẩm với slider ảnh, đánh giá, sản phẩm liên quan
- ✅ Đăng ký / Đăng nhập / Quản lý tài khoản
- ✅ Giỏ hàng (hỗ trợ cả session và user)
- ✅ Thanh toán với tự động điền thông tin
- ✅ Lịch sử đơn hàng
- ✅ Đánh giá sản phẩm (chỉ khi đã mua)
- ✅ Tìm kiếm sản phẩm

### Quản trị viên (Admin)
- ✅ Dashboard với thống kê và biểu đồ doanh thu
- ✅ Quản lý sản phẩm (CRUD + upload nhiều ảnh)
- ✅ Quản lý danh mục
- ✅ Quản lý đơn hàng (cập nhật trạng thái)
- ✅ Quản lý banner/slider
- ✅ Quản lý đánh giá
- ✅ Quản lý người dùng

### Bảo mật
- ✅ Password hashing (bcrypt)
- ✅ Prepared Statements (PDO)
- ✅ Session-based authentication
- ✅ CSRF protection
- ✅ Input validation & sanitization
- ✅ Secure file upload (type, size check)
- ✅ Image resize tự động

## 🛠️ Công nghệ

- **Backend:** PHP 8.2 + Apache
- **Database:** MariaDB 10.11
- **Database Management:** phpMyAdmin
- **Container:** Docker + Docker Compose
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Charts:** Chart.js
- **Icons:** Font Awesome 6

## 🚀 Cài đặt

### Yêu cầu hệ thống

- Docker Engine 20.x+
- Docker Compose 2.x+
- Ubuntu Server (khuyến nghị) hoặc bất kỳ OS nào có Docker

### Bước 1: Clone repository

```bash
git clone <repository-url>
cd web_ban_hang
```

### Bước 2: Cấu hình môi trường

```bash
# Copy file .env.example thành .env
cp .env.example .env

# Chỉnh sửa .env nếu cần (mặc định đã OK)
nano .env
```

**Cấu hình mặc định:**
```env
WEB_PORT=8080
DB_NAME=pc_shop
DB_USER=pc_shop_user
DB_PASSWORD=secure_password_123
DB_ROOT_PASSWORD=root_password_123
DB_PORT=3306
PMA_PORT=8081
```

### Bước 3: Build và khởi chạy

```bash
# Build images và start containers
docker-compose up -d --build

# Xem logs
docker-compose logs -f

# Kiểm tra trạng thái containers
docker-compose ps
```

### Bước 4: Truy cập ứng dụng

- **Website:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081
  - Server: `db`
  - Username: `pc_shop_user`
  - Password: `secure_password_123`

### Bước 5: Import dữ liệu mẫu (tự động)

Database schema và dữ liệu mẫu sẽ tự động được import khi container khởi động lần đầu:
- `database/schema.sql` - Cấu trúc database
- `database/seed.sql` - Dữ liệu mẫu

## 📁 Cấu trúc dự án

```
web_ban_hang/
├── admin/                      # Admin panel
│   ├── index.php              # Dashboard
│   ├── products.php           # Quản lý sản phẩm
│   ├── categories.php         # Quản lý danh mục
│   └── orders.php             # Quản lý đơn hàng
│
├── config/                    # Configuration files
│   ├── database.php           # Database connection
│   └── functions.php          # Helper functions
│
├── database/                  # Database files
│   ├── schema.sql             # Database structure
│   └── seed.sql               # Sample data
│
├── includes/                  # Shared components
│   ├── header.php             # Header template
│   └── footer.php             # Footer template
│
├── public/                    # Public assets
│   ├── css/
│   │   ├── style.css          # Main stylesheet
│   │   └── admin.css          # Admin stylesheet
│   ├── js/
│   │   └── main.js            # Main JavaScript
│   ├── images/                # Static images
│   └── uploads/               # User uploads
│       ├── products/          # Product images
│       └── banners/           # Banner images
│
├── index.php                  # Homepage
├── category.php               # Category page
├── product.php                # Product detail
├── products.php               # Products listing
├── cart.php                   # Shopping cart
├── cart-api.php               # Cart API
├── checkout.php               # Checkout page
├── login.php                  # Login page
├── register.php               # Register page
├── logout.php                 # Logout handler
├── profile.php                # User profile
├── orders.php                 # Order history
│
├── docker-compose.yml         # Docker Compose config
├── Dockerfile                 # PHP Dockerfile
├── apache-config.conf         # Apache configuration
├── .env                       # Environment variables
├── .env.example               # Environment example
└── README.md                  # This file
```

## 📚 Sử dụng

### Quản lý Containers

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose stop

# Restart containers
docker-compose restart

# Stop và xóa containers
docker-compose down

# Xóa containers và volumes (CẢNH BÁO: Mất dữ liệu)
docker-compose down -v

# Rebuild containers sau khi thay đổi code
docker-compose up -d --build

# Xem logs của service cụ thể
docker-compose logs -f web
docker-compose logs -f db

# Truy cập vào container
docker-compose exec web bash
docker-compose exec db mysql -u root -p
```

### Quản lý Database

```bash
# Backup database
docker-compose exec db mysqldump -u pc_shop_user -psecure_password_123 pc_shop > backup.sql

# Restore database
docker-compose exec -T db mysql -u pc_shop_user -psecure_password_123 pc_shop < backup.sql

# Access MySQL console
docker-compose exec db mysql -u root -proot_password_123
```

### Quản lý Uploads

```bash
# Set permissions cho upload directory
docker-compose exec web chown -R www-data:www-data /var/www/html/public/uploads
docker-compose exec web chmod -R 755 /var/www/html/public/uploads

# Xem upload directory
docker-compose exec web ls -la /var/www/html/public/uploads/products
```

### Debug

```bash
# Xem PHP logs
docker-compose exec web tail -f /var/log/apache2/error.log

# Xem tất cả logs
docker-compose logs --tail=100 -f

# Check PHP version và extensions
docker-compose exec web php -v
docker-compose exec web php -m

# Test database connection
docker-compose exec web php -r "new PDO('mysql:host=db;dbname=pc_shop', 'pc_shop_user', 'secure_password_123');"
```

## 🔌 API Endpoints

### Cart API (`cart-api.php`)

**Add to cart**
```
POST /cart-api.php
Body: action=add&product_id=1&quantity=2
Response: {success: true, message: "...", cart_count: 5}
```

**Update cart**
```
POST /cart-api.php
Body: action=update&cart_id=1&quantity=3
Response: {success: true, message: "..."}
```

**Remove from cart**
```
POST /cart-api.php
Body: action=remove&cart_id=1
Response: {success: true, message: "..."}
```

**Get cart count**
```
GET /cart-api.php?action=get_count
Response: {success: true, count: 5}
```

## 🗄️ Database Schema

### Bảng chính

- **users** - Người dùng (khách hàng, admin)
- **categories** - Danh mục sản phẩm
- **products** - Sản phẩm
- **product_images** - Ảnh sản phẩm
- **banners** - Banner slider
- **carts** - Giỏ hàng
- **orders** - Đơn hàng
- **order_items** - Chi tiết đơn hàng
- **reviews** - Đánh giá sản phẩm

### Relationships

```
users (1) -----> (N) orders
users (1) -----> (N) reviews
users (1) -----> (N) carts

categories (1) -----> (N) products
products (1) -----> (N) product_images
products (1) -----> (N) reviews
products (1) -----> (N) order_items

orders (1) -----> (N) order_items
```

## 👤 Tài khoản demo

### Admin
- **Username:** admin
- **Password:** password123
- **Email:** admin@pcshop.com

### Customer
- **Username:** customer1
- **Password:** password123
- **Email:** customer1@email.com

## 🐛 Troubleshooting

### Container không start được

```bash
# Kiểm tra ports có bị chiếm không
netstat -tulpn | grep -E '8080|3306|8081'

# Thay đổi ports trong .env nếu cần
nano .env

# Rebuild
docker-compose down
docker-compose up -d --build
```

### Database connection failed

```bash
# Kiểm tra DB container
docker-compose ps
docker-compose logs db

# Restart DB container
docker-compose restart db

# Xóa và tạo lại volumes
docker-compose down -v
docker-compose up -d
```

### Permission denied cho uploads

```bash
# Fix permissions
docker-compose exec web bash
chown -R www-data:www-data /var/www/html/public/uploads
chmod -R 755 /var/www/html/public/uploads
exit
```

### Upload file không hoạt động

```bash
# Kiểm tra PHP upload settings
docker-compose exec web php -i | grep upload

# Tăng upload limit (đã set trong Dockerfile):
# upload_max_filesize = 50M
# post_max_size = 50M

# Rebuild nếu cần
docker-compose up -d --build
```

### Images không hiển thị

1. Kiểm tra file có tồn tại không:
```bash
docker-compose exec web ls -la /var/www/html/public/uploads/products
```

2. Kiểm tra permissions:
```bash
docker-compose exec web stat /var/www/html/public/uploads/products/[filename]
```

3. Thêm placeholder images:
```bash
# Download placeholder image
wget -O public/images/placeholder.jpg https://via.placeholder.com/400x300
wget -O public/images/placeholder-banner.jpg https://via.placeholder.com/1200x400
```

### Clear cache và rebuild

```bash
# Stop all containers
docker-compose down

# Remove all containers, networks, volumes
docker-compose down -v

# Remove old images
docker image prune -a

# Rebuild from scratch
docker-compose up -d --build
```

## 🔧 Customization

### Thay đổi theme colors

Chỉnh sửa `public/css/style.css`:

```css
:root {
    --primary-color: #2563eb;  /* Màu chính */
    --secondary-color: #64748b;
    --success-color: #10b981;
    --danger-color: #ef4444;
    /* ... */
}
```

### Thay đổi upload limits

Chỉnh sửa `Dockerfile`:

```dockerfile
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini
```

Rebuild:
```bash
docker-compose up -d --build
```

### Thêm payment gateway

1. Tạo file `config/payment.php`
2. Thêm logic vào `checkout.php`
3. Update database thêm trường payment status

## 📝 Development

### Local development workflow

1. Edit code trong host machine
2. Changes tự động sync vào container (via volume)
3. Refresh browser để xem changes
4. No need to rebuild cho PHP changes

### Thêm PHP extension

Chỉnh sửa `Dockerfile`:

```dockerfile
RUN docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    your_new_extension
```

Rebuild:
```bash
docker-compose up -d --build
```

## 📄 License

Dự án này được tạo ra cho mục đích học tập.

## 🤝 Contributing

Mọi đóng góp đều được chào đón! Vui lòng:
1. Fork repository
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📞 Support

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra [Troubleshooting](#-troubleshooting)
2. Xem logs: `docker-compose logs -f`
3. Tạo issue trên GitHub

---

**Happy Coding! 🚀**
