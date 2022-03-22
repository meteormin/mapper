<?php

namespace Miniyus\Mapper\Consonle;

use JsonMapper_Exception;
use Miniyus\Mapper\Generate\MakeClass;
use Miniyus\Mapper\Generate\MapGenerator;
use Illuminate\Console\Command;
use Miniyus\Mapper\Generate\MapTemplate;
use ReflectionException;

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
     * @param string $namespace
     * @param string $name
     * @param object $data
     * @return MapGenerator
     * @throws JsonMapper_Exception
     */
    protected function makeGenerator(string $namespace, string $name, object $data): MapGenerator
    {
        return new MapGenerator(
            $namespace,
            $name,
            new MakeClass(
                base_path(config('make_class.stub_path', 'app/Stubs')),
                base_path(config('make_class.save_path', 'app/Maps'))
            ),
            new MapTemplate($data)
        );
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws JsonMapper_Exception
     * @throws ReflectionException
     */
    public function handle(): int
    {
        $this->option('json');

        $namespace = config('mapper.map_namespace');

        if ($this->option('json')) {
            $name = Str::of($this->option('json'))->basename('.json');
            $json = file_get_contents(config('make_class.json_path') . $this->option('json'));
            if (is_null($json)) {
                $this->error('file not found...');
                return 1;
            }

            $mapData = json_decode($json);
        } else {
            $name = \Str::studly($this->argument('name'));
            $mapClass = "$namespace\\$name";
            $mapData = config("mapper.maps.$mapClass");
            $mapData['map'] = null;
        }

        $generator = $this->makeGenerator($namespace, $name, (object)$mapData);

        $this->info(json_encode($mapData, JSON_UNESCAPED_UNICODE || JSON_UNESCAPED_SLASHES || JSON_PRETTY_PRINT));

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
