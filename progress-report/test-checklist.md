# Test Checklist — Hệ thống tối ưu hóa quản lý thời gian cá nhân

## Quy ước mức độ ưu tiên

| Mức | Ý nghĩa                                            |
| --- | -------------------------------------------------- |
| P0  | Bắt buộc phải đạt, lỗi sẽ ảnh hưởng demo/nộp đồ án |
| P1  | Quan trọng, nên đạt trước khi nộp                  |
| P2  | Bổ sung, giúp sản phẩm hoàn thiện hơn              |

---

# 1. Kiểm thử khởi động, cấu hình và môi trường

| Mã test | Chức năng        | Bước kiểm thử                                 | Kết quả mong đợi                                | Mức |
| ------- | ---------------- | --------------------------------------------- | ----------------------------------------------- | --- |
| SYS-01  | Khởi động server | Chạy PHP server tại `scr/public`              | Server chạy tại `http://localhost:8000`         | P0  |
| SYS-02  | Kết nối database | Mở trang Dashboard hoặc Categories            | Không lỗi PDO/database                          | P0  |
| SYS-03  | Import database  | Import `schema.sql`, sau đó import `seed.sql` | Database có đủ bảng và dữ liệu demo             | P0  |
| SYS-04  | File `.env`      | Kiểm tra local có `.env`                      | App đọc đúng cấu hình local                     | P0  |
| SYS-05  | Không lộ `.env`  | Chạy `git ls-files .env`                      | Không hiện `.env`                               | P0  |
| SYS-06  | `.env.example`   | Mở `.env.example` trên GitHub                 | Có cấu hình mẫu, không có mật khẩu nhạy cảm     | P0  |
| SYS-07  | Git status       | Chạy `git status`                             | Working tree clean trước khi nộp                | P0  |
| SYS-08  | README setup     | Đọc README và làm theo hướng dẫn chạy         | Người khác có thể clone, tạo DB và chạy project | P0  |

---

# 2. Kiểm thử đăng nhập, đăng xuất và tài khoản

| Mã test | Chức năng                        | Bước kiểm thử                                           | Kết quả mong đợi                                             | Mức |
| ------- | -------------------------------- | ------------------------------------------------------- | ------------------------------------------------------------ | --- |
| AUTH-01 | Mở trang login                   | Truy cập `/login` khi chưa đăng nhập                    | Hiển thị form đăng nhập                                      | P0  |
| AUTH-02 | VI/EN trên login                 | Bấm `VI`, sau đó `EN` ở trang login                     | Text đổi đúng Việt/Anh                                       | P0  |
| AUTH-03 | Đăng nhập đúng                   | Nhập `demo@example.com` / `password`                    | Đăng nhập thành công, vào hệ thống                           | P0  |
| AUTH-04 | Đăng nhập sai                    | Nhập sai mật khẩu                                       | Báo lỗi, không vào hệ thống                                  | P0  |
| AUTH-05 | Chặn truy cập khi chưa login     | Logout rồi mở `/dashboard`                              | Redirect về `/login`                                         | P0  |
| AUTH-06 | Giữ ngôn ngữ sau login           | Chọn EN ở login rồi đăng nhập                           | Giao diện bên trong dùng tiếng Anh                           | P1  |
| AUTH-07 | Trang tài khoản                  | Mở `/account`                                           | Hiển thị tên, email, tổng hoạt động, tổng lịch, tổng nhật ký | P0  |
| AUTH-08 | Đăng xuất                        | Bấm Log Out / Đăng xuất                                 | Thoát phiên, quay về login                                   | P0  |
| AUTH-09 | Session                          | Refresh sau khi login                                   | Vẫn giữ trạng thái đăng nhập                                 | P1  |
| AUTH-10 | Không truy cập dữ liệu user khác | Nếu có nhiều user demo, kiểm tra dữ liệu theo `user_id` | Chỉ thấy dữ liệu user hiện tại                               | P1  |

---

# 3. Kiểm thử giao diện workspace

