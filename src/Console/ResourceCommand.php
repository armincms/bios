<?php

namespace Armincms\Bios\Console; 

use Laravel\Nova\Console\ResourceCommand as GeneratorCommand;   
use Symfony\Component\Console\Input\InputOption; 

class ResourceCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bios:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new bios resource class';  

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {   
        return str_replace(
            'DummyStore', $this->option('store'), parent::buildClass($name)
        ); 
    }  

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/resource.stub';
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [ 
            ['store', 's', InputOption::VALUE_REQUIRED, 'The option storage driver name.'],
        ]);
    }
}
