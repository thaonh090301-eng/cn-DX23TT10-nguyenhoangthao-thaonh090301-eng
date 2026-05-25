# Đặc tả yêu cầu chức năng

## 1. Tổng quan

Tài liệu này mô tả các yêu cầu chức năng và phi chức năng của URTISYNC - Hệ thống tối ưu hóa quản lý thời gian cá nhân. Các yêu cầu được xây dựng dựa trên phiên bản hiện tại của ứng dụng web sử dụng PHP MVC, MySQL/MariaDB, HTML, CSS, JavaScript và FullCalendar.

## 2. Yêu cầu chức năng

| Mã yêu cầu | Tên chức năng | Mô tả | Đầu vào | Xử lý | Đầu ra | Ghi chú |
|---|---|---|---|---|---|---|
| FR-01 | Đăng nhập / đăng xuất | Cho phép người dùng đăng nhập và thoát khỏi hệ thống. | Email, mật khẩu. | Kiểm tra tài khoản, tạo hoặc hủy phiên đăng nhập. | Chuyển vào hệ thống khi hợp lệ hoặc hiển thị lỗi khi không hợp lệ. | Người dùng chưa đăng nhập được chuyển đến trang đăng nhập. |
| FR-02 | Trang chủ | Hiển thị nội dung giới thiệu và các bài viết/thói quen hỗ trợ tối ưu thời gian. | Trạng thái đăng nhập, ngôn ngữ hiện tại. | Tải nội dung trang chủ và hiển thị theo ngôn ngữ đang chọn. | Trang chủ có các thẻ nội dung, biểu tượng và thông tin định hướng thói quen. | Chỉ hiển thị trong giao diện chính sau khi đăng nhập. |
| FR-03 | Dashboard | Hiển thị tổng quan dữ liệu thời gian của người dùng. | Dữ liệu lịch, hoạt động, nhật ký thời gian. | Tổng hợp số liệu và hiển thị các khối thống kê. | Dashboard với các thông tin tổng quan. | Phục vụ đánh giá nhanh tình hình sử dụng thời gian. |
| FR-04 | Quản lý danh mục | Cho phép thêm, sửa, xóa và xem danh mục hoạt động. | Tên danh mục, màu sắc, thứ tự sắp xếp. | Kiểm tra dữ liệu, lưu theo tài khoản người dùng. | Danh sách danh mục được cập nhật. | Tên danh mục không trùng trong cùng tài khoản. |
| FR-05 | Quản lý hoạt động | Cho phép thêm, sửa, xóa và lọc hoạt động. | Danh mục, tên, mô tả, mức ưu tiên, thời lượng ước tính, trạng thái hoạt động. | Kiểm tra danh mục hợp lệ, lưu thông tin hoạt động. | Danh sách hoạt động được cập nhật. | Hoạt động còn lịch liên quan không được xóa. Nhật ký thời gian cũ được giữ lại khi xóa hoạt động. |
| FR-06 | Quản lý lịch | Cho phép tạo, sửa, xóa và lọc lịch theo ngày, hoạt động, tìm kiếm và trạng thái hiển thị. | Hoạt động, tiêu đề, thời gian bắt đầu, thời gian kết thúc, trạng thái lưu trữ, ghi chú, bộ lọc. | Kiểm tra thời gian, lưu lịch và xác định trạng thái hiển thị theo thời gian thực. | Danh sách lịch theo ngày hoặc tất cả ngày. | Trạng thái hiển thị gồm Đã ghi nhận, Đang thực hiện, Đã hoàn thành, Đã hủy. |
| FR-07 | Thời gian biểu | Hiển thị lịch trong một ngày theo dạng thời gian biểu. | Ngày xem, lịch của người dùng. | Lấy lịch theo ngày, sắp xếp theo thời gian bắt đầu và xác định trạng thái. | Bảng thời gian biểu trong ngày. | Hỗ trợ thêm và xóa lịch từ trang thời gian biểu. |
| FR-08 | Lịch tháng | Hiển thị lịch và ngày quan trọng trên giao diện lịch tháng. | Dữ liệu lịch, dữ liệu ngày quan trọng. | Cung cấp dữ liệu sự kiện cho FullCalendar. | Giao diện lịch tháng trực quan. | Truy cập qua `/calendar` hoặc `/schedules/calendar`. |
| FR-09 | Quản lý nhắc nhở | Cho phép thêm, sửa, xóa, bật/tắt nhắc nhở. | Tiêu đề, ghi chú, giờ nhắc, kiểu lặp, thứ trong tuần, khoảng lặp, trạng thái bật/tắt. | Kiểm tra dữ liệu và lưu nhắc nhở theo tài khoản. | Danh sách nhắc nhở được cập nhật. | Hỗ trợ không lặp, hằng ngày, hằng tuần và theo khoảng thời gian. |
| FR-10 | Thông báo trình duyệt / Windows notification | Cho phép người dùng nhận thông báo nhắc nhở trên trình duyệt và hệ điều hành nếu được hỗ trợ. | Quyền thông báo của trình duyệt, danh sách nhắc nhở trong ngày. | Kiểm tra nhắc nhở đang hoạt động và phát thông báo khi đến thời điểm. | Thông báo hiển thị trên thiết bị người dùng. | Người dùng có thể bật/tắt thông báo trong giao diện. |
| FR-11 | Quản lý ngày quan trọng | Cho phép thêm, sửa, xóa và xem các ngày quan trọng. | Tiêu đề, ngày diễn ra, loại sự kiện, ghi chú, số ngày nhắc trước, lặp hằng năm. | Lưu ngày quan trọng và tính ngày sắp tới khi cần hiển thị. | Danh sách ngày quan trọng và sự kiện trên lịch tháng. | Hỗ trợ nhiều loại sự kiện như kỳ thi, sinh nhật, hạn chót, du lịch. |
| FR-12 | Nhật ký thời gian | Hiển thị báo cáo thời gian theo ngày. | Ngày xem, dữ liệu lịch và nhật ký thời gian. | Lấy dữ liệu lịch theo ngày và hiển thị như dữ liệu thời gian chính thức. | Bảng báo cáo gồm hoạt động, danh mục, thời gian kế hoạch, thời lượng kế hoạch và ghi chú. | Trang có định hướng xem báo cáo, không có bước chỉnh sửa thủ công sau khi tạo lịch. |
| FR-13 | Đặt lại nhật ký thời gian | Cho phép xóa toàn bộ dữ liệu nhật ký thời gian của người dùng hiện tại. | Xác nhận từ người dùng. | Gửi yêu cầu POST và xóa `time_logs` theo `user_id` hiện tại. | Nhật ký thời gian được đặt lại, hiển thị thông báo thành công. | Không xóa hoạt động, danh mục, lịch, nhắc nhở, ngày quan trọng hoặc người dùng. |
| FR-14 | Gợi ý tối ưu khoảng trống | Gợi ý thời gian phù hợp để xếp một hoạt động vào lịch. | Hoạt động, khoảng ngày, thời lượng cần tìm, giờ bắt đầu sớm nhất, giờ kết thúc muộn nhất. | So sánh với lịch bận, tìm khoảng trống đủ thời lượng và chấm điểm gợi ý. | Danh sách khoảng trống phù hợp, có thể tạo lịch từ gợi ý. | Không tạo lịch nếu khoảng được chọn đã bị chiếm. |
| FR-15 | Trợ lý thông minh | Đưa ra nhận xét và khuyến nghị dựa trên dữ liệu lịch, nhắc nhở và ngày quan trọng. | Dữ liệu cá nhân của người dùng trong hệ thống. | Tổng hợp số liệu, phát hiện tình huống quá tải theo lịch, khoảng trống hoặc sự kiện sắp tới. | Danh sách gợi ý với mức độ thông tin, cảnh báo hoặc thành công. | Dựa trên quy tắc xử lý dữ liệu nội bộ của hệ thống. |
| FR-16 | Giao diện sáng/tối | Cho phép chuyển đổi giữa giao diện sáng và tối. | Lựa chọn theme của người dùng. | Lưu tùy chọn phía trình duyệt và thay đổi lớp giao diện. | Giao diện đổi màu phù hợp với theme. | Logo trong giao diện chính và trang đăng nhập thay đổi theo theme. |
| FR-17 | Hỗ trợ VI/EN | Cho phép chuyển đổi ngôn ngữ Việt/Anh. | Lựa chọn ngôn ngữ. | Lưu lựa chọn và lấy chuỗi hiển thị từ tệp ngôn ngữ. | Giao diện hiển thị theo ngôn ngữ được chọn. | Áp dụng cho nhãn menu, thông báo, trạng thái và nội dung chính. |