| Mã test | Chức năng             | Bước kiểm thử                                | Kết quả mong đợi                                        | Mức |
| ------- | --------------------- | -------------------------------------------- | ------------------------------------------------------- | --- |
| UI-01   | Sidebar               | Mở mọi trang chính                           | Sidebar hiển thị đầy đủ module                          | P0  |
| UI-02   | Active menu           | Bấm từng module                              | Menu đang mở được highlight đúng                        | P1  |
| UI-03   | Topbar                | Mở Dashboard, Assistant, Calendar, Time Logs | Header không bị cắt chữ, không bó hẹp                   | P0  |
| UI-04   | Full width layout     | Mở trình duyệt full screen                   | Nội dung mở rộng theo cửa sổ, không bị bó giữa          | P1  |
| UI-05   | Dark mode             | Chọn giao diện tối                           | Toàn app chuyển tối, chữ vẫn rõ                         | P0  |
| UI-06   | Light mode            | Chọn giao diện sáng                          | Toàn app chuyển sáng đúng                               | P0  |
| UI-07   | Không nhấp nháy theme | Chọn dark mode rồi chuyển trang              | Không chớp sáng trước khi đổi tối                       | P1  |
| UI-08   | Accent color          | Đổi xanh dương/tím/xanh lá/cam               | Nút, highlight, badge đổi màu đúng                      | P1  |
| UI-09   | Mật độ thoải mái      | Chọn “Thoải mái”                             | Card/form/table có khoảng cách rộng                     | P1  |
| UI-10   | Mật độ gọn            | Chọn “Gọn”                                   | Card/form/table gọn rõ ràng hơn                         | P1  |
| UI-11   | Responsive            | Thu nhỏ cửa sổ trình duyệt                   | Không mất nút quan trọng, không tràn ngang nghiêm trọng | P2  |
| UI-12   | Empty state           | Mở trang chưa có dữ liệu                     | Hiển thị thông báo dễ hiểu và nút thao tác phù hợp      | P1  |
| UI-13   | Toast message         | Tạo/sửa/xóa dữ liệu                          | Có thông báo thành công hoặc lỗi                        | P1  |
| UI-14   | Confirm delete        | Bấm xóa dữ liệu                              | Có xác nhận trước khi xóa                               | P0  |

---

# 4. Kiểm thử đa ngôn ngữ và định dạng thời gian

| Mã test | Chức năng                     | Bước kiểm thử                        | Kết quả mong đợi                                       | Mức |
| ------- | ----------------------------- | ------------------------------------ | ------------------------------------------------------ | --- |
| LANG-01 | Chuyển VI                     | Bấm `VI`                             | Giao diện chuyển sang tiếng Việt                       | P0  |
| LANG-02 | Chuyển EN                     | Bấm `EN`                             | Giao diện chuyển sang tiếng Anh                        | P0  |
| LANG-03 | Giữ ngôn ngữ                  | Chọn EN rồi refresh                  | Vẫn giữ EN                                             | P1  |
| LANG-04 | Không lẫn tiếng Anh trong VI  | Mở tất cả trang khi đang ở VI        | Không còn text như `Created from...`, `Open Optimizer` | P0  |
| LANG-05 | Không lẫn tiếng Việt trong EN | Mở tất cả trang khi đang ở EN        | Text chính chuyển sang tiếng Anh hợp lý                | P1  |
| LANG-06 | Giờ tiếng Việt                | Chọn VI, mở lịch/time logs/timetable | Giờ hiển thị dạng 24h: `08:30`, `16:30`                | P0  |
| LANG-07 | Giờ tiếng Anh                 | Chọn EN, mở lịch/time logs/timetable | Giờ hiển thị dạng 12h: `08:30 AM`, `04:30 PM`          | P0  |
| LANG-08 | Ngày tiếng Việt               | Chọn VI                              | Ngày giờ hiển thị kiểu `19/05/2026 08:30`              | P1  |
| LANG-09 | Ngày tiếng Anh                | Chọn EN                              | Ngày giờ hiển thị kiểu `05/19/2026 08:30 AM`           | P1  |
| LANG-10 | FullCalendar VI               | Chọn VI, mở Calendar                 | Nút: `Hôm nay`, `Danh sách`, `Ngày`, `Tuần`, `Tháng`   | P0  |
| LANG-11 | FullCalendar EN               | Chọn EN, mở Calendar                 | Nút: `Today`, `List`, `Day`, `Week`, `Month`           | P1  |

---

# 5. Kiểm thử Dashboard và Gamification

