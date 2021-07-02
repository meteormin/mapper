<?php

namespace Miniyus\Mapper\Commands;

use JsonMapper_Exception;
use Miniyus\Mapper\Generate\MapGenerator;
use Illuminate\Console\Command;

class GenerateMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:map {name} {--json=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate map class';

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
     * @return int
     * @throws JsonMapper_Exception
     */
    public function handle(): int
    {
        $this->option('json');

        if ($this->option('json')) {
            $json = file_get_contents(config('make_class.json_path') . '/maps/' . $this->option('json'));
            if (is_null($json)) {
                $this->error('file not found...');
                return 1;
            }
            $generator = new MapGenerator($this->argument('name'), $json);
        } else {
            $generator = new MapGenerator($this->argument('name'));
            $json = $generator->getJson();
        }

        $this->info(json_encode($json, JSON_UNESCAPED_UNICODE || JSON_UNESCAPED_SLASHES || JSON_PRETTY_PRINT));

        if (!$this->confirm('Are you sure?')) {
            return 0;
        }

        if ($generator->generate()) {
            $this->info('Generate Success');
            return 0;
        }

        $this->error('Generate Fail');
        return 1;
    }
}
