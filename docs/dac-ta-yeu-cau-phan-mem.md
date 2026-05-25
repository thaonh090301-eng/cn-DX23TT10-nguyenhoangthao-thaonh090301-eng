# Đặc tả yêu cầu phần mềm

## 1. Giới thiệu đề tài

Đề tài **Personal Time Optimization System** là hệ thống hỗ trợ người dùng quản lý, theo dõi và tối ưu hóa thời gian cá nhân. Hệ thống được xây dựng theo mô hình PHP MVC thuần, sử dụng MySQL/MariaDB làm cơ sở dữ liệu và giao diện web bằng HTML, CSS, JavaScript.

Tên tiếng Việt của đề tài: **Xây dựng hệ thống tối ưu hóa quản lý thời gian cá nhân**.

## 2. Mục tiêu hệ thống

- Hỗ trợ người dùng đăng nhập và quản lý thông tin tài khoản cơ bản.
- Cho phép phân loại hoạt động theo danh mục.
- Cho phép tạo hoạt động, lập lịch, theo dõi lịch bằng danh sách và lịch tháng/tuần/ngày.
- Sử dụng dữ liệu lịch đã lên làm dữ liệu thời gian chính thức cho từng hoạt động.
- Cung cấp Dashboard để tổng hợp thời gian theo kế hoạch/lịch và cảnh báo cơ bản.
- Hỗ trợ nhắc nhở, ngày quan trọng, Focus/Pomodoro và gợi ý tối ưu lịch theo khoảng trống.
- Cung cấp trợ lý gợi ý thông minh theo luật đơn giản dựa trên dữ liệu hiện có.

## 3. Công nghệ sử dụng

- Ngôn ngữ backend: PHP 8.x.
- Kiến trúc: PHP MVC thuần, không dùng Laravel, CakePHP, FuelPHP hoặc framework lớn.
- Cơ sở dữ liệu: MySQL/MariaDB.
- Kết nối dữ liệu: PDO và prepared statements.
- Frontend: HTML, CSS, JavaScript thuần.
- Thư viện lịch: FullCalendar từ CDN.
- Lưu cấu hình giao diện phía trình duyệt: `localStorage`.
- Tệp cấu hình môi trường: `.env`.

## 4. Tác nhân sử dụng

- **Người dùng đã đăng nhập**: sử dụng các chức năng chính như Dashboard, danh mục, hoạt động, lịch, nhắc nhở, ngày quan trọng, nhật ký thời gian, Focus/Pomodoro, tối ưu lịch và trợ lý gợi ý.
- **Khách chưa đăng nhập**: chỉ có thể truy cập trang đăng nhập. Các trang chức năng yêu cầu phiên đăng nhập hợp lệ.
- **Quản trị hệ thống**: hiện chưa có module quản trị riêng trong mã nguồn.

## 5. Phạm vi hệ thống

Hệ thống hiện tập trung vào quản lý thời gian cá nhân cho một người dùng sau khi đăng nhập. Các chức năng chính nằm trong thư mục `scr/`, định tuyến tại `scr/routes.php`, controller tại `scr/app/Controllers/`, repository tại `scr/app/Repositories/`, view tại `scr/app/Views/`, và schema cơ sở dữ liệu tại `setup/database/schema.sql`.

Phạm vi hiện tại bao gồm:

- Xác thực đăng nhập, đăng xuất và xem thông tin tài khoản.
- Quản lý danh mục và hoạt động.
- Lập lịch, xem lịch bằng FullCalendar và API JSON cho sự kiện lịch.
- Nhắc nhở cá nhân và API nhắc nhở trong ngày.
- Quản lý ngày quan trọng và hiển thị lên lịch.
- Báo cáo nhật ký thời gian dựa trên dữ liệu lịch.
- Focus/Pomodoro và lưu phiên tập trung thành nhật ký thời gian.
- Tối ưu lịch bằng phân tích khoảng trống theo luật.
- Trợ lý gợi ý thông minh dựa trên dữ liệu lịch, nhật ký, nhắc nhở và ngày quan trọng.

Ngoài phạm vi hiện tại:

