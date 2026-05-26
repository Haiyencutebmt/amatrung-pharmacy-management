<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $staff = User::where('role', 'staff')->first();

        if (!$admin || !$staff) {
            $this->command->warn('⚠️  Chưa có user admin/staff, bỏ qua ArticleSeeder.');
            return;
        }

        $articles = [
            [
                'user_id'      => $admin->id,
                'title'        => 'Tác Dụng Của Hoàng Kỳ Trong Y Học Cổ Truyền',
                'slug'         => 'tac-dung-cua-hoang-ky-trong-y-hoc-co-truyen',
                'content'      => '<p>Hoàng Kỳ (Astragalus membranaceus) là một trong những vị thuốc quý của y học cổ truyền phương Đông. Vị thuốc này đã được sử dụng hàng ngàn năm để <strong>bổ khí, tăng cường sức đề kháng</strong> và hỗ trợ hệ miễn dịch.</p><h3>Công dụng chính</h3><ul><li>Bổ khí, tăng sức đề kháng</li><li>Hỗ trợ điều trị mệt mỏi, suy nhược cơ thể</li><li>Cải thiện chức năng thận</li><li>Hỗ trợ huyết áp ổn định</li></ul><p>Liều dùng thông thường: 10–30g/ngày sắc với nước uống. Nên hỏi ý kiến bác sĩ trước khi dùng.</p>',
                'is_published' => 1,
                'published_at' => now()->subDays(10),
            ],
            [
                'user_id'      => $staff->id,
                'title'        => 'Bài Thuốc Lục Vị Địa Hoàng: Bổ Thận Âm Hiệu Quả',
                'slug'         => 'bai-thuoc-luc-vi-dia-hoang-bo-than-am-hieu-qua',
                'content'      => '<p>Bài thuốc Lục Vị Địa Hoàng là một trong những bài thuốc kinh điển nhất của Đông y, được ghi chép trong "Tiểu Nhi Dược Chứng Trực Quyết" từ thời Tống. Bài thuốc gồm 6 vị: <strong>Thục Địa, Sơn Dược, Sơn Thù Du, Phục Linh, Mẫu Đơn Bì, Trạch Tả</strong>.</p><h3>Chỉ định</h3><ul><li>Thận âm hư: hoa mắt, chóng mặt, ù tai</li><li>Đau lưng mỏi gối</li><li>Ra mồ hôi trộm, miệng khô</li><li>Tiểu tiện ít, màu vàng</li></ul><blockquote><em>Lưu ý: Không dùng cho người thận dương hư (lạnh tay chân, tiểu đêm nhiều).</em></blockquote>',
                'is_published' => 1,
                'published_at' => now()->subDays(7),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'Ngải Cứu: Dược Liệu Quen Thuộc Với Nhiều Tác Dụng',
                'slug'         => 'ngai-cuu-duoc-lieu-quen-thuoc-voi-nhieu-tac-dung',
                'content'      => '<p>Ngải Cứu (Artemisia vulgaris) là loại cây thân thuộc trong vườn nhà người Việt. Không chỉ là gia vị trong ẩm thực, ngải cứu còn là vị thuốc quý trong y học cổ truyền.</p><h3>Tác dụng theo Đông y</h3><ul><li>Ôn kinh tán hàn</li><li>Cầm máu, trị băng huyết</li><li>Điều kinh, giảm đau bụng kinh</li><li>Ngâm chân giúp lưu thông khí huyết</li></ul><h3>Cách dùng phổ biến</h3><p>Ngâm chân: Nấu sôi 100g ngải cứu khô với 2 lít nước, để nguội bớt rồi ngâm 20 phút trước khi ngủ. Dùng 3–4 lần/tuần.</p>',
                'is_published' => 1,
                'published_at' => now()->subDays(3),
            ],
            [
                'user_id'      => $staff->id,
                'title'        => 'Chế Độ Ăn Uống Hỗ Trợ Điều Trị Gout Theo Đông Y',
                'slug'         => 'che-do-an-uong-ho-tro-dieu-tri-gout-theo-dong-y',
                'content'      => '<p>Gout (thống phong) là bệnh khớp do rối loạn chuyển hóa purine, gây tích tụ acid uric trong máu. Theo Đông y, bệnh thuộc phạm trù "tý chứng" — do phong, hàn, thấp xâm nhập.</p><h3>Thực phẩm nên dùng</h3><ul><li>Rau xanh: bắp cải, dưa leo, cà rốt</li><li>Trái cây ít ngọt: cherry, dâu tây</li><li>Nước lọc nhiều (2–3 lít/ngày)</li></ul><h3>Thực phẩm cần kiêng</h3><ul><li>Hải sản, nội tạng động vật</li><li>Bia rượu, thức uống có đường</li><li>Thịt đỏ liều cao</li></ul><p>Dược liệu hỗ trợ: Cao Gắm, Trạch Tả, Ý Dĩ — nên hỏi ý kiến thầy thuốc trước khi dùng.</p>',
                'is_published' => 1,
                'published_at' => now()->subDay(),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'Hướng Dẫn Sắc Thuốc Đông Y Đúng Cách Tại Nhà',
                'slug'         => 'huong-dan-sac-thuoc-dong-y-dung-cach-tai-nha',
                'content'      => '<p>Sắc thuốc đúng cách là yếu tố quan trọng quyết định hiệu quả của bài thuốc Đông y. Nhiều người sai lầm khi sắc thuốc vì chưa biết quy trình đúng.</p><h3>Các bước sắc thuốc cơ bản</h3><ol><li><strong>Ngâm dược liệu</strong>: Ngâm với nước sạch 20–30 phút trước khi sắc</li><li><strong>Lượng nước</strong>: Cho nước ngập dược liệu khoảng 3cm</li><li><strong>Lửa lớn ban đầu</strong>: Đun sôi, sau đó hạ lửa nhỏ</li><li><strong>Thời gian sắc</strong>: 30–45 phút tùy bài thuốc</li><li><strong>Lọc và uống</strong>: Uống ấm, chia 2–3 lần/ngày sau bữa ăn</li></ol><p><em>Không sắc bằng nồi kim loại thông thường. Nên dùng ấm đất hoặc nồi inox.</em></p>',
                'is_published' => 0,
                'published_at' => null,
            ],
        ];

        foreach ($articles as $articleData) {
            Article::create($articleData);
        }

        $this->command->info('✅ Đã tạo 5 bài viết mẫu (4 đã đăng, 1 bản nháp)');
    }
}
