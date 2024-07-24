<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TrashFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trashFiles:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Trashed Files Every X Month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $files = Storage::disk('trashed')->allFiles();

        // foreach ($files as $file) {
        //     Storage::disk('trashed')->delete($file);
        // }

        // $this->info('All trashed files have been deleted.');


        $trashedMediaItems = Media::where('disk', 'trashed')->get();

        foreach ($trashedMediaItems as $mediaItem) {
            $mediaItem->delete();
        }

        $this->info('All trashed media files have been deleted.');
    }
}