- Chưa có đăng ký tài khoản tự phục vụ.
- Chưa có phân quyền quản trị nhiều cấp.
- Chưa có đồng bộ Google Calendar hoặc dịch vụ bên ngoài.
- Chưa có machine learning.
- Chưa có triển khai cloud, Docker bắt buộc, Redis hoặc OAuth.

## 6. Danh sách chức năng

### 6.1 Đăng nhập, đăng xuất, tài khoản

- Người dùng đăng nhập bằng email và mật khẩu.
- Mật khẩu được kiểm tra bằng `password_verify`.
- Sau khi đăng nhập, hệ thống lưu thông tin người dùng trong PHP session.
- Người dùng có thể đăng xuất và xem trang tài khoản.

### 6.2 Dashboard

- Hiển thị tổng thời gian kế hoạch/lịch trong ngày/tuần.
- Hiển thị số hoạt động đang dùng, số lịch đã lên và số phiên tập trung.
- Thống kê thời gian theo danh mục.
- Hiển thị cảnh báo khi thời gian cá nhân/giải trí theo lịch vượt ngưỡng.
- Hiển thị gợi ý phụ trợ từ nhắc nhở và ngày quan trọng sắp tới.

### 6.3 Quản lý danh mục

- Xem danh sách danh mục.
- Thêm, sửa, xóa danh mục.
- Mỗi danh mục có tên, màu sắc và thứ tự sắp xếp.
- Danh mục thuộc về người dùng.

### 6.4 Quản lý hoạt động

- Xem danh sách hoạt động.
- Thêm, sửa, xóa hoạt động.
- Mỗi hoạt động thuộc một danh mục.
- Hoạt động có tiêu đề, mô tả, mức ưu tiên, thời lượng ước tính và trạng thái hoạt động.

### 6.5 Lập lịch

- Xem danh sách lịch.
- Thêm, sửa, xóa lịch.
- Mỗi lịch thuộc một hoạt động.
- Lịch có tiêu đề, thời gian bắt đầu, thời gian kết thúc, trạng thái và ghi chú.
- Hệ thống kiểm tra thời gian kết thúc phải sau thời gian bắt đầu.

### 6.6 Calendar

- Hiển thị lịch bằng FullCalendar.
- Lấy dữ liệu sự kiện từ route `/api/schedules`.
- Sự kiện lịch sử dụng dữ liệu từ lịch đã tạo và ngày quan trọng.
- Người dùng có thể bấm vào sự kiện để đi đến trang chỉnh sửa phù hợp.

### 6.7 Nhắc nhở

- Xem danh sách nhắc nhở.
- Thêm, sửa, xóa nhắc nhở.
- Bật/tắt nhắc nhở.
- Hỗ trợ kiểu lặp `none`, `daily`, `weekly`.
- Có API `/api/reminders/today` để lấy nhắc nhở đang hoạt động trong ngày.

### 6.8 Ngày quan trọng

- Xem danh sách ngày quan trọng.
- Thêm, sửa, xóa ngày quan trọng.
- Hỗ trợ loại ngày như kỳ nghỉ, du lịch, hẹn hò, kỷ niệm, hạn chót, sinh nhật, thi cử và loại khác.
- Có số ngày nhắc trước và tùy chọn lặp hằng năm.
- Ngày quan trọng được đưa vào dữ liệu Calendar.

### 6.9 Báo cáo nhật ký thời gian

- Xem báo cáo nhật ký thời gian theo ngày.
- Báo cáo hiển thị dữ liệu từ lịch đã tạo: hoạt động, danh mục, thời gian kế hoạch, thời lượng kế hoạch và ghi chú.
- Trang báo cáo không yêu cầu người dùng nhập thêm dữ liệu sau khi tạo lịch.
- Chức năng đặt lại nhật ký thời gian chỉ xóa dữ liệu `time_logs`, không làm mất dữ liệu lịch.

### 6.10 Focus/Pomodoro

- Chọn hoạt động và thời lượng tập trung.
- Chạy phiên tập trung bằng JavaScript phía trình duyệt.
- Khi lưu, hệ thống tạo một bản ghi trong `time_logs`.
- Nhật ký tạo từ Focus/Pomodoro dùng ghi chú ổn định: `Created from focus mode.`

