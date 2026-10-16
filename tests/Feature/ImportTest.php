<?php

namespace Tests\Feature;

use App\Http\Controllers\ExportController;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\BaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(BaseSeeder::class);
    }

    private function categoryId(int $order): int
    {
        return Category::where('order', $order)->value('id');
    }

    /**
     * An .xlsx upload with a Days sheet and, unless null, a Tags sheet.
     */
    private function file(array $days, ?array $tags = []): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'import') . '.xlsx';
        $writer = SimpleExcelWriter::create($path);
        $writer->nameCurrentSheet('Days');
        $writer->addRows($days);
        if ($tags !== null) {
            $writer->addNewSheetAndMakeItCurrent('Tags');
            $writer->addRows($tags);
        }
        unset($writer); // the destructor finishes the file

        return new UploadedFile($path, 'tellmolly.xlsx', null, null, true);
    }

    private function import(User $user, UploadedFile $file)
    {
        return $this->actingAs($user)->from('/profile')->post('/import', ['file' => $file]);
    }

    public function test_an_export_imports_into_another_account(): void
    {
        $source = User::factory()->create();
        $hiking = $source->tags()->create(['name' => 'Hiking', 'color' => '#2465bf']);
        $source->days()->create(['date' => '2026-05-01', 'category_id' => $this->categoryId(Category::GREAT), 'comment' => "Summit\nday", 'grateful_for' => 'Sunshine'])->tags()->attach($hiking);
        $source->days()->create(['date' => '2026-05-02', 'category_id' => $this->categoryId(Category::BAD)]);

        $path = tempnam(sys_get_temp_dir(), 'export') . '.xlsx';
        $writer = SimpleExcelWriter::create($path);
        ExportController::write($writer, $source);
        unset($writer); // the destructor finishes the file

        $target = User::factory()->create();
        $this->import($target, new UploadedFile($path, 'tellmolly.xlsx', null, null, true))
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('import', 'Imported 2 days and 1 new tag.');

        $days = $target->days()->with('category', 'tags')->orderBy('date')->get();
        $this->assertSame(['2026-05-01', '2026-05-02'], $days->pluck('date')->all());
        $this->assertSame([Category::GREAT, Category::BAD], $days->pluck('category.order')->all());
        $this->assertSame("Summit\nday", $days[0]->comment);
        $this->assertSame('Sunshine', $days[0]->grateful_for);
        $this->assertSame(['Hiking'], $days[0]->tags->pluck('name')->all());
        $this->assertSame('#2465bf', $target->tags()->first()->color);
        $this->assertSame('hiking', $target->tags()->first()->slug);
        // The source account is untouched.
        $this->assertSame(2, $source->days()->count());
    }

    public function test_existing_days_are_kept_and_tags_are_reused(): void
    {
        $user = User::factory()->create();
        $user->tags()->create(['name' => 'Hiking', 'color' => '#111111']);
        $user->days()->create(['date' => '2026-05-01', 'category_id' => $this->categoryId(Category::GOOD), 'comment' => 'Mine']);

        $this->import($user, $this->file([
            ['date' => '2026-05-01', 'category' => 1, 'comment' => 'From the file', 'grateful_for' => '', 'tags' => ''],
            ['date' => '2026-05-03', 'category' => 2, 'comment' => '', 'grateful_for' => '', 'tags' => 'hiking, Family'],
        ], [['name' => 'Hiking', 'color' => '#222222']]))
            ->assertSessionHas('import', 'Imported 1 day and 1 new tag. 1 day you already had was left as they are.');

        $this->assertSame('Mine', $user->days()->where('date', '2026-05-01')->value('comment'));
        $this->assertSame(['Family', 'Hiking'], $user->tags()->orderBy('name')->pluck('name')->all());
        $this->assertSame('#111111', $user->tags()->where('name', 'Hiking')->value('color'));
        $this->assertEqualsCanonicalizing(['Hiking', 'Family'], $user->days()->where('date', '2026-05-03')->first()->tags->pluck('name')->all());
    }

    public function test_importing_the_same_file_twice_adds_nothing_the_second_time(): void
    {
        $user = User::factory()->create();
        $rows = [['date' => '2026-05-01', 'category' => 1], ['date' => '2026-05-02', 'category' => 2]];

        $this->import($user, $this->file($rows))->assertSessionHas('import', 'Imported 2 days.');
        $this->import($user, $this->file($rows))->assertSessionHas('import', 'Nothing new to import: you already had all 2 days in this file.');

        $this->assertSame(2, $user->days()->count());
    }

    public function test_one_bad_row_means_nothing_is_imported(): void
    {
        $user = User::factory()->create();

        $this->import($user, $this->file([
            ['date' => '2026-05-01', 'category' => 1, 'comment' => 'Fine'],
            ['date' => '2026-02-30', 'category' => 1, 'comment' => 'No such date'],
            ['date' => '2026-05-04', 'category' => 9, 'comment' => 'No such mood'],
            ['date' => '2026-05-05', 'category' => 2, 'comment' => str_repeat('a', 16001)],
            ['date' => '2026-05-01', 'category' => 2, 'comment' => 'Twice'],
        ], [['name' => 'Bad color', 'color' => 'red;background:url(x)']]))
            ->assertRedirect('/profile')
            ->assertSessionHasErrorsIn('import', 'file');

        $errors = session('errors')->getBag('import')->get('file');
        $this->assertStringContainsString('Tags row 2', $errors[0]);
        $this->assertTrue(collect($errors)->contains(fn($e) => str_starts_with($e, 'Days row 3:')));
        $this->assertTrue(collect($errors)->contains(fn($e) => str_starts_with($e, 'Days row 4:')));
        $this->assertTrue(collect($errors)->contains(fn($e) => str_starts_with($e, 'Days row 5:')));
        $this->assertTrue(collect($errors)->contains(fn($e) => str_contains($e, 'appears more than once')));
        $this->assertSame('Nothing was imported.', end($errors));
        $this->assertSame(0, $user->days()->count());
        $this->assertSame(0, $user->tags()->count());
    }

    public function test_the_file_cannot_target_another_user(): void
    {
        $victim = User::factory()->create();
        $user = User::factory()->create();

        $this->import($user, $this->file([
            ['user_id' => $victim->id, 'id' => 1, 'date' => '2026-05-01', 'category' => 1, 'comment' => 'Hi'],
        ]))->assertSessionHasNoErrors();

        $this->assertSame(0, $victim->days()->count());
        $this->assertSame(1, $user->days()->count());
    }

    public function test_hostile_text_is_stored_as_text_and_shown_escaped(): void
    {
        $user = User::factory()->create();
        $script = '<script>alert("xss")</script>';
        $sql = "'); DROP TABLE days; --";

        $this->import($user, $this->file([
            ['date' => '2026-05-01', 'category' => 1, 'comment' => $sql, 'grateful_for' => $script, 'tags' => '<img src=x onerror=alert(1)>'],
        ]))->assertSessionHasNoErrors();

        $this->assertTrue(Schema::hasTable('days'));
        $day = $user->days()->first();
        $this->assertSame($sql, $day->comment);
        $this->assertSame($script, $day->grateful_for);

        $this->actingAs($user)->get('/gratitude')
            ->assertOk()
            ->assertDontSee($script, false)
            ->assertSee(e($script), false);
        $this->actingAs($user)->get('/days')
            ->assertOk()
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    public function test_only_xlsx_exports_are_accepted(): void
    {
        $user = User::factory()->create();

        $this->import($user, UploadedFile::fake()->createWithContent('notes.txt', 'hello'))
            ->assertSessionHasErrorsIn('import', 'file');

        $this->import($user, UploadedFile::fake()->createWithContent('fake.xlsx', 'not a spreadsheet'))
            ->assertSessionHasErrorsIn('import', 'file');
        $this->assertStringContainsString('could not be read', session('errors')->getBag('import')->first('file'));

        $this->import($user, $this->file([['when' => '2026-05-01', 'mood' => 1]]))
            ->assertSessionHasErrorsIn('import', 'file');
        $this->assertStringContainsString('does not look like a Tell Molly export', session('errors')->getBag('import')->first('file'));
    }

    public function test_too_large_files_are_refused(): void
    {
        $this->import(User::factory()->create(), UploadedFile::fake()->create('big.xlsx', 3000))
            ->assertSessionHasErrorsIn('import', 'file');
    }

    public function test_a_file_without_a_tags_sheet_still_imports(): void
    {
        $user = User::factory()->create();

        $this->import($user, $this->file([['date' => '2026-05-01', 'category' => 3, 'tags' => 'Work']], null))
            ->assertSessionHasNoErrors();

        $this->assertSame(['Work'], $user->days()->first()->tags->pluck('name')->all());
        $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/', $user->tags()->first()->color);
    }

    public function test_unconfirmed_and_demo_accounts_cannot_import(): void
    {
        $this->import(User::factory()->unverified()->create(), $this->file([['date' => '2026-05-01', 'category' => 1]]))
            ->assertRedirect(route('verification.notice'));

        config(['calendar.demo.email' => 'demo@example.com']);
        $demo = User::factory()->create(['email' => 'demo@example.com']);
        $this->import($demo, $this->file([['date' => '2026-05-01', 'category' => 1]]))
            ->assertSessionHasErrorsIn('import', 'file');
        $this->assertSame(0, $demo->days()->count());
    }
}
