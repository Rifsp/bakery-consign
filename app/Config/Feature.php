<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Enable/disable backward compatibility breaking features.
 */
class Feature extends BaseConfig
{
    /**
     * Use improved new auto routing instead of the default legacy version.
     */
    public bool $autoRoutesImproved = false;

    /**
     * Use the old filter execution order.
     *
     * If you enable this:
     *   - Before filters are executed in route => globals => methods => filters order.
     *   - After filters are executed in route => globals => filters order.
     */
    public bool $oldFilterOrder = false;

    /**
     * Whether to limit the number of rows to 0 or to return all rows.
     *
     * If you enable this, limit(0) will return all rows.
     */
    public bool $limitZeroAsAll = false;
}