| Mã test | Chức năng             | Bước kiểm thử                    | Kết quả mong đợi                           | Mức |
| ------- | --------------------- | -------------------------------- | ------------------------------------------ | --- |
| DASH-01 | Mở Dashboard          | Truy cập `/dashboard`            | Trang tổng quan hiển thị không lỗi         | P0  |
| DASH-02 | Kế hoạch hôm nay      | Có lịch trong hôm nay            | Card “Kế hoạch hôm nay” tính đúng phút     | P0  |
| DASH-03 | Thực tế hôm nay       | Có time log đã xác nhận          | Card “Thực tế hôm nay” tính đúng phút      | P0  |
| DASH-04 | Kế hoạch tuần này     | Có lịch trong tuần               | Tổng phút tuần tính đúng                   | P1  |
| DASH-05 | Thực tế tuần này      | Có nhật ký tuần này              | Tổng thực tế tuần tính đúng                | P1  |
| DASH-06 | Hoạt động đang dùng   | Có active activities             | Đếm đúng hoạt động đang dùng               | P1  |
| DASH-07 | Lịch đã lên           | Có schedules                     | Đếm đúng số lịch đã lên                    | P1  |
| DASH-08 | Điểm năng suất        | Có dữ liệu kế hoạch và nhật ký   | Hiển thị điểm /100                         | P1  |
| DASH-09 | Badge “Có kế hoạch”   | Có lịch hôm nay                  | Badge xuất hiện                            | P2  |
| DASH-10 | Badge “Có ghi nhận”   | Có time log hôm nay              | Badge xuất hiện                            | P2  |
| DASH-11 | Cảnh báo nhẹ          | Có lịch nhưng chưa xác nhận      | Hiển thị cảnh báo màu cam                  | P0  |
| DASH-12 | Báo động đỏ           | Tạo log quá dài, ví dụ >720 phút | Hiển thị báo động đỏ                       | P0  |
| DASH-13 | Trợ lý trên Dashboard | Mở Dashboard                     | Có khu vực trợ lý/gợi ý hoặc nút mở trợ lý | P1  |
| DASH-14 | Category statistics   | Có nhiều danh mục                | Thanh thống kê theo danh mục hiển thị đúng | P1  |

---

# 6. Kiểm thử danh mục

| Mã test | Chức năng                 | Bước kiểm thử                     | Kết quả mong đợi                                    | Mức |
| ------- | ------------------------- | --------------------------------- | --------------------------------------------------- | --- |
| CAT-01  | Danh sách danh mục        | Mở `/categories`                  | Hiển thị danh mục, màu, thứ tự, số hoạt động        | P0  |
| CAT-02  | Thêm danh mục             | Bấm “Thêm danh mục”, nhập dữ liệu | Danh mục mới được tạo                               | P0  |
| CAT-03  | Sửa danh mục              | Đổi tên/màu/thứ tự                | Cập nhật đúng                                       | P0  |
| CAT-04  | Xóa danh mục trống        | Xóa danh mục không có activity    | Xóa thành công                                      | P1  |
| CAT-05  | Xóa danh mục có hoạt động | Xóa danh mục đang có activity     | Không làm hỏng dữ liệu, có cảnh báo                 | P0  |
| CAT-06  | Validate tên rỗng         | Tạo danh mục không nhập tên       | Báo lỗi, không lưu                                  | P0  |
| CAT-07  | Màu danh mục              | Chọn màu mới                      | Màu hiển thị đúng ở Activities, Schedules, Calendar | P1  |

---

# 7. Kiểm thử hoạt động

| Mã test | Chức năng           | Bước kiểm thử                          | Kết quả mong đợi                                        | Mức |
| ------- | ------------------- | -------------------------------------- | ------------------------------------------------------- | --- |
| ACT-01  | Danh sách hoạt động | Mở `/activities`                       | Hiển thị title, danh mục, ưu tiên, ước tính, trạng thái | P0  |
| ACT-02  | Thêm hoạt động      | Tạo hoạt động mới                      | Hoạt động xuất hiện trong danh sách                     | P0  |
| ACT-03  | Sửa hoạt động       | Sửa tên/danh mục/ưu tiên/phút ước tính | Dữ liệu cập nhật đúng                                   | P0  |
| ACT-04  | Xóa hoạt động       | Xóa activity không còn dùng            | Xóa hoặc cảnh báo nếu có dữ liệu liên quan              | P1  |
| ACT-05  | Validate tiêu đề    | Bỏ trống tiêu đề                       | Báo lỗi                                                 | P0  |
| ACT-06  | Validate thời lượng | Nhập số phút âm hoặc 0                 | Báo lỗi                                                 | P0  |
| ACT-07  | Lọc hoạt động       | Dùng filter/search nếu có              | Danh sách lọc đúng                                      | P1  |
| ACT-08  | Trạng thái active   | Tắt/bật hoạt động nếu có               | Dashboard/Optimizer xử lý đúng hoạt động active         | P1  |

---

# 8. Kiểm thử lịch / schedules

