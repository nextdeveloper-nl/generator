<?php
/**
 * This file is part of the PlusClouds.Account library.
 *
 * (c) Semih Turna <semih.turna@plusclouds.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{ $namespace }}\{{ $module }}\Database\Filters;

class {{ $model }}QueryFilter
{
    /**
     * Askıya alınmış hesaplar.
     *
     * @return mixed
     */
    public function name($name)
    {
        return $this->builder->where('name', 'like', '%'.$name.'%');
    }
}
