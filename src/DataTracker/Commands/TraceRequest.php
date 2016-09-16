<?php

namespace DataTracker\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Console\Helper\TableSeparator;

class TraceRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trace:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a debug trace of data flux that begins at route request';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $headers = ["Order", "Class::method", "Arguments"];
        $finished = false;
        $this->line('Waiting for the next request...');
        $time_init = microtime(true);
        while(!$finished) {
            if (\Cache::has('trace')) {
                $rows = \Cache::pull('trace');
                $separated_rows = [];
                foreach ($rows as $key => $row) {
                    $separated_rows[] = $row;
                    if ($key < count($rows) - 1) {
                        $separated_rows[] = new TableSeparator();
                    }
                }
                $this->table($headers, $separated_rows);
                //$this->info(dd(\Cache::pull('trace')));
                $finished = true;
            }
        }
        $time_end = microtime(true);
        $diff = round($time_end - $time_init, 3);
        $this->info('Finished after '. $diff . ' seconds.');
    }
}
