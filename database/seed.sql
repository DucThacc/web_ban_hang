-- =====================================================
-- PC SHOP SEED DATA
-- =====================================================

-- USE `pc_shop`;

-- =====================================================
-- SEED: users
-- Password for all users: password123
-- =====================================================
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `phone`, `address`, `role`) VALUES
('admin', 'admin@pcshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '0901234567', '123 Nguyen Hue, District 1, HCMC', 'admin'),
('customer1', 'customer1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyen Van A', '0912345678', '456 Le Loi, District 3, HCMC', 'customer'),
('customer2', 'customer2@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tran Thi B', '0923456789', '789 Vo Van Tan, District 10, HCMC', 'customer');

-- =====================================================
-- SEED: categories
-- =====================================================
INSERT INTO `categories` (`name`, `slug`, `description`, `display_order`, `is_active`) VALUES
('PC Gaming', 'pc-gaming', 'Máy tính gaming hoàn chỉnh', 1, 1),
('CPU - Bộ vi xử lý', 'cpu-bo-vi-xu-ly', 'CPU Intel, AMD', 2, 1),
('VGA - Card màn hình', 'vga-card-man-hinh', 'Card đồ họa NVIDIA, AMD', 3, 1),
('RAM - Bộ nhớ', 'ram-bo-nho', 'RAM DDR4, DDR5', 4, 1),
('Mainboard - Bo mạch chủ', 'mainboard-bo-mach-chu', 'Mainboard Intel, AMD', 5, 1),
('SSD - Ổ cứng', 'ssd-o-cung', 'SSD NVMe, SATA', 6, 1),
('HDD - Ổ cứng', 'hdd-o-cung', 'HDD dung lượng lớn', 7, 1),
('PSU - Nguồn', 'psu-nguon', 'Nguồn máy tính', 8, 1),
('Case - Vỏ máy', 'case-vo-may', 'Vỏ case máy tính', 9, 1),
('Tản nhiệt', 'tan-nhiet', 'Tản nhiệt khí, nước', 10, 1),
('Màn hình', 'man-hinh', 'Màn hình gaming, văn phòng', 11, 1),
('Bàn phím', 'ban-phim', 'Bàn phím cơ, gaming', 12, 1),
('Chuột', 'chuot', 'Chuột gaming, văn phòng', 13, 1),
('Tai nghe', 'tai-nghe', 'Tai nghe gaming, âm thanh', 14, 1),
('Phụ kiện', 'phu-kien', 'Phụ kiện máy tính', 15, 1);

-- =====================================================
-- SEED: products - PC Gaming
-- =====================================================
INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `brand`, `price`, `sale_price`, `quantity`, `description`, `specifications`, `is_featured`, `is_active`) VALUES
(1, 'PC Gaming ASUS ROG Strix G10', 'pc-gaming-asus-rog-strix-g10', 'PC-ROG-G10', 'ASUS', 35990000, 32990000, 10, 
'Máy tính gaming ASUS ROG Strix G10 với hiệu năng mạnh mẽ, thiết kế đẹp mắt phù hợp cho game thủ chuyên nghiệp', 
'CPU: Intel Core i7-12700F\nRAM: 16GB DDR4\nVGA: NVIDIA RTX 3060 Ti\nSSD: 512GB NVMe\nCase: ASUS ROG', 1, 1),

(1, 'PC Gaming Acer Predator Orion 3000', 'pc-gaming-acer-predator-orion-3000', 'PC-PRED-3000', 'Acer', 42990000, 39990000, 8, 
'Máy tính gaming Acer Predator với cấu hình cao cấp, RGB đẹp mắt', 
'CPU: Intel Core i7-13700\nRAM: 32GB DDR5\nVGA: NVIDIA RTX 4060 Ti\nSSD: 1TB NVMe\nCase: Predator', 1, 1),

(1, 'PC Gaming MSI Aegis RS', 'pc-gaming-msi-aegis-rs', 'PC-MSI-AEGIS', 'MSI', 29990000, NULL, 5, 
'Máy tính gaming MSI Aegis RS thiết kế hiện đại, hiệu năng tốt', 
'CPU: Intel Core i5-12400F\nRAM: 16GB DDR4\nVGA: NVIDIA RTX 3050\nSSD: 512GB NVMe\nCase: MSI Aegis', 0, 1);

