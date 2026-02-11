<?php
/**
 * فایل عیب‌یابی
 * بعد از رفع مشکل حذف کنید
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__ . '/');

echo "<div dir='rtl' style='font-family:Tahoma;padding:30px;max-width:900px;margin:auto;'>";
echo "<h1 style='color:#e94560;'>🔧 عیب‌یابی سیستم مدیریت مالی</h1>";
echo "<hr>";

// ۱. بررسی PHP
echo "<h3>۱. نسخه PHP</h3>";
echo "<p>نسخه: <strong>" . phpversion() . "</strong>";
if (version_compare(phpversion(), '7.0', '>=')) {
    echo " ✅</p>";
} else {
    echo " ❌ نیاز به PHP 7.0 یا بالاتر</p>";
}

// ۲. بررسی PDO
echo "<h3>۲. PDO MySQL</h3>";
if (extension_loaded('pdo_mysql')) {
    echo "<p>PDO MySQL: <strong>فعال</strong> ✅</p>";
} else {
    echo "<p>PDO MySQL: <strong>غیرفعال</strong> ❌</p>";
}

// ۳. بررسی mod_rewrite
echo "<h3>۳. Apache mod_rewrite</h3>";
if (in_array('mod_rewrite', apache_get_modules())) {
    echo "<p>mod_rewrite: <strong>فعال</strong> ✅</p>";
} else {
    echo "<p>mod_rewrite: <strong>نامشخص</strong> ⚠️ (ممکن است فعال باشد)</p>";
}

// ۴. بررسی دیتابیس
echo "<h3>۴. اتصال دیتابیس</h3>";
try {
    require_once BASE_PATH . 'config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p>اتصال به دیتابیس: <strong>موفق</strong> ✅</p>";
    
    // بررسی جداول
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>تعداد جداول: <strong>" . count($tables) . "</strong></p>";
    
    $requiredTables = ['users', 'employees', 'allocations', 'invoices', 'missions', 
                       'mission_members', 'deposits', 'debts', 'debt_payments', 
                       'notifications', 'daily_activities', 'settings'];
    
    foreach ($requiredTables as $table) {
        if (in_array($table, $tables)) {
            $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "<p style='margin-right:20px;'>✅ $table ($count رکورد)</p>";
        } else {
            echo "<p style='margin-right:20px;color:red;'>❌ $table (وجود ندارد!)</p>";
        }
    }
    
    // بررسی کاربر
    echo "<h3>۵. بررسی کاربر</h3>";
    $stmt = $db->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ?");
    $stmt->execute(['Erfanaki']);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p>کاربر Erfanaki: <strong>موجود</strong> ✅</p>";
        echo "<p style='margin-right:20px;'>نام: {$user['full_name']}</p>";
        echo "<p style='margin-right:20px;'>نقش: {$user['role']}</p>";
        
        if (password_verify('1234', $user['password'])) {
            echo "<p style='margin-right:20px;'>رمز 1234: <strong style='color:green;'>صحیح ✅</strong></p>";
        } else {
            echo "<p style='margin-right:20px;'>رمز 1234: <strong style='color:red;'>اشتباه ❌</strong></p>";
            echo "<p style='margin-right:20px;'>برای رفع: <a href='fix_password.php' style='color:blue;'>کلیک کنید</a></p>";
        }
    } else {
        echo "<p style='color:red;'>❌ کاربر Erfanaki وجود ندارد!</p>";
        echo "<p>برای ایجاد: <a href='fix_password.php' style='color:blue;'>کلیک کنید</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ خطا: " . $e->getMessage() . "</p>";
}

// ۵. بررسی فایل‌ها
echo "<h3>۶. بررسی فایل‌های اصلی</h3>";
$files = [
    '.htaccess',
    'index.php',
    'config/database.php',
    'helpers/functions.php',
    'controllers/AuthController.php',
    'controllers/DashboardController.php',
    'controllers/EmployeeController.php',
    'controllers/AllocationController.php',
    'controllers/InvoiceController.php',
    'controllers/MissionController.php',
    'controllers/DepositController.php',
    'controllers/ReportController.php',
    'controllers/NotificationController.php',
    'controllers/DailyActivityController.php',
    'controllers/ProfileController.php',
    'controllers/AIController.php',
    'controllers/AboutController.php',
    'models/User.php',
    'models/Employee.php',
    'models/Allocation.php',
    'models/Invoice.php',
    'models/Mission.php',
    'models/Deposit.php',
    'models/Notification.php',
    'models/DailyActivity.php',
    'views/layouts/main.php',
    'views/auth/login.php',
    'views/dashboard/index.php',
    'views/employees/index.php',
    'views/employees/show.php',
    'views/allocations/index.php',
    'views/invoices/index.php',
    'views/missions/index.php',
    'views/missions/show.php',
    'views/deposits/index.php',
    'views/reports/index.php',
    'views/notifications/index.php',
    'views/daily-activity/index.php',
    'views/profile/index.php',
    'views/ai/index.php',
    'views/about/index.php',
    'views/errors/404.php',
    'assets/css/style.css',
    'assets/js/app.js',
];

$missing = 0;
foreach ($files as $file) {
    $path = BASE_PATH . $file;
    if (file_exists($path)) {
        $size = filesize($path);
        echo "<p style='margin-right:20px;'>✅ $file <span style='color:#888;'>($size bytes)</span></p>";
    } else {
        echo "<p style='margin-right:20px;color:red;'>❌ $file <strong>(وجود ندارد!)</strong></p>";
        $missing++;
    }
}

if ($missing > 0) {
    echo "<p style='color:red;font-weight:bold;'>⚠️ $missing فایل وجود ندارد!</p>";
} else {
    echo "<p style='color:green;font-weight:bold;'>✅ تمام فایل‌ها موجود هستند</p>";
}

// ۶. بررسی پوشه‌های آپلود
echo "<h3>۷. پوشه‌های آپلود</h3>";
$uploadDirs = [
    'assets/uploads/',
    'assets/uploads/allocations/',
    'assets/uploads/invoices/',
    'assets/uploads/missions/',
    'assets/uploads/avatars/',
];

foreach ($uploadDirs as $dir) {
    $path = BASE_PATH . $dir;
    if (is_dir($path)) {
        $writable = is_writable($path) ? 'قابل نوشتن ✅' : 'غیرقابل نوشتن ❌';
        echo "<p style='margin-right:20px;'>✅ $dir ($writable)</p>";
    } else {
        // ایجاد خودکار
        if (mkdir($path, 0755, true)) {
            echo "<p style='margin-right:20px;color:green;'>✅ $dir (ایجاد شد)</p>";
        } else {
            echo "<p style='margin-right:20px;color:red;'>❌ $dir (ایجاد نشد!)</p>";
        }
    }
}

// ۷. بررسی Session
echo "<h3>۸. Session</h3>";
echo "<p>Session ID: " . session_id() . "</p>";
if (isset($_SESSION['user_id'])) {
    echo "<p>لاگین شده: بله (User ID: {$_SESSION['user_id']})</p>";
} else {
    echo "<p>لاگین شده: خیر</p>";
}

// ۸. BASE_URL
echo "<h3>۹. مسیرها</h3>";
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = rtrim($scriptName, '/') . '/';
echo "<p>BASE_URL: <strong>$baseUrl</strong></p>";
echo "<p>BASE_PATH: <strong>" . BASE_PATH . "</strong></p>";
echo "<p>DOCUMENT_ROOT: <strong>" . $_SERVER['DOCUMENT_ROOT'] . "</strong></p>";

// لینک‌های تست
echo "<h3>۱۰. تست لینک‌ها</h3>";
$links = [
    'auth/login' => 'صفحه ورود',
    'dashboard' => 'داشبورد',
    'employees' => 'کارمندان',
    'allocations' => 'تخصیص‌ها',
    'invoices' => 'فاکتورها',
    'missions' => 'ماموریت‌ها',
    'deposits' => 'واریزی‌ها',
    'reports' => 'گزارش‌ها',
    'notifications' => 'اعلان‌ها',
    'daily-activity' => 'عملکرد روزانه',
    'profile' => 'پروفایل',
    'ai' => 'هوش مصنوعی',
    'about' => 'درباره',
];

foreach ($links as $link => $title) {
    echo "<p style='margin-right:20px;'><a href='{$baseUrl}{$link}' style='color:#3498db;' target='_blank'>🔗 $title ({$baseUrl}{$link})</a></p>";
}

echo "<hr>";
echo "<p style='color:red;font-weight:bold;'>⚠️ بعد از رفع مشکلات، فایل‌های debug.php و fix_password.php را حذف کنید!</p>";
echo "</div>";