| Mã test | Chức năng                | Bước kiểm thử                   | Kết quả mong đợi                                          | Mức |
| ------- | ------------------------ | ------------------------------- | --------------------------------------------------------- | --- |
| SCH-01  | Danh sách lịch           | Mở `/schedules`                 | Hiển thị title, activity, category, start, end, status    | P0  |
| SCH-02  | Thêm lịch                | Tạo lịch mới từ form            | Lịch xuất hiện trong Schedules                            | P0  |
| SCH-03  | Sửa lịch                 | Đổi thời gian hoặc hoạt động    | Cập nhật đúng                                             | P0  |
| SCH-04  | Xóa lịch                 | Xóa một schedule                | Lịch biến mất khỏi Schedules, Calendar, Timetable         | P0  |
| SCH-05  | Validate end < start     | Tạo lịch kết thúc trước bắt đầu | Báo lỗi, không lưu                                        | P0  |
| SCH-06  | Lịch trùng giờ           | Tạo lịch chồng lên lịch khác    | Hệ thống cảnh báo hoặc xử lý không làm hỏng dữ liệu       | P1  |
| SCH-07  | Ghi chú lịch             | Nhập note tiếng Việt            | Hiển thị đúng, không lỗi mã hóa                           | P1  |
| SCH-08  | Tạo từ Optimizer         | Bấm tạo lịch từ gợi ý           | Schedule mới tạo đúng start/end/activity                  | P0  |
| SCH-09  | Ghi chú tạo từ Optimizer | Mở Time Logs/Schedules          | VI hiển thị “Tạo từ gợi ý tối ưu.”, EN hiển thị tiếng Anh | P1  |
| SCH-10  | Đồng bộ Calendar         | Tạo/sửa/xóa lịch                | Calendar cập nhật đúng                                    | P0  |
| SCH-11  | Đồng bộ Timetable        | Tạo/sửa/xóa lịch                | Timetable cập nhật đúng                                   | P0  |

---

# 9. Kiểm thử Calendar

| Mã test | Chức năng       | Bước kiểm thử                  | Kết quả mong đợi                             | Mức |
| ------- | --------------- | ------------------------------ | -------------------------------------------- | --- |
| CAL-01  | Mở Calendar     | Truy cập `/calendar`           | FullCalendar hiển thị                        | P0  |
| CAL-02  | View tháng      | Bấm “Tháng”                    | Hiển thị lịch tháng                          | P0  |
| CAL-03  | View tuần       | Bấm “Tuần”                     | Hiển thị lịch tuần                           | P0  |
| CAL-04  | View ngày       | Bấm “Ngày”                     | Hiển thị lịch ngày                           | P0  |
| CAL-05  | View danh sách  | Bấm “Danh sách”                | Hiển thị danh sách sự kiện                   | P1  |
| CAL-06  | Thứ tự nút view | Quan sát nút lịch              | Theo thứ tự: Danh sách - Ngày - Tuần - Tháng | P1  |
| CAL-07  | Nút Hôm nay     | Bấm “Hôm nay”                  | Quay về ngày hiện tại                        | P1  |
| CAL-08  | Event schedule  | Có schedule trong DB           | Event hiển thị đúng ngày/giờ                 | P0  |
| CAL-09  | Màu category    | Schedule thuộc danh mục có màu | Event dùng màu phù hợp                       | P1  |
| CAL-10  | Ngày quan trọng | Có Important Date              | Hiển thị như sự kiện cả ngày                 | P1  |
| CAL-11  | Giờ VI          | Chọn VI                        | Event time dùng 24h                          | P0  |
| CAL-12  | Giờ EN          | Chọn EN                        | Event time dùng AM/PM                        | P1  |

---

# 10. Kiểm thử Optimizer / Gợi ý tối ưu

