<?php
/**
 * بروزرسانی دیتابیس
 */
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance()->getConnection();

echo "<div dir='rtl' style='font-family:Tahoma;padding:30px;'>";
echo "<h2>بروزرسانی دیتابیس</h2>";

try {
    // اضافه کردن ستون category به جدول allocations
    $db->exec("ALTER TABLE allocations ADD COLUMN IF NOT EXISTS category VARCHAR(100) DEFAULT NULL AFTER description");
    echo "<p style='color:green;'>✅ ستون category به جدول allocations اضافه شد</p>";
} catch (Exception $e) {
    echo "<p>ستون category: " . $e->getMessage() . "</p>";
}

try {
    // بررسی جدول daily_activities
    $db->exec("CREATE TABLE IF NOT EXISTS daily_activities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        action VARCHAR(300) NOT NULL,
        description TEXT DEFAULT NULL,
        activity_date VARCHAR(20) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p style='color:green;'>✅ جدول daily_activities بررسی شد</p>";
} catch (Exception $e) {
    echo "<p>daily_activities: " . $e->getMessage() . "</p>";
}

// اضافه کردن ستون receiver_id به missions برای انتخاب دریافت‌کننده واریز
try {
    $db->exec("ALTER TABLE missions ADD COLUMN IF NOT EXISTS receiver_id INT DEFAULT NULL AFTER expense_amount");
    echo "<p style='color:green;'>✅ ستون receiver_id به جدول missions اضافه شد</p>";
} catch (Exception $e) {
    echo "<p>receiver_id: " . $e->getMessage() . "</p>";
}

echo "<hr><p style='color:red;'>بعد از اجرا این فایل را حذف کنید!</p>";
echo "<p><a href='dashboard'>برو به داشبورد</a></p>";
echo "</div>";