# Bảng test chức năng

## 1. Mục đích

Bảng test chức năng dùng để kiểm tra các nghiệp vụ chính của URTISYNC - Hệ thống tối ưu hóa quản lý thời gian cá nhân. Các ca kiểm thử tập trung vào luồng sử dụng thực tế của người dùng, bao gồm đăng nhập, quản lý dữ liệu, xem lịch, nhận nhắc nhở, báo cáo và tùy chỉnh giao diện.

## 2. Danh sách ca kiểm thử

| Mã test | Chức năng | Dữ liệu kiểm thử | Các bước thực hiện | Kết quả mong đợi | Trạng thái |
|---|---|---|---|---|---|
| TC-01 | Đăng nhập thành công | Email: `demo@example.com`, mật khẩu: `password` | Mở `/login`, nhập thông tin hợp lệ, nhấn đăng nhập. | Hệ thống đăng nhập thành công và chuyển vào giao diện chính. | Chưa thực hiện |
| TC-02 | Đăng nhập thất bại | Email đúng hoặc sai, mật khẩu sai | Mở `/login`, nhập thông tin không hợp lệ, nhấn đăng nhập. | Hệ thống giữ ở trang đăng nhập và hiển thị thông báo lỗi. | Chưa thực hiện |
| TC-03 | Đăng xuất | Tài khoản đã đăng nhập | Nhấn nút đăng xuất. | Phiên đăng nhập bị hủy và người dùng quay về trang đăng nhập. | Chưa thực hiện |
| TC-04 | Xem dashboard | Tài khoản có dữ liệu lịch/hoạt động | Mở `/dashboard`. | Dashboard hiển thị các khối thống kê và thông tin tổng quan. | Chưa thực hiện |
| TC-05 | Thêm danh mục | Tên: Học tập, màu bất kỳ | Mở `/categories`, chọn thêm mới, nhập dữ liệu và lưu. | Danh mục mới xuất hiện trong danh sách. | Chưa thực hiện |
| TC-06 | Sửa danh mục | Danh mục đã tồn tại | Mở trang sửa danh mục, thay đổi tên/màu/thứ tự và lưu. | Danh mục được cập nhật đúng. | Chưa thực hiện |
| TC-07 | Xóa danh mục | Danh mục không có hoạt động liên quan | Mở trang xóa danh mục và xác nhận. | Danh mục bị xóa khỏi danh sách. | Chưa thực hiện |
| TC-08 | Thêm hoạt động | Danh mục hợp lệ, tên hoạt động, ưu tiên, thời lượng ước tính | Mở `/activities/create`, nhập dữ liệu và lưu. | Hoạt động mới được tạo và hiển thị trong danh sách. | Chưa thực hiện |
| TC-09 | Sửa hoạt động | Hoạt động đã tồn tại | Mở trang sửa hoạt động, cập nhật thông tin và lưu. | Hoạt động được cập nhật đúng. | Chưa thực hiện |
| TC-10 | Xóa hoạt động | Hoạt động không có lịch liên quan | Mở trang xóa hoạt động và xác nhận. | Hoạt động bị xóa khỏi danh sách. | Chưa thực hiện |
| TC-11 | Xóa hoạt động có nhật ký thời gian | Hoạt động có ít nhất một dòng `time_logs` và không có lịch đang tham chiếu | Xóa hoạt động, sau đó mở nhật ký thời gian đúng ngày. | Hoạt động được xóa, nhật ký thời gian vẫn còn và hiển thị nhãn hoạt động đã xóa. | Chưa thực hiện |
| TC-12 | Thêm lịch | Hoạt động hợp lệ, thời gian bắt đầu và kết thúc hợp lệ | Mở `/schedules/create`, nhập dữ liệu và lưu. | Lịch mới được tạo và hiển thị trong ngày tương ứng. | Chưa thực hiện |
| TC-13 | Sửa lịch | Lịch đã tồn tại | Mở trang sửa lịch, thay đổi thời gian/ghi chú/trạng thái và lưu. | Lịch được cập nhật đúng. | Chưa thực hiện |
| TC-14 | Xóa lịch | Lịch đã tồn tại | Mở trang xóa lịch và xác nhận. | Lịch bị xóa khỏi danh sách. | Chưa thực hiện |
| TC-15 | Xem lịch theo ngày | Nhiều lịch ở các ngày khác nhau | Mở `/schedules`, chọn ngày xem và nhấn xem ngày. | Chỉ lịch của ngày được chọn hiển thị, trừ khi chọn tất cả ngày. | Chưa thực hiện |
| TC-16 | Trạng thái lịch Đã ghi nhận | Lịch có `start_at` ở tương lai | Tạo lịch tương lai, mở `/schedules` hoặc `/timetable`. | Lịch hiển thị trạng thái Đã ghi nhận. | Chưa thực hiện |
| TC-17 | Trạng thái lịch Đang thực hiện | Lịch có thời gian hiện tại nằm giữa `start_at` và `end_at` | Tạo hoặc chỉnh lịch sao cho hiện tại nằm trong khoảng thời gian lịch. | Lịch hiển thị trạng thái Đang thực hiện. | Chưa thực hiện |
| TC-18 | Trạng thái lịch Đã hoàn thành | Lịch có `end_at` trước thời điểm hiện tại | Tạo hoặc dùng lịch đã kết thúc, mở `/schedules` hoặc `/timetable`. | Lịch hiển thị trạng thái Đã hoàn thành. | Chưa thực hiện |
| TC-19 | Xem thời gian biểu theo ngày | Có lịch trong ngày cần xem | Mở `/timetable`, chọn ngày và xem kết quả. | Thời gian biểu hiển thị các mục lịch trong ngày, sắp xếp theo thời gian. | Chưa thực hiện |
| TC-20 | Xóa mục trong thời gian biểu | Một lịch đang hiển thị trên `/timetable` | Nhấn xóa mục lịch và xác nhận. | Mục lịch bị xóa và không còn hiển thị trong thời gian biểu. | Chưa thực hiện |
| TC-21 | Xem lịch tháng | Có lịch và ngày quan trọng | Mở `/calendar` hoặc `/schedules/calendar`. | FullCalendar tải và hiển thị sự kiện lịch cùng ngày quan trọng. | Chưa thực hiện |
| TC-22 | Thêm nhắc nhở | Tiêu đề, giờ nhắc, kiểu lặp | Mở `/reminders/create`, nhập dữ liệu và lưu. | Nhắc nhở mới xuất hiện trong danh sách. | Chưa thực hiện |
| TC-23 | Sửa nhắc nhở | Nhắc nhở đã tồn tại | Mở trang sửa nhắc nhở, cập nhật dữ liệu và lưu. | Nhắc nhở được cập nhật đúng. | Chưa thực hiện |
| TC-24 | Xóa nhắc nhở | Nhắc nhở đã tồn tại | Mở trang xóa nhắc nhở và xác nhận. | Nhắc nhở bị xóa khỏi danh sách. | Chưa thực hiện |
| TC-25 | Nhắc nhở theo khoảng thời gian | Kiểu lặp: interval, số giờ/phút hợp lệ | Tạo nhắc nhở interval và mở danh sách nhắc nhở. | Nhắc nhở được lưu với khoảng lặp hợp lệ. | Chưa thực hiện |
| TC-26 | Bật/tắt thông báo trình duyệt | Trình duyệt hỗ trợ thông báo | Bật thông báo trong giao diện, cấp quyền nếu được hỏi, sau đó tắt lại. | Trạng thái thông báo thay đổi đúng, không làm lỗi trang. | Chưa thực hiện |
| TC-27 | Thêm/sửa/xóa ngày quan trọng | Tiêu đề, ngày, loại sự kiện, số ngày nhắc trước | Tạo ngày quan trọng, sửa thông tin, sau đó xóa. | Dữ liệu ngày quan trọng được thêm, cập nhật và xóa đúng. | Chưa thực hiện |
| TC-28 | Xem nhật ký thời gian | Có lịch hoặc nhật ký trong ngày | Mở `/time-logs`, chọn ngày cần xem. | Bảng báo cáo hiển thị hoạt động, danh mục, thời gian kế hoạch, thời lượng kế hoạch và ghi chú. | Chưa thực hiện |
| TC-29 | Đặt lại nhật ký thời gian | Tài khoản có dữ liệu `time_logs` | Mở `/time-logs`, nhấn đặt lại, xác nhận. | Nhật ký thời gian của người dùng hiện tại bị xóa, dữ liệu khác vẫn giữ nguyên. | Chưa thực hiện |
| TC-30 | Gợi ý tối ưu khoảng trống | Hoạt động hợp lệ, khoảng ngày, thời lượng cần tìm | Mở `/optimizer`, nhập dữ liệu và gửi yêu cầu gợi ý. | Hệ thống hiển thị các khoảng trống phù hợp hoặc thông báo nếu không có. | Chưa thực hiện |
| TC-31 | Xem trợ lý thông minh | Tài khoản có dữ liệu lịch, nhật ký hoặc ngày quan trọng | Mở `/assistant`. | Hệ thống hiển thị danh sách nhận xét và khuyến nghị phù hợp với dữ liệu hiện có. | Chưa thực hiện |
| TC-32 | Chuyển giao diện sáng/tối | Giao diện đang mở | Sử dụng công tắc theme trong giao diện. | Giao diện chuyển sáng/tối, logo và màu chữ vẫn hiển thị rõ. | Chưa thực hiện |
| TC-33 | Chuyển ngôn ngữ VI/EN | Giao diện đang mở | Chọn VI hoặc EN trong công tắc ngôn ngữ. | Nhãn giao diện, thông báo và trạng thái được chuyển theo ngôn ngữ đã chọn. | Chưa thực hiện |

## 3. Ghi chú kiểm thử

- Nên kiểm thử trên tài khoản demo trước, sau đó kiểm thử thêm trên tài khoản người dùng mới nếu có.
- Với trạng thái lịch theo thời gian thực, cần tạo dữ liệu có thời điểm trước, trong và sau thời gian hiện tại.
- Với thông báo trình duyệt, kết quả có thể phụ thuộc quyền thông báo và cấu hình của trình duyệt/hệ điều hành.
- Với chức năng đặt lại nhật ký thời gian, cần kiểm tra lại các bảng hoạt động, danh mục, lịch, nhắc nhở và ngày quan trọng để bảo đảm không bị xóa nhầm.