## 3. Yêu cầu phi chức năng

| Mã yêu cầu | Tên yêu cầu | Mô tả |
|---|---|---|
| NFR-01 | Tính dễ sử dụng | Giao diện cần rõ ràng, dễ thao tác, phù hợp với người dùng cá nhân. |
| NFR-02 | Tính nhất quán | Các trang cần giữ chung phong cách hiển thị, theme, menu, ngôn ngữ và thông báo. |
| NFR-03 | Tính bảo mật cơ bản | Các trang chức năng cần yêu cầu đăng nhập; dữ liệu được lọc theo người dùng hiện tại. |
| NFR-04 | Toàn vẹn dữ liệu | Các quan hệ cơ sở dữ liệu cần đảm bảo không tạo dữ liệu mồ côi ngoài các trường hợp được chủ động cho phép như nhật ký thời gian tách hoạt động. |
| NFR-05 | Tính tương thích | Ứng dụng chạy trên PHP, MySQL/MariaDB và trình duyệt hiện đại. |
| NFR-06 | Tính phản hồi | Giao diện cần hiển thị tốt trên màn hình máy tính và các kích thước nhỏ hơn. |
| NFR-07 | Tính bảo trì | Mã nguồn giữ kiến trúc PHP MVC đơn giản, tách controller, repository, view và service. |
| NFR-08 | Tính bản địa hóa | Hệ thống hỗ trợ tiếng Việt và tiếng Anh, không cố định chuỗi giao diện trong một ngôn ngữ duy nhất. |

## 4. Quy tắc nghiệp vụ quan trọng

- Người dùng chỉ được xem và thao tác dữ liệu thuộc tài khoản hiện tại.
- Lịch phải có thời gian kết thúc lớn hơn thời gian bắt đầu.
- Lịch gắn với một hoạt động hợp lệ.
- Trạng thái lịch hiển thị được xác định theo thời gian hiện tại, không chỉ dựa vào trạng thái lưu trong cơ sở dữ liệu.
- Lịch đã hủy luôn hiển thị là Đã hủy.
- Hoạt động có lịch liên quan không được xóa.
- Khi xóa hoạt động có nhật ký thời gian, nhật ký được giữ lại và trường hoạt động được đặt về rỗng.
- Đặt lại nhật ký thời gian chỉ xóa dữ liệu `time_logs` của người dùng hiện tại.
- Nhắc nhở theo tuần cần có ngày trong tuần hợp lệ.
- Nhắc nhở theo khoảng thời gian cần có số phút lặp lớn hơn 0.
