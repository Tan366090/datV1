<?php
require_once '../../config/config.php';
require_once '../../controllers/AttendanceController.php';

$attendanceController = new AttendanceController($db);

// Get filters from request
$filters = [
    'start_date' => $_GET['start_date'] ?? '',
    'end_date' => $_GET['end_date'] ?? '',
    'employee_code' => $_GET['employee_code'] ?? '',
    'full_name' => $_GET['full_name'] ?? ''
];

// Get attendance list
$attendanceList = $attendanceController->getAttendanceList($filters);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý chấm công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid py-4">
        <h2 class="mb-4">Quản lý chấm công</h2>
        
        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" name="start_date" value="<?= $filters['start_date'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" name="end_date" value="<?= $filters['end_date'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mã nhân viên</label>
                        <input type="text" class="form-control" name="employee_code" value="<?= $filters['employee_code'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Họ tên</label>
                        <input type="text" class="form-control" name="full_name" value="<?= $filters['full_name'] ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Làm mới
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Attendance List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Mã NV</th>
                                <th>Họ tên</th>
                                <th>Giờ vào</th>
                                <th>Giờ ra</th>
                                <th>Thời gian làm việc</th>
                                <th>Trạng thái</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceList as $attendance): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($attendance['attendance_date'])) ?></td>
                                <td><?= htmlspecialchars($attendance['employee_code']) ?></td>
                                <td><?= htmlspecialchars($attendance['full_name']) ?></td>
                                <td><?= $attendance['check_in_time'] ? date('H:i', strtotime($attendance['check_in_time'])) : '-' ?></td>
                                <td><?= $attendance['check_out_time'] ? date('H:i', strtotime($attendance['check_out_time'])) : '-' ?></td>
                                <td><?= $attendance['work_duration_hours'] ? $attendance['work_duration_hours'] . ' giờ' : '-' ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch ($attendance['attendance_symbol']) {
                                        case 'P':
                                            $statusClass = 'success';
                                            $statusText = 'Có mặt';
                                            break;
                                        case 'A':
                                            $statusClass = 'danger';
                                            $statusText = 'Vắng mặt';
                                            break;
                                        case 'L':
                                            $statusClass = 'warning';
                                            $statusText = 'Nghỉ phép';
                                            break;
                                        case 'WFH':
                                            $statusClass = 'info';
                                            $statusText = 'Làm việc từ xa';
                                            break;
                                        default:
                                            $statusClass = 'secondary';
                                            $statusText = 'Chưa xác định';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td><?= htmlspecialchars($attendance['notes'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 