| Mã test | Chức năng                | Bước kiểm thử                           | Kết quả mong đợi                  | Mức |
| ------- | ------------------------ | --------------------------------------- | --------------------------------- | --- |
| OPT-01  | Mở Optimizer             | Truy cập `/optimizer`                   | Form tìm khoảng trống hiển thị    | P0  |
| OPT-02  | Chọn hoạt động           | Chọn activity trong form                | Activity được chọn đúng           | P0  |
| OPT-03  | Tìm slot hợp lệ          | Nhập duration 30-60 phút, range hợp lệ  | Hiển thị khoảng trống phù hợp     | P0  |
| OPT-04  | Không trùng lịch         | Có lịch 08:30-09:00, tìm slot           | Không gợi ý trùng lịch            | P0  |
| OPT-05  | Gộp lịch chồng           | Có lịch 08:00-09:30 và 09:00-10:00      | Xem 08:00-10:00 là bận            | P0  |
| OPT-06  | Duration quá dài         | Nhập duration 600 phút                  | Báo không có slot phù hợp         | P0  |
| OPT-07  | Range không hợp lệ       | End trước start                         | Báo lỗi                           | P0  |
| OPT-08  | Tạo lịch từ gợi ý        | Bấm “Tạo lịch”                          | Schedule mới xuất hiện            | P0  |
| OPT-09  | Ghi chú gợi ý            | Kiểm tra note của lịch tạo từ Optimizer | Note đúng ngôn ngữ hiển thị       | P1  |
| OPT-10  | Mở từ Timetable          | Bấm “Mở gợi ý tối ưu” tại khoảng trống  | Điều hướng sang Optimizer hợp lý  | P1  |
| OPT-11  | Trợ lý dùng khoảng trống | Có gap lớn trong ngày                   | Assistant gợi ý dùng khoảng trống | P1  |

---

# 11. Kiểm thử Time Logs / Báo cáo thời gian hằng ngày

| Mã test | Chức năng                   | Bước kiểm thử                      | Kết quả mong đợi                    | Mức |
| ------- | --------------------------- | ---------------------------------- | ----------------------------------- | --- |
| TL-01   | Mở Time Logs                | Truy cập `/time-logs`              | Trang báo cáo thời gian hiển thị    | P0  |
| TL-02   | Tự kết xuất từ lịch         | Có schedule nhưng chưa có time_log | Vẫn hiển thị schedule trong báo cáo | P0  |
| TL-03   | Trạng thái chưa ghi nhận    | Schedule chưa có time_log          | Hiển thị “Chưa ghi nhận”            | P0  |
| TL-04   | Ghi nhận theo lịch          | Bấm “Ghi nhận theo lịch”           | Tạo time_log từ schedule            | P0  |
| TL-05   | Không tạo trùng             | Bấm ghi nhận lại cùng schedule     | Không tạo duplicate time_log        | P0  |
| TL-06   | Chỉnh sửa thực tế           | Sửa actual start/end               | Cập nhật time_log đúng              | P0  |
| TL-07   | Ghi nhận ngoài lịch         | Tạo log không gắn schedule         | Log xuất hiện trong báo cáo         | P1  |
| TL-08   | Tổng kế hoạch               | Chọn ngày có schedules             | Tổng kế hoạch tính đúng             | P0  |
| TL-09   | Tổng thực tế                | Có time_logs                       | Tổng thực tế tính đúng              | P0  |
| TL-10   | Chưa xác nhận               | Có schedules chưa log              | Số “Chưa xác nhận” đúng             | P1  |
| TL-11   | Validate actual end < start | Nhập giờ thực tế sai               | Báo lỗi                             | P0  |
| TL-12   | Log quá dài                 | Nhập log >720 phút                 | Dashboard/Assistant báo động đỏ     | P0  |
| TL-13   | Tìm kiếm/lọc                | Lọc theo activity/category         | Kết quả đúng                        | P1  |
| TL-14   | Xuất/in báo cáo nếu có      | Bấm in/xuất                        | Giao diện in gọn, dữ liệu đầy đủ    | P2  |
| TL-15   | Giờ VI/EN                   | Đổi ngôn ngữ                       | Giờ đổi đúng 24h/AM-PM              | P0  |

---

# 12. Kiểm thử Thời gian biểu trong ngày

