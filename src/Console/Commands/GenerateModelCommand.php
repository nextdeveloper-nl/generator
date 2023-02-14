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
use Illuminate\Support\Facades\DB;

/**
 * Class FetchExchangeRatesCommand.
 *
 * @package PlusClouds\Core\Console\Commands
 */
class GenerateModelCommand extends Command {
    /**
     * @var string
     */
    protected $signature = 'nextdeveloper:generate-model';

    /**
     * @var string
     */
    protected $description = 'NextDeveloper Model Generator. This command will generate requested models for you.';

    /**
     * @return int
     */
    public function handle() {
        $this->line('To create the initial account for LEO. Please make sure that you complete the followings;');

        $table = 'accounts'; //$this->ask('What is the name of the table from the database ? Example: countries');

        $columns = DB::select( DB::raw("SHOW COLUMNS FROM " . $table));

        $render = view('templates.model')->render();

        $this->info(print_r($render, true));

        return 1;
    }
}