### 6.11 Tối ưu lịch

- Người dùng chọn hoạt động, khoảng ngày, thời lượng cần tìm, thời gian sớm nhất và muộn nhất.
- Hệ thống đọc lịch hiện có trong khoảng thời gian được chọn.
- Các lịch hiện có được xem là khoảng bận.
- Dịch vụ tối ưu sắp xếp, gộp khoảng bận và tìm khoảng trống đủ dài.
- Kết quả gợi ý gồm thời gian bắt đầu, kết thúc, độ dài khoảng trống, hoạt động, danh mục, điểm và lý do.
- Người dùng có thể tạo lịch từ gợi ý nếu khoảng thời gian vẫn hợp lệ.

### 6.12 Trợ lý gợi ý thông minh

- Trợ lý dùng các luật đơn giản, không dùng machine learning.
- Phân tích lịch hiện có, phân bố hoạt động/danh mục, nhắc nhở, ngày quan trọng và khoảng trống.
- Trả về các gợi ý dạng cảnh báo, thông tin hoặc khuyến nghị.

## 7. Yêu cầu phi chức năng

- Hệ thống phải chạy được trên môi trường localhost với PHP 8.x và MySQL/MariaDB.
- Truy vấn cơ sở dữ liệu phải sử dụng PDO prepared statements.
- Dữ liệu hiển thị trong view cần được escape để giảm rủi ro XSS.
- Giao diện cần dễ sử dụng trên trình duyệt hiện đại.
- Các chức năng CRUD cần có kiểm tra dữ liệu bắt buộc.
- Không thêm framework lớn khi chưa có yêu cầu rõ ràng.
- Mã nguồn nên giữ phong cách PHP đơn giản, dễ đọc, dễ kiểm tra.
- Cấu hình nhạy cảm không được hard-code trong mã nguồn, mà lấy từ `.env`.
- Cơ sở dữ liệu phải tương thích MySQL/MariaDB và dùng `utf8mb4`.

## 8. Mô hình dữ liệu tóm tắt

### 8.1 users

Lưu người dùng, email, mật khẩu đã hash, vai trò và thời điểm tạo/cập nhật.

### 8.2 categories

Lưu danh mục của người dùng, gồm tên, màu sắc và thứ tự sắp xếp. Mỗi danh mục thuộc một người dùng.

### 8.3 activities

Lưu hoạt động cá nhân, liên kết với `users` và `categories`. Hoạt động có tiêu đề, mô tả, ưu tiên, thời lượng ước tính và trạng thái đang dùng.

### 8.4 schedules

Lưu lịch đã lên, liên kết với `users` và `activities`. Lịch có thời gian bắt đầu, thời gian kết thúc, trạng thái và ghi chú.

### 8.5 reminders

Lưu nhắc nhở cá nhân, gồm tiêu đề, ghi chú, giờ nhắc, kiểu lặp, ngày trong tuần và trạng thái bật/tắt.

### 8.6 important_dates

Lưu ngày quan trọng, gồm tiêu đề, ngày sự kiện, loại ngày, ghi chú, số ngày nhắc trước và tùy chọn lặp hằng năm.

### 8.7 time_logs

Lưu dữ liệu nhật ký thời gian lịch sử, liên kết với người dùng, hoạt động và tùy chọn liên kết với lịch. Báo cáo hiện tại ưu tiên đọc trực tiếp từ bảng `schedules`.

## 9. Luồng xử lý chính

### 9.1 Luồng đăng nhập

1. Người dùng mở `/login`.
2. Người dùng nhập email và mật khẩu.
3. Controller tìm người dùng theo email.
4. Hệ thống kiểm tra mật khẩu bằng `password_verify`.
5. Nếu hợp lệ, hệ thống lưu thông tin vào session và chuyển đến trang dự định hoặc Dashboard.
6. Nếu không hợp lệ, hệ thống trả về lỗi đăng nhập.

### 9.2 Luồng quản lý hoạt động

1. Người dùng mở danh sách hoạt động.
2. Người dùng chọn thêm hoặc sửa hoạt động.
3. Hệ thống kiểm tra danh mục, tiêu đề và các trường bắt buộc.
4. Repository lưu dữ liệu bằng PDO prepared statements.
5. Người dùng được chuyển về danh sách hoạt động.

