<?php
/**
 * کنترلر اعلان‌ها
 */
require_once BASE_PATH . 'models/Notification.php';
require_once BASE_PATH . 'models/Employee.php';

class NotificationController {
    private $model;
    private $employeeModel;
    
    public function __construct() {
        $this->model = new Notification();
        $this->employeeModel = new Employee();
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // ایجاد یادآوری‌های بدهی
        $this->model->createDebtReminders($userId);
        
        // POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = post('action');
            
            if ($action === 'create') {
                $data = [
                    'user_id' => $userId,
                    'title' => post('title'),
                    'message' => post('message'),
                    'type' => post('type', 'note'),
                    'remind_date' => toEnglishNumber(post('remind_date')),
                    'related_employee_id' => post('related_employee_id') ?: null
                ];
                
                if (empty($data['title'])) {
                    if (isAjax()) jsonResponse(['success' => false, 'message' => 'عنوان الزامی است']);
                    setFlash('error', 'عنوان الزامی است');
                    redirect('notifications');
                    return;
                }
                
                $id = $this->model->create($data);
                if ($id) {
                    logActivity($userId, 'ثبت اعلان', $data['title']);
                    if (isAjax()) jsonResponse(['success' => true, 'message' => 'اعلان ثبت شد']);
                    setFlash('success', 'اعلان با موفقیت ثبت شد');
                }
                redirect('notifications');
                return;
            }
            
            if ($action === 'mark_read' && isAjax()) {
                $id = post('id');
                $this->model->markAsRead($id);
                jsonResponse(['success' => true]);
            }
            
            if ($action === 'mark_done' && isAjax()) {
                $id = post('id');
                $this->model->markAsDone($id);
                jsonResponse(['success' => true, 'message' => 'انجام شد']);
            }
            
            if ($action === 'delete' && isAjax()) {
                $id = post('id');
                $this->model->delete($id);
                jsonResponse(['success' => true, 'message' => 'حذف شد']);
            }
        }
        
        $notifications = $this->model->getByUser($userId);
        $todayReminders = $this->model->getTodayReminders($userId);
        $employees = $this->employeeModel->getAll();
        $unreadCount = $this->model->getUnreadCount($userId);
        
        $data = [
            'pageTitle' => 'اعلان‌ها و یادآوری',
            'currentPage' => 'notifications',
            'notifications' => $notifications,
            'todayReminders' => $todayReminders,
            'employees' => $employees,
            'unreadCount' => $unreadCount
        ];
        
        loadView('notifications/index', $data);
    }
}