-- =====================================================
-- SEED: products - CPU
-- =====================================================
INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `brand`, `price`, `sale_price`, `quantity`, `description`, `specifications`, `is_featured`, `is_active`) VALUES
(2, 'Intel Core i9-13900K', 'intel-core-i9-13900k', 'CPU-I9-13900K', 'Intel', 14990000, 13990000, 15, 
'CPU Intel thế hệ 13 hiệu năng cao nhất cho gaming và workstation', 
'Socket: LGA1700\nCores: 24 (8P+16E)\nThreads: 32\nBase Clock: 3.0GHz\nBoost Clock: 5.8GHz\nCache: 36MB', 1, 1),

(2, 'Intel Core i7-13700K', 'intel-core-i7-13700k', 'CPU-I7-13700K', 'Intel', 10990000, 9990000, 20, 
'CPU Intel Core i7 thế hệ 13 hiệu năng mạnh mẽ', 
'Socket: LGA1700\nCores: 16 (8P+8E)\nThreads: 24\nBase Clock: 3.4GHz\nBoost Clock: 5.4GHz\nCache: 30MB', 1, 1),

(2, 'Intel Core i5-13600K', 'intel-core-i5-13600k', 'CPU-I5-13600K', 'Intel', 7990000, 7490000, 25, 
'CPU Intel Core i5 thế hệ 13 tốt nhất cho gaming', 
'Socket: LGA1700\nCores: 14 (6P+8E)\nThreads: 20\nBase Clock: 3.5GHz\nBoost Clock: 5.1GHz\nCache: 24MB', 0, 1),

(2, 'AMD Ryzen 9 7950X', 'amd-ryzen-9-7950x', 'CPU-R9-7950X', 'AMD', 15990000, 14990000, 12, 
'CPU AMD Ryzen 9 hiệu năng đỉnh cao cho đa nhiệm', 
'Socket: AM5\nCores: 16\nThreads: 32\nBase Clock: 4.5GHz\nBoost Clock: 5.7GHz\nCache: 64MB', 1, 1),

(2, 'AMD Ryzen 7 7800X3D', 'amd-ryzen-7-7800x3d', 'CPU-R7-7800X3D', 'AMD', 11990000, 10990000, 18, 
'CPU AMD Ryzen 7 với 3D V-Cache tốt nhất cho gaming', 
'Socket: AM5\nCores: 8\nThreads: 16\nBase Clock: 4.2GHz\nBoost Clock: 5.0GHz\nCache: 96MB', 1, 1),

(2, 'AMD Ryzen 5 7600X', 'amd-ryzen-5-7600x', 'CPU-R5-7600X', 'AMD', 6990000, NULL, 30, 
'CPU AMD Ryzen 5 giá rẻ hiệu năng tốt', 
'Socket: AM5\nCores: 6\nThreads: 12\nBase Clock: 4.7GHz\nBoost Clock: 5.3GHz\nCache: 32MB', 0, 1);

-- =====================================================
-- SEED: products - VGA
-- =====================================================
INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `brand`, `price`, `sale_price`, `quantity`, `description`, `specifications`, `is_featured`, `is_active`) VALUES
(3, 'NVIDIA GeForce RTX 4090 MSI Gaming X Trio', 'nvidia-rtx-4090-msi', 'VGA-4090-MSI', 'MSI', 52990000, 49990000, 5, 
'Card đồ họa mạnh nhất hiện nay cho gaming 4K và workstation', 
'GPU: NVIDIA RTX 4090\nVRAM: 24GB GDDR6X\nClock: 2610MHz\nTDP: 450W\nPorts: 3xDP, 1xHDMI', 1, 1),

(3, 'NVIDIA GeForce RTX 4080 ASUS ROG Strix', 'nvidia-rtx-4080-asus', 'VGA-4080-ASUS', 'ASUS', 36990000, 34990000, 8, 
'Card đồ họa RTX 4080 hiệu năng cao cho gaming 4K', 
'GPU: NVIDIA RTX 4080\nVRAM: 16GB GDDR6X\nClock: 2565MHz\nTDP: 320W\nPorts: 3xDP, 2xHDMI', 1, 1),

