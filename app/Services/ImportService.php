<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Day;
use App\Models\Tag;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\SimpleExcel\SimpleExcelReader;
use Throwable;

/**
 * Imports a Tell Molly export (the .xlsx from ExportController) into the
 * signed-in user's account.
 *
 * Safety rules:
 * - Everything is written to the requesting user. The file cannot name a user;
 *   unknown columns (including any ids) are ignored.
 * - Moods are mapped from their number (1-5) to the existing categories, so a
 *   file can never supply the category HTML that is rendered unescaped.
 * - Tag colors must be #rrggbb, since they end up in a style attribute. All
 *   other text is length-checked and only ever shown escaped.
 * - All writes use query bindings; nothing from the file reaches raw SQL.
 * - The whole file is validated first and then written in one transaction, so
 *   a bad row means nothing is imported.
 * - Days that already exist are left untouched.
 */
class ImportService
{
    public const MAX_FILE_KILOBYTES = 2048;

    public const MAX_DAYS = 10000;

    public const MAX_TAGS = 1000;

    private const MAX_TAGS_PER_DAY = 50;

    private const MAX_ERRORS_SHOWN = 10;

    /**
     * @return array{days: int, skipped: int, tags: int}
     *
     * @throws ValidationException
     */
    public function import(Request $request): array
    {
        $request->validateWithBag('import', [
            'file' => ['required', 'file', 'extensions:xlsx', 'max:' . self::MAX_FILE_KILOBYTES],
        ]);

        $user = $request->user();

        if ($user->email === config('calendar.demo.email')) {
            $this->fail(['The demo account cannot import data.']);
        }

        [$dayRows, $tagRows] = $this->read($request->file('file')->getRealPath());

        $errors = [];
        $tags = $this->validateTags($tagRows, $errors);
        $days = $this->validateDays($dayRows, $errors);

        if ($errors) {
            $this->fail($errors);
        }

        // Tags only named on days get a generated color.
        foreach ($days as $day) {
            foreach ($day['tags'] as $name) {
                $tags[mb_strtolower($name)] ??= ['name' => $name, 'color' => Tag::colorFor($name)];
            }
        }

        if (count($tags) > self::MAX_TAGS) {
            $this->fail(['A file can use at most ' . self::MAX_TAGS . ' different tags.']);
        }

        return DB::transaction(fn() => $this->write($user, $days, $tags));
    }

    /**
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>}
     */
    private function read(string $path): array
    {
        try {
            $days = SimpleExcelReader::create($path, 'xlsx')->fromSheetName('Days');
            $headers = $days->getHeaders() ?? [];
            // One row more than allowed, to notice a file that is too long without reading all of it.
            $dayRows = $days->take(self::MAX_DAYS + 1)->getRows()->all();
        } catch (Throwable) {
            $this->fail(['This file could not be read. Please upload an export file (.xlsx) from Tell Molly.']);
        }

        try {
            $tagRows = SimpleExcelReader::create($path, 'xlsx')->fromSheetName('Tags')
                ->take(self::MAX_TAGS + 1)->getRows()->all();
        } catch (Throwable) {
            $tagRows = []; // the Tags sheet is optional
        }

        if ($dayRows && (! in_array('date', $headers, true) || ! in_array('category', $headers, true))) {
            $this->fail(['This file does not look like a Tell Molly export: the Days sheet needs "date" and "category" columns.']);
        }

        if (count($dayRows) > self::MAX_DAYS) {
            $this->fail(['A file can contain at most ' . self::MAX_DAYS . ' days.']);
        }

        if (count($tagRows) > self::MAX_TAGS) {
            $this->fail(['A file can contain at most ' . self::MAX_TAGS . ' tags.']);
        }

        return [$dayRows, $tagRows];
    }

    /**
     * @return array<string, array{name: string, color: string}> keyed by lowercase name
     */
    private function validateTags(array $rows, array &$errors): array
    {
        $tags = [];

        foreach ($rows as $index => $row) {
            $data = [
                'name' => $this->text($row['name'] ?? null),
                'color' => $this->text($row['color'] ?? null),
            ];

            if ($data['name'] === null && $data['color'] === null) {
                continue; // blank row
            }

            $validator = Validator::make($data, [
                'name' => ['required', 'string', 'max:255'],
                'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            ], [
                'color.regex' => 'The color must be a hex color like #2465bf.',
            ]);

            if ($validator->fails()) {
                $this->collect($errors, 'Tags', $index, $validator->errors()->all());

                continue;
            }

            $tags[mb_strtolower($data['name'])] ??= $data;
        }

        return $tags;
    }

