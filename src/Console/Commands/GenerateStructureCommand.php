<?php
/**
 * This file is part of the PlusClouds.Core library.
 *
 * (c) Semih Turna <semih.turna@plusclouds.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NextDeveloper\Generator\Console\Commands;

use Illuminate\Console\Command;
use NextDeveloper\Generator\Services\Structure\StructureService;

/**
 * Class FetchExchangeRatesCommand.
 *
 * @package  NextDeveloper\Commons\Console\Commands
 */
class GenerateStructureCommand extends Command {
    /**
     * @var string
     */
    protected $signature = 'nextdeveloper:generate-structure';

    /**
     * @var string
     */
    protected $description = 'NextDeveloper Structure Generator. This command will generate requested models for you.';

    /**
     * @return int
     */
    public function handle() {
        $this->line('This command will create the initial structure for a basic module. Items below will be created automatically;');
        $this->line('- Directory structure');
        $this->line('- Basic composer.json file');
        $this->line('- Service provider files');
        $this->line('- Empty API route files');
        $this->line('- Empty configuration files');

        $this->line('WARNING: Please make sure that all permissions are given in the root folder. For example if you want to
        create a directory in a library and the module of the root directory is not created. You may need to create depending on which
        user you are using to run this application.');

        $namespace  =   $this->ask('Please write the namespace you are using for this module. Example: NextDeveloper');
        $moduleName = $this->ask('Please tell me the module name in this fashion: Generator ....');

        $libraryFolder = $this->ask('Please tell me the path that you store your library in. Please make sure that you 
        provide the path in referance to the base path of this application. For example: ../../Libraries');

        $libraryFolder = $libraryFolder . '/' . $moduleName;

        $result = StructureService::generateStructure($moduleName, $libraryFolder);

        return 1;
    }
}