| Mã test | Chức năng               | Bước kiểm thử                      | Kết quả mong đợi                                       | Mức |
| ------- | ----------------------- | ---------------------------------- | ------------------------------------------------------ | --- |
| TT-01   | Mở Timetable            | Truy cập `/timetable`              | Hiển thị thời gian biểu trong ngày                     | P0  |
| TT-02   | Dữ liệu từ schedules    | Có schedules trong ngày            | Tự hiển thị theo thứ tự thời gian                      | P0  |
| TT-03   | Khoảng trống            | Có gap giữa 2 lịch                 | Hiển thị “Trống X phút”                                | P0  |
| TT-04   | Không mâu thuẫn lịch    | Tạo lịch mới                       | Timetable cập nhật theo schedule                       | P0  |
| TT-05   | Ghi nhận theo lịch      | Từ timetable ghi nhận một schedule | Time log được tạo                                      | P0  |
| TT-06   | Đã ghi nhận             | Schedule đã có time_log            | Chỉ hiển thị 1 badge “Đã ghi nhận”                     | P0  |
| TT-07   | Chưa ghi nhận           | Schedule chưa log                  | Hiển thị “Chưa ghi nhận”                               | P0  |
| TT-08   | Không lặp badge         | Quan sát schedule đã ghi nhận      | Không có 2 chữ “Đã ghi nhận”                           | P0  |
| TT-09   | Chỉnh sửa thực tế       | Bấm “Chỉnh sửa thực tế”            | Mở form sửa actual time                                | P1  |
| TT-10   | Mở gợi ý tối ưu         | Bấm tại khoảng trống               | Điều hướng đến Optimizer                               | P1  |
| TT-11   | Reminder trong timeline | Có nhắc nhở trong ngày             | Hiển thị trong timeline nếu đã tích hợp                | P1  |
| TT-12   | Ngày quan trọng         | Có sự kiện hôm nay                 | Hiển thị/nhắc trong timeline nếu đã tích hợp           | P2  |
| TT-13   | Thông báo sắp đến giờ   | Mở trang trước giờ schedule 5 phút | Hiển thị toast/browser notification nếu được cấp quyền | P2  |
| TT-14   | Giờ VI/EN               | Đổi ngôn ngữ                       | Time display đúng quy tắc                              | P0  |

---

# 13. Kiểm thử Trợ lý thông minh

| Mã test | Chức năng                             | Bước kiểm thử                                 | Kết quả mong đợi                    | Mức |
| ------- | ------------------------------------- | --------------------------------------------- | ----------------------------------- | --- |
| AI-01   | Mở Assistant                          | Truy cập `/assistant`                         | Gợi ý tự hiển thị                   | P0  |
| AI-02   | Không cần nút tạo                     | Quan sát trang Assistant                      | Không còn nút “Tạo gợi ý”           | P0  |
| AI-03   | Phân tích có lịch chưa log            | Có schedule nhưng chưa xác nhận               | Gợi ý xác nhận thời gian thực tế    | P0  |
| AI-04   | Phân tích kế hoạch nhiều tiến độ thấp | Planned > 0, actual = 0                       | Gợi ý bắt đầu phiên tập trung/ngắn  | P0  |
| AI-05   | Phân tích log bất thường              | Có time log >720 phút                         | Hiển thị Báo động đỏ                | P0  |
| AI-06   | Phân tích tổng >24h                   | Tổng actual trong ngày >1440 phút             | Báo động dữ liệu bất thường         | P0  |
| AI-07   | Phân tích category chiếm ưu thế       | Một category >60% tuần                        | Gợi ý cân bằng                      | P1  |
| AI-08   | Phân tích khoảng trống                | Có gap dài                                    | Gợi ý dùng Optimizer/nghỉ ngắn      | P1  |
| AI-09   | Phân tích reminders                   | Có reminder sắp tới                           | Gợi ý/nhắc nhẹ nếu đã tích hợp      | P2  |
| AI-10   | Phân tích ngày quan trọng             | Có sự kiện sắp đến                            | Gợi ý chuẩn bị trước                | P2  |
| AI-11   | Tone chăm sóc                         | Đọc nội dung gợi ý                            | Văn phong ấm áp, dễ hiểu, không khô | P1  |
| AI-12   | Severity                              | Tạo các tình huống info/success/warning/alarm | Màu badge/card đúng                 | P0  |
| AI-13   | Không phụ thuộc time_logs בלבד        | Xóa time_logs nhưng còn schedules             | Assistant vẫn phân tích từ lịch     | P0  |
| AI-14   | Dữ liệu mới cập nhật                  | Thêm/sửa/xóa schedule rồi mở Assistant        | Gợi ý thay đổi theo dữ liệu mới     | P0  |

---

# 14. Kiểm thử Focus Mode / Pomodoro