(3, 'NVIDIA GeForce RTX 4070 Ti Gigabyte Gaming OC', 'nvidia-rtx-4070-ti-gigabyte', 'VGA-4070TI-GB', 'Gigabyte', 24990000, 22990000, 12, 
'Card đồ họa RTX 4070 Ti cho gaming 1440p', 
'GPU: NVIDIA RTX 4070 Ti\nVRAM: 12GB GDDR6X\nClock: 2610MHz\nTDP: 285W\nPorts: 3xDP, 1xHDMI', 1, 1),

(3, 'NVIDIA GeForce RTX 4060 Ti ASUS Dual', 'nvidia-rtx-4060-ti-asus', 'VGA-4060TI-ASUS', 'ASUS', 13990000, 12990000, 20, 
'Card đồ họa RTX 4060 Ti cho gaming 1080p/1440p', 
'GPU: NVIDIA RTX 4060 Ti\nVRAM: 8GB GDDR6\nClock: 2535MHz\nTDP: 160W\nPorts: 3xDP, 1xHDMI', 0, 1),

(3, 'AMD Radeon RX 7900 XTX Sapphire Nitro+', 'amd-rx-7900-xtx-sapphire', 'VGA-7900XTX-SAP', 'Sapphire', 29990000, 27990000, 10, 
'Card đồ họa AMD RX 7900 XTX hiệu năng cao', 
'GPU: AMD RX 7900 XTX\nVRAM: 24GB GDDR6\nClock: 2565MHz\nTDP: 355W\nPorts: 2xDP, 2xHDMI, 1xUSB-C', 1, 1),

(3, 'AMD Radeon RX 7800 XT PowerColor Red Devil', 'amd-rx-7800-xt-powercolor', 'VGA-7800XT-PC', 'PowerColor', 17990000, NULL, 15, 
'Card đồ họa AMD RX 7800 XT cho gaming 1440p', 
'GPU: AMD RX 7800 XT\nVRAM: 16GB GDDR6\nClock: 2475MHz\nTDP: 263W\nPorts: 2xDP, 2xHDMI', 0, 1);

-- =====================================================
-- SEED: products - RAM
-- =====================================================
INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `brand`, `price`, `sale_price`, `quantity`, `description`, `specifications`, `is_featured`, `is_active`) VALUES
(4, 'Corsair Vengeance RGB DDR5 32GB (2x16GB) 6000MHz', 'corsair-vengeance-rgb-ddr5-32gb', 'RAM-DDR5-32GB-6000', 'Corsair', 4990000, 4490000, 30, 
'RAM DDR5 32GB hiệu năng cao với RGB đẹp mắt', 
'Type: DDR5\nCapacity: 32GB (2x16GB)\nSpeed: 6000MHz\nCAS Latency: CL30\nRGB: Yes', 1, 1),

(4, 'G.Skill Trident Z5 RGB DDR5 32GB (2x16GB) 6400MHz', 'gskill-trident-z5-ddr5-32gb', 'RAM-DDR5-32GB-6400', 'G.Skill', 5490000, NULL, 25, 
'RAM DDR5 32GB tốc độ cao cho gaming và workstation', 
'Type: DDR5\nCapacity: 32GB (2x16GB)\nSpeed: 6400MHz\nCAS Latency: CL32\nRGB: Yes', 0, 1),

(4, 'Kingston Fury Beast DDR5 16GB (2x8GB) 5200MHz', 'kingston-fury-beast-ddr5-16gb', 'RAM-DDR5-16GB-5200', 'Kingston', 2490000, 2290000, 40, 
'RAM DDR5 16GB giá rẻ cho gaming phổ thông', 
'Type: DDR5\nCapacity: 16GB (2x8GB)\nSpeed: 5200MHz\nCAS Latency: CL40\nRGB: No', 0, 1),

