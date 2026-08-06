<?php

namespace App\Support;

use App\Models\Staff;

/**
 * Trait helper chung cho các controller trang Hướng dẫn viên.
 *
 * Luồng theo sơ đồ:
 *   1. Đăng nhập HDV
 *   2. Lấy HDV đang đăng nhập (ưu tiên Auth::user()->hdv_id)
 *   3. Truy vấn DATABASE (staff_assignments JOIN departures)
 *   4. Đổ ra "Tour được phân công"
 */
trait ResolvesActiveHdv
{
    protected function modelStaff(): Staff
    {
        if (!isset($this->modelStaff) || !$this->modelStaff instanceof Staff) {
            $this->modelStaff = new Staff();
        }
        return $this->modelStaff;
    }

    /**
     * Lấy HDV_id của người dùng hiện tại theo thứ tự ưu tiên:
     * 1. Tài khoản HDV đã bind sẵn hdv_id (Auth::user()['hdv_id'])
     * 2. Tham số ?hdv_id=xxx trên URL (cho admin / HDV switch)
     * 3. Session $_SESSION['hdv_id'] (đã được lưu trước đó, phải hợp lệ)
     * 4. Fallback: tìm HDV active đầu tiên, nếu không thì dòng đầu, cuối cùng = 1
     */
    protected function resolveActiveHdvId(): int
    {
        $staff = $this->modelStaff();

        // 1. Nếu đăng nhập = HDV đã bind = LUÔN ưu tiên auth.hdv_id (tránh session sai)
        if (Auth::hasBoundHdv()) {
            $authId = (int) (Auth::user()['hdv_id'] ?? 0);
            if ($authId > 0 && $staff->findById($authId)) {
                $_SESSION['hdv_id'] = $authId;
                return $authId;
            }
            if ($authId > 0) {
                $_SESSION['hdv_id'] = $authId;
                return $authId;
            }
        }

        // 2. Admin / HDV chưa bind => cho đổi HDV bằng ?hdv_id=xxx trên URL
        if (isset($_GET['hdv_id']) && (int)$_GET['hdv_id'] > 0) {
            $requested = (int)$_GET['hdv_id'];
            if ($staff->findById($requested)) {
                $_SESSION['hdv_id'] = $requested;
                return $requested;
            }
        }

        // 3. Session hiện tại - chỉ dùng nếu hợp lệ (tồn tại trong bảng hdv)
        $sessionId = isset($_SESSION['hdv_id']) ? (int)$_SESSION['hdv_id'] : 0;
        if ($sessionId > 0 && $staff->findById($sessionId)) {
            return $sessionId;
        }

        // 4. Fallback
        $allStaff = $staff->getAll();
        $fallback = 1;
        foreach ($allStaff as $s) {
            if (($s['Status'] ?? null) === 'active') {
                $fallback = (int)$s['HDV_id'];
                break;
            }
        }
        if ($fallback === 1 && !empty($allStaff)) {
            $fallback = (int)$allStaff[0]['HDV_id'];
        }

        $_SESSION['hdv_id'] = $fallback;
        return $fallback;
    }

    /**
     * Lấy thông tin record HDV hiện tại (dùng cho sidebar, header, …)
     */
    protected function resolveActiveHdv(): ?array
    {
        $id = $this->resolveActiveHdvId();
        $staff = $this->modelStaff();
        $row = $staff->findById($id);
        if ($row) {
            return $row;
        }
        $all = $staff->getAll();
        if (!empty($all)) {
            return $all[0];
        }
        return null;
    }

    /**
     * Lấy danh sách tất cả HDV cho dropdown "Đang xem" (sidebar)
     * Theo sơ đồ: nếu admin hoặc HDV chưa bind mới được đổi; HDV bind sẵn chỉ xem mình.
     */
    protected function resolveAllViewableHdv(): array
    {
        $staff = $this->modelStaff();
        if (Auth::canSwitchHdv()) {
            return $staff->getAll();
        }
        $active = $this->resolveActiveHdv();
        return $active ? [$active] : [];
    }
}
