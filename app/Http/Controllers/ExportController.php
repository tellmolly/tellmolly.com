<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Tag;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ExportController extends Controller
{
    public function __invoke(Request $request): void
    {
        $writer = SimpleExcelWriter::streamDownload('tellmolly.xlsx');
        $writer->nameCurrentSheet('Days');

        $request->user()->days()
            ->with('category', 'tags')
            ->get()
            ->each(function (Day $day) use ($writer) {
                $writer->addRow($day->toExport());
            });

        $writer->addNewSheetAndMakeItCurrent('Tags');
        $request->user()->tags()
            ->get()
            ->each(function (Tag $tag) use ($writer) {
                $writer->addRow($tag->toExport());
            });

        $writer->toBrowser();
    }
}
