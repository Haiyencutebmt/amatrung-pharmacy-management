<?php

namespace Tests\Feature;

use App\Models\HerbDictionaryEntry;
use App\Models\HerbDictionaryImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HerbDictionaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_browse_list_but_cannot_open_detail(): void
    {
        $entry = $this->makeEntry([
            'name' => 'Lá lốt',
            'slug' => 'la-lot',
        ]);

        $this->get(route('herb-dictionary.index'))
            ->assertOk()
            ->assertSee('Lá lốt')
            ->assertSee('Đăng nhập để xem');

        $this->get(route('herb-dictionary.show', $entry))
            ->assertRedirect(route('login'));
    }

    public function test_guest_search_is_not_applied(): void
    {
        $this->makeEntry(['name' => 'Gừng', 'slug' => 'gung']);
        $this->makeEntry(['name' => 'Lá lốt', 'slug' => 'la-lot']);

        $this->get(route('herb-dictionary.index', ['q' => 'khong-co']))
            ->assertOk()
            ->assertSee('Gừng')
            ->assertSee('Lá lốt')
            ->assertSee('Khách chỉ có thể xem danh sách');
    }

    public function test_logged_in_user_can_search_view_detail_and_favorite(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $entry = $this->makeEntry([
            'name' => 'Gừng',
            'slug' => 'gung',
            'scientific_name' => 'Zingiber officinale',
        ]);
        $this->makeEntry(['name' => 'Lá lốt', 'slug' => 'la-lot']);

        $this->actingAs($user)
            ->get(route('herb-dictionary.index', ['q' => 'Gừng']))
            ->assertOk()
            ->assertSee('Gừng')
            ->assertDontSee('Lá lốt');

        $this->actingAs($user)
            ->get(route('herb-dictionary.show', $entry))
            ->assertOk()
            ->assertSee('Zingiber officinale')
            ->assertSee('Không nên tự ý sử dụng thuốc nam');

        $this->actingAs($user)
            ->post(route('herb-dictionary.favorite', $entry))
            ->assertRedirect();

        $this->assertDatabaseHas('herb_dictionary_favorites', [
            'user_id' => $user->id,
            'entry_id' => $entry->id,
        ]);

        $this->actingAs($user)
            ->get(route('herb-dictionary.favorites'))
            ->assertOk()
            ->assertSee('Gừng');
    }

    public function test_staff_needs_dictionary_permission_to_manage_entries(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => [],
        ]);

        $this->actingAs($staff)
            ->get(route('admin.herb-dictionary.index'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_staff_with_permission_can_create_dictionary_entry_with_images(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => ['herb_dictionary.manage'],
        ]);

        $this->actingAs($staff)
            ->post(route('admin.herb-dictionary.store'), [
                'name' => 'Gừng',
                'scientific_name' => 'Zingiber officinale',
                'basic_info' => 'Cây thân thảo, phần thân rễ thường được dùng trong y học cổ truyền.',
                'effects' => 'Thường được ghi nhận trong hỗ trợ làm ấm, giảm lạnh bụng theo chỉ định.',
                'usage_notes' => 'Không tự phối hợp nhiều vị thuốc nếu chưa được thầy thuốc khám.',
                'status' => 'published',
                'images' => [
                    UploadedFile::fake()->image('gung.jpg', 800, 600),
                ],
            ])
            ->assertRedirect(route('admin.herb-dictionary.index'));

        $entry = HerbDictionaryEntry::firstOrFail();

        $this->assertSame('gung', $entry->slug);
        $this->assertCount(1, $entry->images);
        Storage::disk('public')->assertExists($entry->images->first()->image_path);
    }

    public function test_staff_with_permission_can_upload_dictionary_image_from_index(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => ['herb_dictionary.manage'],
        ]);
        $entry = $this->makeEntry([
            'name' => 'Rau má',
            'slug' => 'rau-ma',
        ]);

        $response = $this->actingAs($staff)
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post(route('admin.herb-dictionary.images.store', $entry), [
                'image' => UploadedFile::fake()->image('rau-ma.webp', 800, 600),
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('image.alt', 'Rau má')
            ->assertJsonPath('message', 'Đã thêm hình ảnh cho Rau má.');

        $entry->refresh();

        $this->assertCount(1, $entry->images);
        $this->assertSame(0, $entry->images->first()->sort_order);
        Storage::disk('public')->assertExists($entry->images->first()->image_path);
    }

    public function test_staff_with_permission_can_delete_dictionary_image(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => ['herb_dictionary.manage'],
        ]);
        $entry = $this->makeEntry([
            'name' => 'Cam Thảo Đất',
            'slug' => 'cam-thao-dat',
        ]);
        $image = HerbDictionaryImage::create([
            'entry_id' => $entry->id,
            'image_path' => 'herb-dictionary/wrong.jpg',
            'sort_order' => 0,
        ]);
        $disk = \Mockery::mock();
        $disk->shouldReceive('delete')
            ->once()
            ->with('herb-dictionary/wrong.jpg')
            ->andReturn(true);
        Storage::shouldReceive('disk')
            ->once()
            ->with('public')
            ->andReturn($disk);

        $this->actingAs($staff)
            ->from(route('admin.herb-dictionary.edit', $entry))
            ->delete(route('admin.herb-dictionary.images.destroy', [$entry, $image]))
            ->assertRedirect(route('admin.herb-dictionary.edit', $entry))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('herb_dictionary_images', [
            'id' => $image->id,
        ]);
    }

    public function test_staff_with_permission_can_bulk_delete_dictionary_entries(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => ['herb_dictionary.manage'],
        ]);
        $entryA = $this->makeEntry([
            'name' => 'Dược liệu A',
            'slug' => 'duoc-lieu-a',
        ]);
        $entryB = $this->makeEntry([
            'name' => 'Dược liệu B',
            'slug' => 'duoc-lieu-b',
        ]);
        $entryC = $this->makeEntry([
            'name' => 'Dược liệu C',
            'slug' => 'duoc-lieu-c',
        ]);
        HerbDictionaryImage::create([
            'entry_id' => $entryA->id,
            'image_path' => 'herb-dictionary/a.jpg',
            'sort_order' => 0,
        ]);
        HerbDictionaryImage::create([
            'entry_id' => $entryB->id,
            'image_path' => 'herb-dictionary/b.jpg',
            'sort_order' => 0,
        ]);
        $disk = \Mockery::mock();
        $disk->shouldReceive('delete')
            ->once()
            ->with(\Mockery::on(function ($paths) {
                sort($paths);
                return $paths === ['herb-dictionary/a.jpg', 'herb-dictionary/b.jpg'];
            }))
            ->andReturn(true);
        Storage::shouldReceive('disk')
            ->once()
            ->with('public')
            ->andReturn($disk);

        $this->actingAs($staff)
            ->from(route('admin.herb-dictionary.index'))
            ->delete(route('admin.herb-dictionary.bulk-destroy'), [
                'ids' => [$entryA->id, $entryB->id],
            ])
            ->assertRedirect(route('admin.herb-dictionary.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('herb_dictionary_entries', ['id' => $entryA->id]);
        $this->assertDatabaseMissing('herb_dictionary_entries', ['id' => $entryB->id]);
        $this->assertDatabaseHas('herb_dictionary_entries', ['id' => $entryC->id]);
        $this->assertDatabaseMissing('herb_dictionary_images', ['entry_id' => $entryA->id]);
        $this->assertDatabaseMissing('herb_dictionary_images', ['entry_id' => $entryB->id]);
    }

    public function test_staff_with_permission_can_import_dictionary_entries_without_images(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'permissions' => ['herb_dictionary.manage'],
        ]);

        $buffer = fopen('php://temp', 'r+');
        fputcsv($buffer, ['MẪU NHẬP TỪ ĐIỂN THUỐC NAM']);
        fputcsv($buffer, ['Hình ảnh không nhập qua file, có thể bổ sung sau ở nút Sửa từng mục.']);
        fputcsv($buffer, []);
        fputcsv($buffer, [
            'Tên thuốc nam *',
            'Tên khoa học',
            'Tên gọi khác',
            'Họ thực vật',
            'Bộ phận dùng',
            'Tính vị / đặc điểm',
            'Thông tin cơ bản',
            'Tác dụng',
            'Lưu ý khi sử dụng',
            'Khuyến cáo an toàn',
            'Trạng thái',
        ]);
        fputcsv($buffer, [
            'Rau má',
            'Centella asiatica',
            'Tích tuyết thảo',
            'Apiaceae',
            'Toàn cây',
            'Vị đắng nhẹ, tính mát',
            'Cây thuốc nam thường gặp, cần định danh đúng trước khi sử dụng.',
            'Thường được ghi nhận trong hỗ trợ thanh nhiệt theo chỉ định.',
            'Không tự dùng thay thuốc điều trị.',
            '',
            'Đã xuất bản',
        ]);
        rewind($buffer);
        $csv = stream_get_contents($buffer);
        fclose($buffer);

        $path = tempnam(storage_path(), 'herb_dictionary_import_');
        file_put_contents($path, "\xEF\xBB\xBF" . $csv);

        $file = new UploadedFile($path, 'tu-dien-thuoc-nam.csv', 'text/csv', null, true);

        $this->actingAs($staff)
            ->post(route('admin.herb-dictionary.import'), [
                'dictionary_file' => $file,
            ])
            ->assertRedirect(route('admin.herb-dictionary.index'))
            ->assertSessionHas('success');

        $entry = HerbDictionaryEntry::where('name', 'Rau má')->firstOrFail();

        $this->assertSame('rau-ma', $entry->slug);
        $this->assertSame('published', $entry->status);
        $this->assertSame('Centella asiatica', $entry->scientific_name);
        $this->assertStringContainsString('Không nên tự ý sử dụng thuốc nam', $entry->safety_warning);
        $this->assertCount(0, $entry->images);

        $this->actingAs($staff)
            ->get(route('admin.herb-dictionary.index'))
            ->assertOk()
            ->assertSee('Nhập danh sách bằng Excel')
            ->assertSee('Tải mẫu Excel');

        $response = $this->actingAs($staff)
            ->get(route('admin.herb-dictionary.download-template'))
            ->assertOk();

        $this->assertStringContainsString(
            'mau_import_tu_dien_thuoc_nam.xlsx',
            $response->headers->get('content-disposition')
        );
    }

    private function makeEntry(array $attributes = []): HerbDictionaryEntry
    {
        return HerbDictionaryEntry::create(array_merge([
            'name' => 'Cây thuốc',
            'slug' => 'cay-thuoc',
            'basic_info' => 'Thông tin cơ bản về cây thuốc.',
            'effects' => 'Tác dụng tham khảo, không thay thế thăm khám.',
            'status' => 'published',
        ], $attributes));
    }
}