(4, 'Corsair Vengeance RGB Pro DDR4 32GB (2x16GB) 3600MHz', 'corsair-vengeance-rgb-ddr4-32gb', 'RAM-DDR4-32GB-3600', 'Corsair', 2990000, 2690000, 35, 
'RAM DDR4 32GB phổ biến nhất cho gaming', 
'Type: DDR4\nCapacity: 32GB (2x16GB)\nSpeed: 3600MHz\nCAS Latency: CL18\nRGB: Yes', 1, 1),

(4, 'G.Skill Trident Z Neo DDR4 16GB (2x8GB) 3600MHz', 'gskill-trident-z-neo-ddr4-16gb', 'RAM-DDR4-16GB-3600', 'G.Skill', 1790000, NULL, 50, 
'RAM DDR4 16GB tối ưu cho AMD Ryzen', 
'Type: DDR4\nCapacity: 16GB (2x8GB)\nSpeed: 3600MHz\nCAS Latency: CL16\nRGB: Yes', 0, 1);

-- =====================================================
-- SEED: products - SSD
-- =====================================================
INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `brand`, `price`, `sale_price`, `quantity`, `description`, `specifications`, `is_featured`, `is_active`) VALUES
(6, 'Samsung 990 PRO 2TB NVMe Gen4', 'samsung-990-pro-2tb', 'SSD-990PRO-2TB', 'Samsung', 5990000, 5490000, 20, 
'SSD NVMe Gen4 hiệu năng cao nhất từ Samsung', 
'Capacity: 2TB\nInterface: NVMe PCIe 4.0 x4\nRead: 7450MB/s\nWrite: 6900MB/s\nForm Factor: M.2 2280', 1, 1),

(6, 'Samsung 980 PRO 1TB NVMe Gen4', 'samsung-980-pro-1tb', 'SSD-980PRO-1TB', 'Samsung', 2990000, 2690000, 30, 
'SSD NVMe Gen4 1TB tốc độ cao', 
'Capacity: 1TB\nInterface: NVMe PCIe 4.0 x4\nRead: 7000MB/s\nWrite: 5100MB/s\nForm Factor: M.2 2280', 1, 1),

(6, 'WD Black SN850X 1TB NVMe Gen4', 'wd-black-sn850x-1tb', 'SSD-SN850X-1TB', 'Western Digital', 2790000, NULL, 25, 
'SSD NVMe Gen4 gaming từ Western Digital', 
'Capacity: 1TB\nInterface: NVMe PCIe 4.0 x4\nRead: 7300MB/s\nWrite: 6300MB/s\nForm Factor: M.2 2280', 0, 1),

(6, 'Kingston KC3000 512GB NVMe Gen4', 'kingston-kc3000-512gb', 'SSD-KC3000-512GB', 'Kingston', 1490000, 1290000, 40, 
'SSD NVMe Gen4 512GB giá rẻ', 
'Capacity: 512GB\nInterface: NVMe PCIe 4.0 x4\nRead: 7000MB/s\nWrite: 3900MB/s\nForm Factor: M.2 2280', 0, 1);

-- =====================================================
-- SEED: products - Monitors
-- =====================================================
INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `brand`, `price`, `sale_price`, `quantity`, `description`, `specifications`, `is_featured`, `is_active`) VALUES
(11, 'ASUS ROG Swift PG27AQDM 27" OLED 240Hz', 'asus-rog-swift-pg27aqdm', 'MON-PG27AQDM', 'ASUS', 24990000, 22990000, 8, 
'Màn hình gaming OLED 1440p 240Hz tốt nhất', 
'Size: 27"\nResolution: 2560x1440 (QHD)\nPanel: OLED\nRefresh Rate: 240Hz\nResponse Time: 0.03ms', 1, 1),

(11, 'LG UltraGear 27GP950-B 27" 4K 160Hz', 'lg-ultragear-27gp950', 'MON-27GP950', 'LG', 17990000, 15990000, 12, 
'Màn hình gaming 4K 160Hz cho gaming cao cấp', 
'Size: 27"\nResolution: 3840x2160 (4K)\nPanel: Nano IPS\nRefresh Rate: 160Hz\nResponse Time: 1ms', 1, 1),

