<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlgoliaService;

class IndexAlgolia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'algolia:index {--clear : Clear the index before indexing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index all data in Algolia search';

    protected $algolia;

    public function __construct(AlgoliaService $algolia)
    {
        parent::__construct();
        $this->algolia = $algolia;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Algolia indexing...');

        if ($this->option('clear')) {
            $this->warn('Clearing existing index...');
            $this->algolia->clearIndex();
        }

        $this->info('Indexing owners...');
        $this->algolia->indexOwners();

        $this->info('Indexing actions...');
        $this->algolia->indexActions();

        $this->info('✅ Algolia indexing completed successfully!');

        return Command::SUCCESS;
    }
}
