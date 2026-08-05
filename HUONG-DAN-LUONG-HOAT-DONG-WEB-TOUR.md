# Luồng Hoạt Động Của Website Tour Du Lịch

## 1. Mục đích của hệ thống

Theo code hiện tại, đây là **hệ thống quản lý tour du lịch nội bộ**.  
Trang web này không phải kiểu website bán tour đầy đủ cho khách ngoài truy cập tự do từ trang chủ, mà tập trung vào 3 nhóm người dùng chính:

- `Người dùng thường`
- `Quản trị viên (admin)`
- `Hướng dẫn viên (HDV)`

Ngoài ra có một trang công khai để xem nhanh chi tiết tour qua mã QR:

- `/tour/{id}`

## 2. Các vai trò đang có trong hệ thống

### Người dùng thường
- Có thể đăng ký tài khoản
- Có thể đăng nhập
- Có thể xem trang tài khoản của mình
- Hiện tại **chưa có luồng tự đặt tour online trực tiếp trên giao diện người dùng**

### Admin
- Quản lý toàn bộ hệ thống
- Quản lý tour, booking, lịch khởi hành, tài khoản, HDV, phân công nhân sự, nhật ký tour, dịch vụ

### HDV
- Đăng nhập vào cổng HDV riêng
- Xem tour được phân công
- Xem danh sách khách
- Check-in khách
- Xem lịch trình
- Viết nhật ký tour

## 3. Luồng sử dụng từ góc nhìn người dùng thường

### Bước 1: Đăng ký tài khoản
Người dùng vào:

- `/auth/register`

Tại đây người dùng nhập:

- Họ tên
- Email
- Số điện thoại
- Mật khẩu
- Xác nhận mật khẩu

Sau khi đăng ký thành công:

- hệ thống tạo tài khoản mới
- role mặc định là `user`
- tự đăng nhập vào hệ thống
- chuyển sang trang tài khoản

### Bước 2: Đăng nhập
Người dùng vào:

- `/auth/login`

Nhập:

- email
- mật khẩu

Nếu đúng:

- hệ thống tạo session đăng nhập
- chuyển đến đúng khu vực theo quyền

Cụ thể:

- `admin` -> vào `/admin/dashboard`
- `hdv` -> vào `/hdv/dashboard`
- `user` -> vào `/auth/account`

### Bước 3: Xem trang tài khoản
Sau khi đăng nhập, người dùng thường vào:

- `/auth/account`

Trang này hiển thị:

- họ tên
- email
- vai trò tài khoản

Nếu là người dùng thường thì hiện tại luồng dừng ở đây.  
Tức là người dùng đã có tài khoản nhưng **chưa có giao diện tự xem danh sách tour, đặt tour, thanh toán hay theo dõi booking riêng**.

### Bước 4: Đăng xuất
Người dùng bấm:

- `/auth/logout`

Hệ thống xóa session và quay về trang đăng nhập.

## 4. Luồng công khai khi quét mã QR tour

Hệ thống có một trang công khai:

- `/tour/{id}`

Mục đích:

- cho người dùng mở nhanh chi tiết tour từ QR

Trang này hiển thị:

- ảnh tour
- tên tour
- giá
- danh mục
- thời lượng
- trạng thái
- mô tả

Lưu ý:

- đây chỉ là trang xem thông tin
- chưa có nút đặt tour trực tuyến trong luồng hiện tại

## 5. Luồng hoạt động của admin

Admin là người vận hành chính của website.

### 5.1. Đăng nhập admin
Admin đăng nhập bằng tài khoản có role `admin`.

Sau khi đăng nhập:

- hệ thống tự chuyển vào `/admin/dashboard`

### 5.2. Dashboard quản trị
Tại dashboard, admin xem nhanh:

- tổng khách hàng
- số booking
- tour đang mở
- doanh thu
- số booking hoàn thành

Đây là màn hình tổng quan để admin nắm tình hình hoạt động.

### 5.3. Quản lý tài khoản
Admin vào khu:

- `/admin/users`

Tại đây admin có thể:

- tạo tài khoản mới
- sửa tài khoản
- xóa tài khoản
- phân quyền `user`, `hdv`, `admin`

Với tài khoản `hdv`, admin có thể:

- tạo kiểu tài khoản HDV riêng
- hoặc tạo tài khoản HDV dùng chung

### 5.4. Quản lý danh mục tour
Admin vào:

- `/admin/tour-categories`

Chức năng:

- thêm danh mục
- sửa danh mục
- xóa danh mục

Ví dụ: tour biển, tour núi, tour nghỉ dưỡng...

### 5.5. Quản lý tour
Admin vào:

- `/admin/tours`

Chức năng:

- thêm tour mới
- cập nhật tour
- xóa tour
- xem chi tiết tour
- xem danh sách người tham gia của tour

Thông tin tour thường gồm:

- tên tour
- danh mục
- giá
- thời lượng
- mô tả
- ảnh
- trạng thái mở / ẩn

### 5.6. Quản lý booking
Admin vào:

- `/admin/bookings`

Đây là nơi admin tạo và quản lý các booking cho khách.

Theo code hiện tại, booking đang được xử lý chủ yếu từ phía admin, nghĩa là:

- hệ thống nội bộ ghi nhận khách đặt
- admin cập nhật thông tin booking
- admin theo dõi trạng thái booking

### 5.7. Quản lý nhân sự HDV
Admin vào:

- `/admin/staff`

Chức năng:

- thêm hồ sơ hướng dẫn viên
- sửa thông tin HDV
- xem chi tiết HDV
- xóa HDV