    /**
     * @return array<int, array{date: string, category: int, comment: ?string, grateful_for: ?string, tags: string[]}>
     */
    private function validateDays(array $rows, array &$errors): array
    {
        $days = [];
        $seen = [];

        foreach ($rows as $index => $row) {
            $data = [
                'date' => $this->date($row['date'] ?? null),
                'category' => $this->text($row['category'] ?? null),
                'comment' => $this->text($row['comment'] ?? null),
                'grateful_for' => $this->text($row['grateful_for'] ?? null),
                'tags' => $this->tagNames($row['tags'] ?? null),
            ];

            if ($data['date'] === null && $data['category'] === null && $data['comment'] === null && $data['grateful_for'] === null && ! $data['tags']) {
                continue; // blank row
            }

            $validator = Validator::make($data, [
                'date' => ['required', 'date_format:Y-m-d'],
                'category' => ['required', 'integer', 'between:' . Category::GREAT . ',' . Category::WORST],
                'comment' => ['nullable', 'string', 'max:' . Day::COMMENT_MAX],
                'grateful_for' => ['nullable', 'string', 'max:' . Day::GRATEFUL_FOR_MAX],
                'tags' => ['array', 'max:' . self::MAX_TAGS_PER_DAY],
                'tags.*' => ['string', 'max:255'],
            ], [
                'category.between' => 'The category must be a mood from 1 (great) to 5 (worst).',
            ]);

            if ($validator->fails()) {
                $this->collect($errors, 'Days', $index, $validator->errors()->all());

                continue;
            }

            if (isset($seen[$data['date']])) {
                $this->collect($errors, 'Days', $index, ['The date ' . $data['date'] . ' appears more than once.']);

                continue;
            }

            $seen[$data['date']] = true;
            $data['category'] = (int) $data['category'];
            $days[] = $data;
        }

        return $days;
    }

    /**
     * @return array{days: int, skipped: int, tags: int}
     */
    private function write(User $user, array $days, array $tags): array
    {
        $categoryIds = Category::query()->pluck('id', 'order');

        // Tags: reuse the user's own tags by name, create the rest. Created one
        // by one so the model assigns slugs.
        $tagIds = $user->tags()->get()->mapWithKeys(fn(Tag $tag) => [mb_strtolower($tag->name) => $tag->id])->all();

        $createdTags = 0;
        foreach ($tags as $key => $tag) {
            if ( ! isset($tagIds[$key])) {
                $tagIds[$key] = $user->tags()->create($tag)->id;
                $createdTags++;
            }
        }

        // Days: only dates the user doesn't have yet.
        $existing = $user->days()->pluck('date')->flip();
        $new = array_values(array_filter($days, fn($day) => ! $existing->has($day['date'])));

        $now = now();
        foreach (array_chunk($new, 500) as $chunk) {
            DB::table('days')->insert(array_map(fn($day) => [
                'user_id' => $user->id,
                'category_id' => $categoryIds[$day['category']],
                'date' => $day['date'],
                'comment' => $day['comment'],
                'grateful_for' => $day['grateful_for'],
                'created_at' => $now,
                'updated_at' => $now,
            ], $chunk));
        }

        $dayIds = [];
        foreach (array_chunk(array_column($new, 'date'), 500) as $dates) {
            $dayIds += $user->days()->whereIn('date', $dates)->pluck('id', 'date')->all();
        }

        $links = [];
        foreach ($new as $day) {
            foreach (array_unique(array_map('mb_strtolower', $day['tags'])) as $key) {
                $links[] = ['day_id' => $dayIds[$day['date']], 'tag_id' => $tagIds[$key]];
            }
        }
        foreach (array_chunk($links, 1000) as $chunk) {
            DB::table('day_tag')->insert($chunk);
        }

        return ['days' => count($new), 'skipped' => count($days) - count($new), 'tags' => $createdTags];
    }

    /**
     * A cell as trimmed text, or null when empty. Numbers become text too, in
     * case the file was edited in a spreadsheet program.
     */
    private function text(mixed $value): ?string
    {
        if ($value === null || is_array($value) || is_object($value)) {
            return null;
        }

        $text = trim(str_replace("\r\n", "\n", (string) $value));

        return $text === '' ? null : $text;
    }

    private function date(mixed $value): ?string
    {
        // A spreadsheet program may have turned the text into a real date cell.
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return $this->text($value);
    }

    /**
     * @return string[]
     */
    private function tagNames(mixed $value): array
    {
        $text = $this->text($value);

        if ($text === null) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $text)), fn($name) => $name !== ''));
    }

    private function collect(array &$errors, string $sheet, int $index, array $messages): void
    {
        // +2: the header is row 1, and $index starts at 0.
        foreach ($messages as $message) {
            $errors[] = $sheet . ' row ' . ($index + 2) . ': ' . $message;
        }
    }

    /**
     * @throws ValidationException
     */
    private function fail(array $errors): never
    {
        $shown = array_slice($errors, 0, self::MAX_ERRORS_SHOWN);

        if (count($errors) > self::MAX_ERRORS_SHOWN) {
            $shown[] = 'And ' . (count($errors) - self::MAX_ERRORS_SHOWN) . ' more problems. Nothing was imported.';
        } else {
            $shown[] = 'Nothing was imported.';
        }

        throw ValidationException::withMessages(['file' => $shown])->errorBag('import');
    }
}