| Mã test  | Chức năng          | Bước kiểm thử                               | Kết quả mong đợi                      | Mức |
| -------- | ------------------ | ------------------------------------------- | ------------------------------------- | --- |
| FOCUS-01 | Mở Focus           | Truy cập `/focus`                           | Trang tập trung hiển thị              | P0  |
| FOCUS-02 | Chọn hoạt động     | Chọn activity                               | Activity được chọn đúng               | P0  |
| FOCUS-03 | Chọn thời lượng    | Chọn 25/45/60 phút                          | Timer nhận đúng thời lượng            | P0  |
| FOCUS-04 | Start              | Bấm Start                                   | Timer bắt đầu đếm ngược               | P0  |
| FOCUS-05 | Pause              | Bấm Pause                                   | Timer dừng                            | P1  |
| FOCUS-06 | Resume             | Bấm tiếp tục nếu có                         | Timer chạy tiếp                       | P1  |
| FOCUS-07 | Reset              | Bấm Reset                                   | Timer về trạng thái ban đầu           | P1  |
| FOCUS-08 | Hoàn thành         | Để timer hoàn tất hoặc test thời lượng ngắn | Hiện nút lưu vào nhật ký              | P0  |
| FOCUS-09 | Lưu Time Log       | Bấm lưu                                     | Tạo time_log đúng activity/duration   | P0  |
| FOCUS-10 | Dashboard cập nhật | Sau khi lưu focus                           | Actual today tăng đúng                | P0  |
| FOCUS-11 | Badge tập trung    | Có focus log                                | Dashboard badge/score cập nhật nếu có | P2  |

---

# 15. Kiểm thử Nhắc nhở hằng ngày

| Mã test | Chức năng            | Bước kiểm thử                       | Kết quả mong đợi                           | Mức |
| ------- | -------------------- | ----------------------------------- | ------------------------------------------ | --- |
| REM-01  | Mở Reminders         | Truy cập `/reminders`               | Danh sách nhắc nhở hiển thị                | P0  |
| REM-02  | Thêm nhắc nhở        | Tạo nhắc “Uống nước”                | Nhắc nhở được lưu                          | P0  |
| REM-03  | Sửa nhắc nhở         | Đổi giờ/nội dung                    | Cập nhật đúng                              | P0  |
| REM-04  | Xóa nhắc nhở         | Xóa một reminder                    | Xóa thành công                             | P0  |
| REM-05  | Bật/tắt              | Toggle active nếu có                | Nhắc nhở bật/tắt đúng                      | P1  |
| REM-06  | Lặp hằng ngày        | Tạo repeat daily                    | Hiển thị trong hôm nay/ngày sau            | P1  |
| REM-07  | Lặp hằng tuần        | Tạo repeat weekly                   | Chỉ hiện đúng thứ                          | P2  |
| REM-08  | Toast nhắc nhở       | Đặt reminder gần thời gian hiện tại | Hiển thị toast khi web đang mở             | P2  |
| REM-09  | Browser notification | Cho phép notification               | Trình duyệt hiện thông báo nếu tab đang mở | P2  |
| REM-10  | Dashboard tích hợp   | Có reminder hôm nay                 | Dashboard hiển thị nhắc sắp tới nếu có     | P1  |
| REM-11  | Timetable tích hợp   | Có reminder trong ngày              | Timetable hiển thị reminder nếu có         | P1  |
| REM-12  | Assistant tích hợp   | Có reminder sắp tới                 | Assistant nhắc nhẹ                         | P2  |

---

# 16. Kiểm thử Ngày quan trọng và đếm ngược

| Mã test | Chức năng            | Bước kiểm thử                        | Kết quả mong đợi                       | Mức |
| ------- | -------------------- | ------------------------------------ | -------------------------------------- | --- |
| IMP-01  | Mở Important Dates   | Truy cập `/important-dates`          | Danh sách ngày quan trọng hiển thị     | P0  |
| IMP-02  | Thêm ngày quan trọng | Tạo “Hạn nộp đồ án”                  | Lưu thành công                         | P0  |
| IMP-03  | Sửa ngày quan trọng  | Đổi ngày/loại/ghi chú                | Cập nhật đúng                          | P0  |
| IMP-04  | Xóa ngày quan trọng  | Xóa một event                        | Xóa thành công                         | P0  |
| IMP-05  | Đếm ngược            | Tạo event trong 3 ngày tới           | Hiển thị còn X ngày                    | P0  |
| IMP-06  | Nhắc trước N ngày    | Set remind_before_days               | Dashboard/Assistant nhắc khi gần đến   | P1  |
| IMP-07  | Lặp hằng năm         | Tạo kỷ niệm repeat yearly            | Năm sau vẫn tính đúng nếu có logic     | P2  |
| IMP-08  | Hiển thị Calendar    | Mở Calendar                          | Ngày quan trọng hiện như all-day event | P1  |
| IMP-09  | Dashboard tích hợp   | Có event trong 7 ngày                | Dashboard hiển thị card/countdown      | P1  |
| IMP-10  | Assistant tích hợp   | Có sự kiện sắp đến                   | Assistant gợi ý chuẩn bị trước         | P1  |
| IMP-11  | Loại sự kiện         | Chọn du lịch/hẹn hò/kỷ niệm/deadline | Hiển thị type đúng                     | P1  |