### 9.3 Luồng lập lịch

1. Người dùng chọn hoạt động và nhập thời gian bắt đầu/kết thúc.
2. Hệ thống kiểm tra hoạt động tồn tại và thời gian kết thúc sau thời gian bắt đầu.
3. Lịch được lưu vào bảng `schedules`.
4. Lịch xuất hiện trong danh sách lịch và Calendar.

### 9.4 Luồng báo cáo nhật ký thời gian

1. Người dùng tạo lịch với thời gian bắt đầu và kết thúc.
2. Hệ thống kiểm tra thời gian kết thúc sau thời gian bắt đầu.
3. Lịch được lưu vào `schedules`.
4. Dashboard, thời gian biểu, lịch tháng và báo cáo nhật ký dùng thời gian trong lịch để tổng hợp.

### 9.5 Luồng Focus/Pomodoro

1. Người dùng chọn hoạt động và thời lượng tập trung.
2. Giao diện chạy bộ đếm thời gian.
3. Khi hoàn thành và lưu phiên, hệ thống tạo nhật ký thời gian.
4. Nhật ký được đánh dấu bằng ghi chú `Created from focus mode.` để Dashboard có thể đếm số phiên tập trung trong ngày.

### 9.6 Luồng tối ưu lịch

1. Người dùng nhập điều kiện tìm khoảng trống.
2. Hệ thống lấy các lịch đã có trong khoảng ngày.
3. Dịch vụ tối ưu xem các lịch là khoảng bận, gộp khoảng trùng nhau và tìm khoảng trống.
4. Hệ thống lọc khoảng trống đủ thời lượng.
5. Hệ thống chấm điểm và hiển thị gợi ý.
6. Người dùng có thể tạo lịch từ gợi ý.

### 9.7 Luồng trợ lý gợi ý thông minh

1. Người dùng mở trang trợ lý.
2. Hệ thống đọc dữ liệu lịch, nhắc nhở và ngày quan trọng.
3. Dịch vụ áp dụng các luật kiểm tra như quá tải theo lịch, phân bố danh mục hoặc còn khoảng trống.
4. Hệ thống hiển thị các gợi ý và khuyến nghị.

## 10. Giới hạn hiện tại của hệ thống

- Chưa có chức năng đăng ký tài khoản mới cho người dùng cuối.
- Chưa có chức năng quên mật khẩu hoặc đổi mật khẩu.
- Chưa có phân quyền quản trị riêng.
- Chưa có đồng bộ với Google Calendar, Apple Calendar hoặc dịch vụ bên ngoài.
- Trợ lý thông minh hiện là rule-based, chưa dùng AI bên ngoài hoặc machine learning.
- Focus/Pomodoro phụ thuộc vào JavaScript phía trình duyệt, chưa có cơ chế chạy nền khi đóng tab.
- Nhắc nhở phụ thuộc vào trình duyệt và trạng thái mở trang, chưa có hệ thống gửi thông báo nền phía server.
- Dữ liệu demo còn giới hạn, chủ yếu phục vụ kiểm thử local.

## 11. Định hướng phát triển giao diện

- Tiếp tục cải thiện bố cục workspace để các trang quản lý dữ liệu dễ đọc và dễ thao tác hơn.
- Tăng tính nhất quán giữa form, bảng, thẻ thống kê và trạng thái rỗng.
- Tiếp tục hoàn thiện trải nghiệm responsive cho màn hình nhỏ.
- Cải thiện khả năng truy cập bằng bàn phím, trạng thái focus và độ tương phản màu.
- Tiếp tục hoàn thiện chế độ tối như một hướng cải tiến giao diện.
- Có thể nghiên cứu phong cách glassmorphism ở mức vừa phải cho một số khu vực giao diện, nhưng cần ưu tiên độ rõ ràng, hiệu năng và khả năng đọc.
- Bổ sung biểu đồ trực quan hơn cho Dashboard nếu cần, nhưng không làm thay đổi kiến trúc PHP MVC hiện tại.
