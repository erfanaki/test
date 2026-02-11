-- ایجاد دیتابیس
CREATE DATABASE IF NOT EXISTS financial_management 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE financial_management;

-- ============================
-- جدول کاربران
-- ============================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(200) NOT NULL,
    personnel_code VARCHAR(50) DEFAULT NULL,
    role ENUM('admin', 'manager', 'accountant') DEFAULT 'accountant',
    avatar VARCHAR(500) DEFAULT NULL,
    is_online TINYINT(1) DEFAULT 0,
    last_activity DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول کارمندان
-- ============================
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(200) NOT NULL,
    personnel_code VARCHAR(50) NOT NULL UNIQUE,
    position VARCHAR(200) DEFAULT NULL,
    department VARCHAR(200) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول تخصیص‌ها
-- ============================
CREATE TABLE IF NOT EXISTS allocations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    allocation_number VARCHAR(100) NOT NULL,
    issue_date VARCHAR(20) NOT NULL COMMENT 'تاریخ شمسی',
    issue_date_gregorian DATE DEFAULT NULL,
    title VARCHAR(500) NOT NULL,
    amount BIGINT NOT NULL DEFAULT 0,
    used_amount BIGINT NOT NULL DEFAULT 0,
    remaining_amount BIGINT NOT NULL DEFAULT 0,
    status ENUM('active', 'settled', 'cancelled') DEFAULT 'active',
    description TEXT DEFAULT NULL,
    image VARCHAR(500) DEFAULT NULL,
    year_jalali INT DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول فاکتورها
-- ============================
CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(100) NOT NULL,
    invoice_date VARCHAR(20) NOT NULL COMMENT 'تاریخ شمسی',
    invoice_date_gregorian DATE DEFAULT NULL,
    amount BIGINT NOT NULL DEFAULT 0,
    buyer_id INT DEFAULT NULL COMMENT 'شناسه خریدار از جدول کارمندان',
    buyer_name VARCHAR(200) DEFAULT NULL,
    category VARCHAR(200) DEFAULT NULL,
    payment_location VARCHAR(300) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    status ENUM('pending', 'settled', 'cancelled') DEFAULT 'pending',
    image VARCHAR(500) DEFAULT NULL,
    allocation_id INT DEFAULT NULL,
    year_jalali INT DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (allocation_id) REFERENCES allocations(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول ماموریت‌ها
-- ============================
CREATE TABLE IF NOT EXISTS missions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mission_date VARCHAR(20) NOT NULL COMMENT 'تاریخ شمسی',
    mission_date_gregorian DATE DEFAULT NULL,
    send_date VARCHAR(20) DEFAULT NULL COMMENT 'تاریخ ارسال شمسی',
    send_date_gregorian DATE DEFAULT NULL,
    decree_number VARCHAR(100) DEFAULT NULL COMMENT 'شماره حکم',
    location VARCHAR(300) NOT NULL COMMENT 'محل ماموریت',
    days_count INT NOT NULL DEFAULT 1 COMMENT 'تعداد روز',
    deposit_amount BIGINT NOT NULL DEFAULT 0 COMMENT 'مبلغ واریز شده',
    expense_amount BIGINT NOT NULL DEFAULT 0 COMMENT 'مقدار هزینه شده',
    remaining_amount BIGINT NOT NULL DEFAULT 0 COMMENT 'مانده',
    description TEXT DEFAULT NULL,
    image VARCHAR(500) DEFAULT NULL,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    year_jalali INT DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول نفرات ماموریت
-- ============================
CREATE TABLE IF NOT EXISTS mission_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mission_id INT NOT NULL,
    employee_id INT DEFAULT NULL,
    guest_name VARCHAR(200) DEFAULT NULL,
    is_guest TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mission_id) REFERENCES missions(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول واریزی‌ها
-- ============================
CREATE TABLE IF NOT EXISTS deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deposit_date VARCHAR(20) NOT NULL COMMENT 'تاریخ شمسی',
    deposit_date_gregorian DATE DEFAULT NULL,
    employee_id INT NOT NULL,
    amount BIGINT NOT NULL DEFAULT 0,
    deposit_type ENUM('mission', 'purchase', 'other') DEFAULT 'other' COMMENT 'نوع واریز',
    description TEXT DEFAULT NULL,
    mission_id INT DEFAULT NULL,
    year_jalali INT DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (mission_id) REFERENCES missions(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول بدهی‌ها
-- ============================
CREATE TABLE IF NOT EXISTS debts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    amount BIGINT NOT NULL DEFAULT 0 COMMENT 'مبلغ بدهی',
    paid_amount BIGINT NOT NULL DEFAULT 0 COMMENT 'مبلغ پرداخت شده',
    remaining_amount BIGINT NOT NULL DEFAULT 0 COMMENT 'مانده بدهی',
    source_type ENUM('deposit', 'invoice', 'mission', 'other') NOT NULL,
    source_id INT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    status ENUM('pending', 'partial', 'paid') DEFAULT 'pending',
    year_jalali INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول پرداخت‌های بدهی
-- ============================
CREATE TABLE IF NOT EXISTS debt_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    debt_id INT NOT NULL,
    employee_id INT NOT NULL,
    amount BIGINT NOT NULL DEFAULT 0,
    payment_date VARCHAR(20) NOT NULL,
    payment_date_gregorian DATE DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (debt_id) REFERENCES debts(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول اعلان‌ها
-- ============================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(300) NOT NULL,
    message TEXT DEFAULT NULL,
    type ENUM('reminder', 'debt', 'note', 'system') DEFAULT 'note',
    remind_date VARCHAR(20) DEFAULT NULL COMMENT 'تاریخ یادآوری شمسی',
    remind_date_gregorian DATE DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    is_done TINYINT(1) DEFAULT 0,
    related_employee_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (related_employee_id) REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول فعالیت‌های روزانه
-- ============================
CREATE TABLE IF NOT EXISTS daily_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(300) NOT NULL,
    description TEXT DEFAULT NULL,
    activity_date VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- جدول تنظیمات
-- ============================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================
-- درج کاربر پیش‌فرض
-- ============================
INSERT INTO users (username, password, full_name, personnel_code, role) VALUES
('Erfanaki', '$2y$10$8K1p/a0dL1LXMIgoEDHm3.GmOgqj3xRVvQTDXGjmL7YHiJi76k8e2', 'عرفان اکی', '1001', 'admin');
-- رمز عبور: 1234

-- ============================
-- درج چند کارمند نمونه
-- ============================
INSERT INTO employees (full_name, personnel_code, position, department) VALUES
('علی احمدی', '2001', 'کارشناس مالی', 'مالی'),
('محمد رضایی', '2002', 'کارشناس اداری', 'اداری'),
('حسین محمدی', '2003', 'کارشناس فنی', 'فنی'),
('رضا کریمی', '2004', 'حسابدار', 'مالی'),
('امیر حسینی', '2005', 'کارشناس IT', 'فناوری اطلاعات');

-- ============================
-- ایندکس‌ها
-- ============================
CREATE INDEX idx_invoices_year ON invoices(year_jalali);
CREATE INDEX idx_invoices_buyer ON invoices(buyer_id);
CREATE INDEX idx_allocations_year ON allocations(year_jalali);
CREATE INDEX idx_missions_year ON missions(year_jalali);
CREATE INDEX idx_deposits_employee ON deposits(employee_id);
CREATE INDEX idx_deposits_year ON deposits(year_jalali);
CREATE INDEX idx_debts_employee ON debts(employee_id);
CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_daily_activities_user ON daily_activities(user_id);
CREATE INDEX idx_daily_activities_date ON daily_activities(activity_date);