---

# 17. Kiểm thử Quick Add

| Mã test | Chức năng            | Bước kiểm thử             | Kết quả mong đợi                           | Mức |
| ------- | -------------------- | ------------------------- | ------------------------------------------ | --- |
| QA-01   | Mở Quick Add         | Bấm “Thêm nhanh”          | Menu/modal hiện                            | P0  |
| QA-02   | Thêm hoạt động nhanh | Chọn thêm activity        | Điều hướng/form đúng                       | P1  |
| QA-03   | Thêm lịch nhanh      | Chọn thêm schedule        | Điều hướng/form đúng                       | P1  |
| QA-04   | Ghi nhận ngoài lịch  | Chọn ghi time log         | Mở form ghi nhận ngoài lịch                | P1  |
| QA-05   | Mở Optimizer         | Chọn gợi ý tối ưu         | Mở Optimizer                               | P1  |
| QA-06   | Không vỡ layout      | Mở Quick Add ở dark/light | Hiển thị đẹp, không che khuất nghiêm trọng | P2  |

---

# 18. Kiểm thử bảo mật và nhập liệu

| Mã test | Chức năng          | Bước kiểm thử                              | Kết quả mong đợi                                            | Mức |
| ------- | ------------------ | ------------------------------------------ | ----------------------------------------------------------- | --- |
| SEC-01  | Escape HTML        | Nhập `<script>alert(1)</script>` vào title | Script không chạy                                           | P0  |
| SEC-02  | SQL injection      | Nhập `' OR 1=1 --` vào form/search         | Không lỗi SQL, không lộ dữ liệu                             | P0  |
| SEC-03  | CSRF cơ bản nếu có | Kiểm tra form xóa/sửa                      | Không dễ xóa bằng GET nguy hiểm nếu đã có bảo vệ            | P1  |
| SEC-04  | Password hash      | Kiểm tra seed user                         | Password nên được hash nếu code dùng `password_verify`      | P0  |
| SEC-05  | Không lộ lỗi debug | Tạo lỗi nhẹ hoặc truy cập route sai        | Không lộ quá nhiều stack trace khi demo                     | P1  |
| SEC-06  | File nhạy cảm      | Kiểm tra GitHub                            | Không có `.env`, `.agents`, `AGENTS.md`, `skills-lock.json` | P0  |
| SEC-07  | Quyền truy cập     | Logout rồi vào route nội bộ                | Bị redirect login                                           | P0  |

---

# 19. Kiểm thử GitHub, tài liệu và nộp đồ án

| Mã test | Chức năng        | Bước kiểm thử                                                    | Kết quả mong đợi                       | Mức |
| ------- | ---------------- | ---------------------------------------------------------------- | -------------------------------------- | --- |
| DOC-01  | Repo name        | Kiểm tra GitHub                                                  | Đúng tên repo theo yêu cầu             | P0  |
| DOC-02  | README chính     | Mở README.md                                                     | Có mô tả, công nghệ, cài đặt, chạy app | P0  |
| DOC-03  | Hướng dẫn DB     | README có import schema/seed                                     | Người khác làm theo được               | P0  |
| DOC-04  | Tài khoản demo   | README có email/password demo                                    | GVHD đăng nhập được                    | P0  |
| DOC-05  | Cấu trúc thư mục | README có giải thích `scr`, `setup`, `progress-report`, `thesis` | Rõ ràng                                | P1  |
| DOC-06  | Progress report  | Có `progress-report/week-01.md` và các tuần/bản cập nhật         | Có bằng chứng tiến độ                  | P1  |
| DOC-07  | Test checklist   | Có `progress-report/test-checklist.md`                           | Checklist đầy đủ                       | P1  |
| DOC-08  | Thesis folders   | Có `thesis/doc`, `pdf`, `html`, `abs`, `refs`                    | Đúng cấu trúc nộp                      | P1  |
| DOC-09  | Git status local | Chạy `git status`                                                | Clean                                  | P0  |
| DOC-10  | Push GitHub      | Mở GitHub kiểm tra commit mới nhất                               | Code đã lên remote                     | P0  |
| DOC-11  | Branch main      | Kiểm tra branch nộp                                              | `main` có bản mới nhất                 | P0  |
| DOC-12  | Clone test       | Clone repo sang thư mục khác nếu có thời gian                    | Chạy lại được từ README                | P2  |

