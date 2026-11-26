<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class PolicyPageController extends Controller
{
    /**
     * Danh sách 3 trang chính sách hỗ trợ cấu hình
     */
    public static function supportedPages(): array
    {
        return [
            'copyright' => [
                'name' => 'Chính sách lưu trữ & Bản quyền',
                'route' => 'copyright',
                'url_path' => '/chinh-sach-ban-quyen',
                'icon' => 'book-open',
            ],
            'terms' => [
                'name' => 'Điều khoản sử dụng',
                'route' => 'terms',
                'url_path' => '/dieu-khoan-su-dung',
                'icon' => 'shield-check',
            ],
            'privacy' => [
                'name' => 'Chính sách bảo mật',
                'route' => 'privacy',
                'url_path' => '/chinh-sach-bao-mat',
                'icon' => 'lock',
            ],
        ];
    }

    /**
     * Dữ liệu mặc định cho từng trang
     */
    public static function getDefaults(string $page): array
    {
        $siteName = Setting::get('site_name', 'TRƯNG BÀY');

        switch ($page) {
            case 'copyright':
                return [
                    'kicker' => 'ARCHIVAL & INTELLECTUAL PROPERTY POLICY',
                    'title' => 'Chính Sách Lưu Trữ & Bản Quyền Tác Phẩm',
                    'meta' => 'Chính sách lưu trữ, giám tuyển và bảo vệ quyền tác giả, sở hữu trí tuệ đối với các ấn phẩm sách và truyện nghệ thuật.',
                    'content' => <<<HTML
<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">1. Tuyên bố tôn trọng bản quyền &amp; Sở hữu trí tuệ</h2>
    <p>
        <strong>{$siteName}</strong> cam kết tôn trọng tuyệt đối quyền tác giả, quyền tác phẩm và quyền sở hữu trí tuệ của các tác giả, dịch giả, họa sĩ minh họa cũng như các nhà xuất bản tại Việt Nam và quốc tế.
    </p>
    <p>
        Toàn bộ nhãn hiệu, tên tác phẩm, hình ảnh minh họa bìa sách và các nội dung trích lục xuất hiện trên nền tảng đều thuộc quyền sở hữu của các chủ thể quyền hợp pháp tương ứng.
    </p>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">2. Mục đích lưu trữ &amp; Giám tuyển phi thương mại</h2>
    <p>
        Không gian trưng bày được thành lập với tôn chỉ phi lợi nhuận nhằm:
    </p>
    <ul class="taschen-legal-list">
        <li>Lưu trữ số hóa và quảng bá các ấn bản sách, truyện tranh nghệ thuật có giá trị văn hóa, lịch sử và mỹ thuật xuất bản cao cấp.</li>
        <li>Cung cấp thư viện tra cứu thông tin tham khảo, chỉ dẫn thư mục học (catalogue raisonné) cho độc giả, giới nghiên cứu và người đam mê sưu tầm sách.</li>
        <li>Tôn vinh giá trị lao động sáng tạo của tác giả và nghệ thuật làm sách tinh hoa theo tiêu chuẩn triển lãm quốc tế.</li>
    </ul>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">3. Sử dụng hình ảnh bìa &amp; Trích dẫn học thuật (Fair Use)</h2>
    <p>
        Việc đăng tải hình ảnh bìa sách 3D, thông số kỹ thuật (kích thước, số trang, nhà xuất bản, năm phát hành) và các đoạn tóm tắt nội dung trên website được thực hiện dựa trên nguyên tắc sử dụng hợp lý (Fair Use) theo Luật Sở hữu Trí tuệ Việt Nam và các công ước quốc tế (Công ước Bern):
    </p>
    <ul class="taschen-legal-list">
        <li><strong>Hình ảnh bìa sách:</strong> Sử dụng nhằm mục đích nhận diện trực quan ấn phẩm triển lãm và cung cấp thông tin minh họa cho độc giả.</li>
        <li><strong>Trích đoạn &amp; Tóm lược:</strong> Chỉ bao gồm các đoạn giới thiệu ngắn hoặc trích dẫn phê bình editorial nhằm mục đích định hướng thưởng lãm, tuyệt đối không sao chép toàn văn nội dung tác phẩm.</li>
    </ul>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">4. Bảo tồn di sản &amp; Tôn vinh các ấn bản quý hiếm</h2>
    <p>
        Đối với các ấn bản giới hạn, các tác phẩm đã ngưng in (out of print) hoặc các bộ sưu tập di sản, không gian trưng bày đóng vai trò là nơi lưu giữ dữ liệu mỹ thuật để độc giả thế hệ tương lai có thể tiếp cận và cảm nhận giá trị lịch sử của từng thời kỳ xuất bản.
    </p>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">5. Quy trình tiếp nhận yêu cầu &amp; Xử lý bản quyền (Takedown Policy)</h2>
    <p>
        Nếu bạn là tác giả, chủ sở hữu quyền tác giả hoặc đại diện pháp lý hợp pháp của bất kỳ tác phẩm nào được hiển thị trên hệ thống và có mong muốn:
    </p>
    <ul class="taschen-legal-list">
        <li>Cập nhật, bổ sung thông tin chính xác về tác giả, dịch giả, hoặc đơn vị xuất bản;</li>
        <li>Thay đổi hình ảnh trưng bày bìa sách đạt chuẩn chất lượng cao hơn;</li>
        <li>Hoặc yêu cầu gỡ bỏ thông tin ấn phẩm khỏi không gian trưng bày;</li>
    </ul>
    <p>
        Vui lòng gửi văn bản thông báo chính thức đến Ban Quản Trị. Chúng tôi cam kết tiếp nhận và thực hiện xử lý phản hồi trong vòng <strong>24 đến 48 giờ làm việc</strong>.
    </p>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">6. Thông tin liên hệ Ban Biên Tập &amp; Pháp Lý</h2>
    <div class="taschen-legal-contact-box">
        <p><strong>BAN BIÊN TẬP &amp; PHÁP LÝ TRIỂN LÃM SÁCH</strong></p>
        <p>Không gian Trưng Bày Sách &amp; Truyện Nghệ Thuật Cao Cấp</p>
        <p>Email tiếp nhận yêu cầu bản quyền: <em>copyright@trungbaytruyen.demopage.top</em></p>
    </div>
</section>
HTML
                ];

            case 'terms':
                return [
                    'kicker' => 'TERMS & CONDITIONS',
                    'title' => 'Điều Khoản Và Điều Kiện Chung Sử Dụng',
                    'meta' => 'Các điều khoản, điều kiện chung về việc truy cập, tra cứu và trải nghiệm không gian trưng bày sách nghệ thuật.',
                    'content' => <<<HTML
<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">1. Phạm vi áp dụng</h2>
    <p>
        Các Điều khoản và Điều kiện Chung này áp dụng cho toàn bộ độc giả, khách tham quan và người dùng khi truy cập, tra cứu thông tin danh mục, hoặc tương tác với bất kỳ tính năng nào thuộc nền tảng trưng bày trực tuyến <strong>{$siteName}</strong>.
    </p>
    <p>
        Bằng việc tiếp tục truy cập và sử dụng website, bạn xác nhận đã đọc, hiểu rõ và đồng ý tuân thủ đầy đủ các điều khoản này mà không có bất kỳ ngoại lệ nào.
    </p>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">2. Mục đích hoạt động &amp; Tính chất trưng bày trực tuyến</h2>
    <p>
        Không gian trưng bày trực tuyến được tổ chức như một phòng triển lãm số nhằm lưu giữ và giới thiệu các ấn bản sách, truyện tranh nghệ thuật đặc sắc.
    </p>
    <p>
        Việc hiển thị sản phẩm, hình ảnh minh họa 3D và thông tin chi tiết trên trang web đóng vai trò như một danh mục thư mục học trực quan và nguồn tư liệu tra cứu văn hóa nghệ thuật. Mọi hình ảnh ấn bản được số hóa với độ chính xác cao nhất về màu sắc và tỉ lệ thực tế.
    </p>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">3. Quyền sở hữu trí tuệ</h2>
    <p>
        Mọi thiết kế giao diện, bố cục thẩm mỹ, mã nguồn phần mềm, biểu tượng nhận diện thương hiệu và các bài viết giám tuyển độc quyền đều thuộc quyền sở hữu trí tuệ của ban quản trị nền tảng hoặc đã được cấp phép sử dụng.
    </p>
    <p>
        Nghiêm cấm mọi hành vi sao chép toàn phần hoặc trích đoạn, can thiệp mã nguồn, sử dụng công cụ thu thập dữ liệu tự động (scraping/crawling) mà không có văn bản chấp thuận trước.
    </p>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">4. Quy định đối với người dùng</h2>
    <p>
        Khi trải nghiệm không gian trưng bày, độc giả cam kết:
    </p>
    <ul class="taschen-legal-list">
        <li>Không thực hiện các hành vi gây cản trở, làm quá tải máy chủ hoặc xâm nhập trái phép vào hệ thống dữ liệu.</li>
        <li>Cung cấp thông tin liên hệ trung thực, chính xác khi gửi form bày tỏ quan tâm hoặc liên hệ với ban biên tập.</li>
        <li>Tôn trọng cộng đồng độc giả và các giá trị văn hóa nghệ thuật được tôn vinh trên nền tảng.</li>
    </ul>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">5. Giới hạn trách nhiệm</h2>
    <p>
        Chúng tôi nỗ lực tối đa để đảm bảo mọi thông tin thư mục, năm phát hành, số trang và hình ảnh được cập nhật chuẩn xác. Tuy nhiên, nội dung chỉ mang tính chất tham khảo học thuật và lưu trữ văn hóa. Chúng tôi có quyền điều chỉnh, bổ sung hoặc tái cơ cấu danh mục trưng bày bất kỳ lúc nào mà không cần thông báo trước.
    </p>
</section>
HTML
                ];

            case 'privacy':
                return [
                    'kicker' => 'PRIVACY POLICY & DATA PROTECTION',
                    'title' => 'Chính Sách Bảo Mật & Quyền Riêng Tư',
                    'meta' => 'Chính sách bảo mật thông tin cá nhân, quyền riêng tư và quy định lưu trữ dữ liệu độc giả.',
                    'content' => <<<HTML
<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">1. Cam kết bảo mật &amp; Phạm vi áp dụng</h2>
    <p>
        <strong>{$siteName}</strong> tôn trọng và coi trọng quyền riêng tư của mỗi độc giả khi ghé thăm không gian triển lãm của chúng tôi. Chính sách này giải thích cách chúng tôi thu thập, sử dụng và bảo vệ thông tin khi bạn tương tác trên website.
    </p>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">2. Thông tin chúng tôi thu thập</h2>
    <p>
        Chúng tôi chỉ thu thập các thông tin cần thiết nhằm tối ưu hóa trải nghiệm đọc và tra cứu của bạn:
    </p>
    <ul class="taschen-legal-list">
        <li><strong>Thông tin bạn chủ động cung cấp:</strong> Họ tên, số điện thoại, email hoặc ghi chú khi bạn gửi biểu mẫu quan tâm ấn phẩm hay liên hệ trao đổi.</li>
        <li><strong>Dữ liệu tra cứu và tương tác ẩn danh:</strong> Từ khóa tìm kiếm, thể loại sách quan tâm, thời gian tương tác nhằm nâng cao tốc độ phản hồi và gợi ý danh mục phù hợp.</li>
        <li><strong>Thông tin kỹ thuật tiêu chuẩn:</strong> Địa chỉ IP, trình duyệt web và thiết bị nhằm đảm bảo an ninh hệ thống và ngăn chặn tấn công tự động.</li>
    </ul>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">3. Mục đích sử dụng dữ liệu</h2>
    <p>
        Thông tin thu thập được chỉ sử dụng cho các mục đích chính đáng sau:
    </p>
    <ul class="taschen-legal-list">
        <li>Liên hệ phản hồi và hỗ trợ giải đáp thắc mắc của độc giả về các ấn bản sách.</li>
        <li>Cải thiện tốc độ tải trang, nâng cao chất lượng hiển thị hình ảnh mỹ thuật trên mọi kích thước màn hình.</li>
        <li>Bảo vệ an toàn dữ liệu và quyền lợi hợp pháp của người dùng trên nền tảng.</li>
    </ul>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">4. Cam kết không chia sẻ dữ liệu</h2>
    <p>
        Chúng tôi cam kết tuyệt đối không bán, trao đổi hoặc tiết lộ thông tin cá nhân của bạn cho bất kỳ bên thứ ba nào vì mục đích thương mại hoặc quảng cáo trái phép.
    </p>
</section>

<section class="taschen-legal-section">
    <h2 class="taschen-legal-h2">5. Quyền của độc giả</h2>
    <p>
        Bạn có toàn quyền yêu cầu chúng tôi kiểm tra, cập nhật hoặc xóa bỏ vĩnh viễn thông tin cá nhân đã lưu trong hệ thống bằng cách gửi email trực tiếp tới bộ phận quản trị.
    </p>
</section>
HTML
                ];

            default:
                return [
                    'kicker' => '',
                    'title' => '',
                    'meta' => '',
                    'content' => '',
                ];
        }
    }

    /**
     * Trang chỉnh sửa cấu hình các trang chính sách
     */
    public function edit(Request $request)
    {
        $supported = self::supportedPages();
        $tab = $request->query('tab', 'copyright');
        if (! array_key_exists($tab, $supported)) {
            $tab = 'copyright';
        }

        $pagesData = [];
        foreach (array_keys($supported) as $pageKey) {
            $defaults = self::getDefaults($pageKey);
            $pagesData[$pageKey] = [
                'kicker' => Setting::get("page_{$pageKey}_kicker", $defaults['kicker']),
                'title' => Setting::get("page_{$pageKey}_title", $defaults['title']),
                'meta' => Setting::get("page_{$pageKey}_meta", $defaults['meta']),
                'content' => Setting::get("page_{$pageKey}_content", $defaults['content']),
            ];
        }

        return view('admin.policy_pages.edit', [
            'supported' => $supported,
            'currentTab' => $tab,
            'pagesData' => $pagesData,
        ]);
    }

    /**
     * Cập nhật nội dung một trang chính sách
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'page' => ['required', 'string', 'in:copyright,terms,privacy'],
            'kicker' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'meta' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
        ]);

        $page = $data['page'];
        Setting::set("page_{$page}_kicker", $data['kicker'] ?? '');
        Setting::set("page_{$page}_title", $data['title']);
        Setting::set("page_{$page}_meta", $data['meta'] ?? '');
        Setting::set("page_{$page}_content", $data['content']);

        $pageName = self::supportedPages()[$page]['name'] ?? 'trang';

        return redirect()->route('admin.policy-pages.edit', ['tab' => $page])
            ->with('success', "Đã lưu cấu hình \"{$pageName}\" thành công!");
    }

    /**
     * Khôi phục nội dung mẫu mặc định
     */
    public function resetDefault(Request $request)
    {
        $data = $request->validate([
            'page' => ['required', 'string', 'in:copyright,terms,privacy'],
        ]);

        $page = $data['page'];
        $defaults = self::getDefaults($page);

        Setting::set("page_{$page}_kicker", $defaults['kicker']);
        Setting::set("page_{$page}_title", $defaults['title']);
        Setting::set("page_{$page}_meta", $defaults['meta']);
        Setting::set("page_{$page}_content", $defaults['content']);

        $pageName = self::supportedPages()[$page]['name'] ?? 'trang';

        return redirect()->route('admin.policy-pages.edit', ['tab' => $page])
            ->with('success', "Đã khôi phục nội dung mẫu mặc định cho \"{$pageName}\"!");
    }
}
