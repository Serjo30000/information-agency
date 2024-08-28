<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePostStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-post-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        DB::table('videos')
            ->join('statuses', 'videos.status_id', '=', 'statuses.id')
            ->where('statuses.status', 'Ожидает публикации')
            ->where('videos.publication_date', '<=', $now)
            ->update(['videos.status_id' => $this->getPublishedStatusId()]);

        DB::table('news')
            ->join('statuses', 'news.status_id', '=', 'statuses.id')
            ->where('statuses.status', 'Ожидает публикации')
            ->where('news.publication_date', '<=', $now)
            ->update(['news.status_id' => $this->getPublishedStatusId()]);

        DB::table('people_contents')
            ->join('statuses', 'people_contents.status_id', '=', 'statuses.id')
            ->where('people_contents.type','Interview')
            ->where('statuses.status', 'Ожидает публикации')
            ->where('people_contents.publication_date', '<=', $now)
            ->update(['people_contents.status_id' => $this->getPublishedStatusId()]);

        DB::table('people_contents')
            ->join('statuses', 'people_contents.status_id', '=', 'statuses.id')
            ->where('people_contents.type','Opinion')
            ->where('statuses.status', 'Ожидает публикации')
            ->where('people_contents.publication_date', '<=', $now)
            ->update(['people_contents.status_id' => $this->getPublishedStatusId()]);

        $this->info('Статусы обновлены.');
    }

    /**
     * Получить ID статуса "Опубликовано".
     *
     * @return int
     */
    protected function getPublishedStatusId()
    {
        return DB::table('statuses')
            ->where('status', 'Опубликовано')
            ->value('id');
    }
}