Đây là hồ sơ nghiệp vụ của HDV, khác với tài khoản đăng nhập.

### 5.8. Quản lý đợt khởi hành
Admin vào:

- `/admin/departures`

Chức năng:

- tạo chuyến khởi hành cho từng tour
- nhập ngày đi, ngày về
- điểm hẹn
- giờ hẹn
- phương tiện

Đây là bước biến một tour tổng quát thành một chuyến đi cụ thể.

### 5.9. Phân công HDV cho tour
Admin vào:

- `/admin/staff-assignments`

Chức năng:

- gán HDV vào từng đợt khởi hành
- gán vai trò cho người tham gia chuyến đi

Sau bước này, tour mới xuất hiện trong cổng HDV.

### 5.10. Quản lý danh sách khách của đoàn
Admin vào:

- `/admin/guest-groups`

Chức năng:

- xem nhóm khách theo booking / đoàn
- thêm khách vào danh sách
- sửa khách
- xóa khách
- check-in / hủy check-in
- in danh sách

Đây là phần rất quan trọng khi chuẩn bị trước ngày khởi hành.

### 5.11. Quản lý dịch vụ
Admin vào:

- `/admin/services`

Chức năng:

- quản lý các dịch vụ liên quan đến tour

### 5.12. Quản lý nhật ký tour
Admin vào:

- `/admin/tour-diaries`

Chức năng:

- xem các nhật ký tour
- tạo mới
- chỉnh sửa
- xóa

Phần này hỗ trợ lưu lại diễn biến chuyến đi.

## 6. Luồng hoạt động của HDV

HDV có một cổng riêng:

- `/hdv/dashboard`

### 6.1. Đăng nhập HDV
Khi tài khoản có role `hdv` đăng nhập:

- hệ thống tự chuyển sang cổng HDV

Hiện tại hệ thống hỗ trợ 2 kiểu:

- tài khoản HDV gắn riêng với một hồ sơ HDV
- tài khoản HDV dùng chung

Nếu là tài khoản HDV dùng chung:

- sau khi đăng nhập, có thể chọn HDV muốn xem trong dropdown

### 6.2. Xem trang HDV tổng quan
Trang chính HDV hiển thị:

- danh sách tour được phân công
- phân loại theo trạng thái:
  - đang tiến hành
  - sẽ tiến hành
  - đã tiến hành

HDV có thể bấm vào từng tour để xem chi tiết.

### 6.3. Xem chi tiết tour
Trong phần chi tiết tour, HDV xem được:

- thông tin tour
- ngày đi / ngày về
- điểm hẹn
- giờ tập trung
- phương tiện
- danh sách khách
- tài xế
- hoạt động / nhật ký liên quan

Đây là màn hình HDV dùng nhiều nhất khi chuẩn bị dẫn đoàn.

### 6.4. Xem tour được phân công
HDV vào:

- `/hdv/tour-phan-cong`

Tại đây HDV xem:

- tour nào mình được giao
- khách hàng chính
- số lượng khách
- điểm hẹn
- thời gian khởi hành / kết thúc

Từ đây HDV có thể mở chi tiết một tour cụ thể.

### 6.5. Check-in khách
Ngay trong khu tour được phân công, HDV có thể:

- check-in khách đã có mặt
- hủy check-in nếu thao tác nhầm

Mục tiêu:

- kiểm soát khách trước lúc tour xuất phát

### 6.6. Xem lịch trình
HDV vào:

- `/hdv/lich-trinh`

Trang này giúp HDV:

- xem các tour theo thời gian
- nắm lịch công tác sắp tới

### 6.7. Viết nhật ký tour
HDV vào:

- `/hdv/nhat-ky-tour`

Tại đây HDV có thể:

- xem danh sách nhật ký đã viết
- lọc theo chuyến khởi hành
- tạo nhật ký mới
- sửa nhật ký
- xóa nhật ký
- thêm ảnh

Nhật ký giúp lưu lại diễn biến thực tế của chuyến đi.

## 7. Tóm tắt luồng nghiệp vụ toàn hệ thống

Có thể hiểu luồng tổng quát như sau:

1. Admin tạo danh mục tour
2. Admin tạo tour
3. Admin tạo đợt khởi hành cho tour
4. Admin tạo booking và danh sách khách
5. Admin tạo hoặc quản lý hồ sơ HDV
6. Admin phân công HDV cho đợt khởi hành
7. HDV đăng nhập vào cổng HDV
8. HDV xem tour được giao
9. HDV check-in khách khi chuẩn bị khởi hành
10. HDV theo dõi lịch trình và viết nhật ký tour
11. Admin theo dõi toàn bộ dữ liệu từ trang quản trị

## 8. Kết luận theo góc nhìn người dùng

Nếu nhìn từ phía người dùng cuối, hệ thống này hiện đang mạnh ở phần:

- quản lý nội bộ tour
- điều hành tour
- theo dõi khách đoàn
- làm việc giữa admin và HDV

Phần còn thiếu nếu muốn thành website bán tour hoàn chỉnh cho khách bên ngoài là:

- trang chủ công khai
- danh sách tour cho khách tự duyệt
- form đặt tour online
- lịch sử booking của người dùng thường
- thanh toán online

Nói ngắn gọn:

- **admin** là người điều hành hệ thống
- **HDV** là người sử dụng cổng tác nghiệp khi dẫn tour
- **người dùng thường** hiện mới dừng ở đăng ký, đăng nhập và xem tài khoản
- **khách công khai** chỉ có thể xem nhanh chi tiết tour qua link QR
