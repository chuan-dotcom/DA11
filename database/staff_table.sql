-- Bảng chi tiết HDV (Hướng dẫn viên / Quản lý nhân sự) theo thiết kế báo cáo
CREATE TABLE IF NOT EXISTS hdv (
    HDV_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Mã định danh duy nhất cho Hướng dẫn viên',
    Hoten VARCHAR(50) NOT NULL COMMENT 'Họ và tên đầy đủ',
    Ngaysinh DATE NULL COMMENT 'Ngày sinh',
    Gioitinh VARCHAR(10) NULL COMMENT 'Giới tính',
    Lienhe VARCHAR(150) NULL COMMENT 'Thông tin liên hệ (SĐT hoặc email)',
    Ngonngu VARCHAR(50) NULL COMMENT 'Ngôn ngữ hướng dẫn',
    Diachi VARCHAR(255) NULL COMMENT 'Địa chỉ cư trú',
    chungchiHDV VARCHAR(50) NULL COMMENT 'Số hoặc loại chứng chỉ HDV',
    Kinhnghiem INT NULL COMMENT 'Số năm kinh nghiệm',
    Ngaybatdaulam DATE NULL COMMENT 'Ngày bắt đầu làm việc',
    Trangthaisuckhoe TEXT NULL COMMENT 'Tình trạng sức khỏe',
    Ghichunoibo TEXT NULL COMMENT 'Ghi chú nội bộ',
    Diemdanhgia DECIMAL(3,1) NULL DEFAULT 0.0 COMMENT 'Điểm đánh giá trung bình (VD: 4.8)',
    Nhanxetdanhgia TEXT NULL COMMENT 'Nhận xét chi tiết',
    HDV_group_id INT NULL COMMENT 'ID nhóm HDV',
    Status ENUM('active', 'inactive', 'on_leave') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái làm việc',
    created_at DATETIME NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu HDV
INSERT INTO hdv (Hoten, Ngaysinh, Gioitinh, Lienhe, Ngonngu, Diachi, chungchiHDV, Kinhnghiem, Ngaybatdaulam, Trangthaisuckhoe, Ghichunoibo, Diemdanhgia, Nhanxetdanhgia, HDV_group_id, Status, created_at) VALUES
('Nguyễn Văn An', '1990-05-15', 'Nam', '0901234567 - an.nguyen@email.com', 'Tiếng Anh, Tiếng Việt', '123 Nguyễn Huệ, Q.1, TP.HCM', 'HDV Quốc tế #9921', 5, '2020-01-15', 'Tốt, thể lực đảm bảo leo núi', 'Nhiệt tình, dẫn tour nội địa & quốc tế tốt', 4.8, 'Khách hàng phản hồi rất tích cực về thái độ phục vụ.', 1, 'active', NOW()),
('Trần Thị Bích', '1995-08-20', 'Nữ', '0902345678 - bich.tran@email.com', 'Tiếng Trung, Tiếng Việt', '456 Lê Lợi, Q.3, TP.HCM', 'HDV Nội địa #4412', 3, '2022-03-01', 'Bình thường', 'Phù hợp các tour nghỉ dưỡng văn hóa', 4.5, 'Nhiệt tình chăm sóc đoàn khách gia đình.', 2, 'active', NOW()),
('Lê Hoàng Cường', '1988-12-10', 'Nam', '0903456789 - cuong.le@email.com', 'Tiếng Nhật, Tiếng Anh', '789 Trần Hưng Đạo, Q.5, TP.HCM', 'HDV Quốc tế #8820', 8, '2019-06-20', 'Tốt', 'Chuyên tuyến tour Nhật Bản và Đông Nam Á', 4.9, 'Rất chuyên nghiệp, nhiều kinh nghiệm xử lý sự cố.', 1, 'active', NOW()),
('Phạm Minh Đức', '1993-03-25', 'Nam', '0904567890 - duc.pham@email.com', 'Tiếng Hàn', '321 Hai Bà Trưng, Q.1, TP.HCM', 'HDV Nội địa #5123', 2, '2023-01-10', 'Tốt', 'Đang học thêm tiếng Anh nâng cao', 4.2, 'Năng nổ, hòa đồng.', 2, 'on_leave', NOW()),
('Hoàng Thị Em', '1997-07-30', 'Nữ', '0905678901 - em.hoang@email.com', 'Tiếng Pháp', '654 Võ Văn Tần, Q.3, TP.HCM', 'HDV Quốc tế #3301', 4, '2021-09-05', 'Bình thường', 'Đã hết hạn hợp đồng', 4.0, 'Hoàn thành tốt các công việc được giao.', 3, 'inactive', NOW());
    