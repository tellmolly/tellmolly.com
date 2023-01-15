<?php

namespace App\Console\Commands;

use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AddSlugToTags extends Command
{
    protected $signature = 'tm:add-slug-to-tags';

    protected $description = 'Add slugs to existing tags without tags. Slugs were introduced after tags. ';

    public function handle(): int
    {
        $tagsWithoutSlug = Tag::query()
            ->whereNull('slug');

        $affected = $tagsWithoutSlug->count();

        if ($affected === 0) {
            $this->info('No tags without slug detected');

            return Command::SUCCESS;
        }

        $this->info('Generating ' . $affected . ' slugs');

        $tags = $tagsWithoutSlug->get();

        foreach($tags as $tag) {
            $count = 1;
            do {
                $tag->slug = Str::slug($tag->name) . ($count > 1 ? '-' . $count : '');

                $count++;
            } while (
                Tag::query()
                    ->where('user_id', '=', $tag->user_id)
                    ->where('slug', $tag->slug)
                    ->exists()
            );
            $tag->save();
        }

        $this->info('Generated ' . $affected . ' slugs');

        return Command::SUCCESS;
    }
}
