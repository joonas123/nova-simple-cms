<?php

namespace Joonas1234\NovaSimpleCms\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreatePage extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cms:page {classname? : The page\'s classname}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new blueprint and template';

    /**
     * The layout's classname
     *
     * @var string
     */
    protected $classname;

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
     * @return void
     */
    public function handle()
    {
        $this->classname = $this->getClassnameArgument();

        $this->call('Cms:blueprint', [
            'classname' => $this->classname
        ]);
        $this->call('Cms:template', [
            'classname' => Str::kebab($this->classname)
        ]);
    }

    /**
     * Get the classname
     *
     * @return string
     */
    public function getClassnameArgument()
    {
        if(!$this->argument('classname')) {
            return $this->ask('Please provide a class name for your page');
        }

        return $this->argument('classname');
    }


}
