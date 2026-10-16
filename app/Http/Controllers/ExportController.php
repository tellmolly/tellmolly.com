<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ExportController extends Controller
{
    public function __invoke(Request $request): void
    {
        $writer = SimpleExcelWriter::streamDownload('tellmolly.xlsx');

        self::write($writer, $request->user());

        $writer->toBrowser();
    }

    /**
     * The export's sheets; ImportService reads this same layout back.
     */
    public static function write(SimpleExcelWriter $writer, User $user): void
    {
        $writer->nameCurrentSheet('Days');

        $user->days()
            ->with('category', 'tags')
            ->get()
            ->each(function (Day $day) use ($writer) {
                $writer->addRow($day->toExport());
            });

        $writer->addNewSheetAndMakeItCurrent('Tags');
        $user->tags()
            ->get()
            ->each(function (Tag $tag) use ($writer) {
                $writer->addRow($tag->toExport());
            });
    }
}
