<?php
/**
 * This file is part of Torii
 *
 * @version $Revision$
 */

namespace Torii\Struct;
use Torii\Struct;

/**
 * Base user settings struct
 *
 * @version $Revision$
 */
class UserSettings extends Struct
{
    /**
     * User name
     *
     * @var string
     */
    public $name;

    /**
     * Number of columns
     *
     * @var int
     */
    public $columns;

    /**
     * Installed modules
     *
     * @var array
     */
    public $modules = array();

    /**
     * Create from data array
     *
     * @param array $data
     * @return UserSettings
     */
    public static function create( $data )
    {
        $settings = new static();
        if ( is_array( $data ) )
        {
            foreach ( $data as $key => $value )
            {
                $settings->$key = $value;
            }
        }

        return $settings;
    }
}