(11, 'Samsung Odyssey G7 32" Curved 240Hz', 'samsung-odyssey-g7-32', 'MON-G7-32', 'Samsung', 12990000, NULL, 15, 
'Màn hình gaming cong 1440p 240Hz', 
'Size: 32"\nResolution: 2560x1440 (QHD)\nPanel: VA Curved\nRefresh Rate: 240Hz\nResponse Time: 1ms', 0, 1),

(11, 'AOC 24G2 24" IPS 144Hz', 'aoc-24g2-24', 'MON-24G2', 'AOC', 3990000, 3490000, 25, 
'Màn hình gaming 1080p 144Hz giá rẻ', 
'Size: 24"\nResolution: 1920x1080 (FHD)\nPanel: IPS\nRefresh Rate: 144Hz\nResponse Time: 1ms', 0, 1);

-- =====================================================
-- SEED: products - Keyboards & Mouse
-- =====================================================
INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `brand`, `price`, `sale_price`, `quantity`, `description`, `specifications`, `is_featured`, `is_active`) VALUES
(12, 'Logitech G Pro X Mechanical Keyboard', 'logitech-g-pro-x', 'KB-GPRO-X', 'Logitech', 3490000, 2990000, 20, 
'Bàn phím cơ gaming chuyên nghiệp', 
'Type: Mechanical\nSwitch: GX Blue/Brown/Red\nLayout: TKL\nRGB: Yes\nConnection: USB', 0, 1),

(12, 'Corsair K70 RGB Pro Mechanical Keyboard', 'corsair-k70-rgb-pro', 'KB-K70-RGB', 'Corsair', 3990000, NULL, 18, 
'Bàn phím cơ gaming fullsize RGB', 
'Type: Mechanical\nSwitch: Cherry MX\nLayout: Fullsize\nRGB: Yes\nConnection: USB', 0, 1),

(13, 'Logitech G Pro X Superlight', 'logitech-g-pro-x-superlight', 'MOUSE-GPRO-SL', 'Logitech', 2990000, 2690000, 25, 
'Chuột gaming không dây nhẹ nhất', 
'Type: Wireless\nDPI: 25600\nWeight: 63g\nButtons: 5\nBattery: 70h', 1, 1),

(13, 'Razer DeathAdder V3 Pro', 'razer-deathadder-v3-pro', 'MOUSE-DAV3-PRO', 'Razer', 3290000, NULL, 20, 
'Chuột gaming không dây cao cấp', 
'Type: Wireless\nDPI: 30000\nWeight: 63g\nButtons: 5\nBattery: 90h', 0, 1);

-- =====================================================
-- SEED: banners
-- =====================================================
INSERT INTO `banners` (`title`, `image_path`, `link`, `display_order`, `is_active`) VALUES
('Banner Gaming PC Sale', 'banner1.jpg', '/category/pc-gaming', 1, 1),
('Banner CPU Intel Gen 13', 'banner2.jpg', '/category/cpu-bo-vi-xu-ly', 2, 1),
('Banner RTX 40 Series', 'banner3.jpg', '/category/vga-card-man-hinh', 3, 1),
('Banner DDR5 RAM', 'banner4.jpg', '/category/ram-bo-nho', 4, 1),
('Banner Gaming Monitor', 'banner5.jpg', '/category/man-hinh', 5, 1);

-- =====================================================
-- SEED: Sample order for testing
-- =====================================================
INSERT INTO `orders` (`user_id`, `order_code`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `subtotal`, `shipping_fee`, `total`, `status`) VALUES
(2, 'ORD-20260108-001', 'Nguyen Van A', 'customer1@email.com', '0912345678', '456 Le Loi, District 3, HCMC', 13990000, 50000, 14040000, 'pending');

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `product_image`, `price`, `quantity`, `subtotal`) VALUES
(1, 4, 'Intel Core i9-13900K', NULL, 13990000, 1, 13990000);

-- =====================================================
-- SEED: Sample reviews
-- =====================================================
INSERT INTO `reviews` (`product_id`, `user_id`, `rating`, `comment`, `is_approved`) VALUES
(4, 2, 5, 'CPU rất mạnh, chạy game mượt mà!', 1),
(7, 2, 5, 'Card đồ họa tuyệt vời, chơi game 4K mượt', 1),
(1, 3, 4, 'PC gaming tốt, giá hợp lý', 1);
