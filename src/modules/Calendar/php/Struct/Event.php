<?php
/**
 * This file is part of Torii
 *
 * @version $Revision$
 */

namespace Torii\Module\Calendar\Struct;

use Torii\Struct;

/**
 * Calendar event struct
 *
 * @version $Revision$
 */
class Event extends Struct
{
    /**
     * Event title
     *
     * @var string
     */
    public $summary;

    /**
     * Event date
     *
     * @var \DateTime
     */
    public $date;

    /**
     * Event location
     *
     * @var string
     */
    public $location;

    /**
     * Name of calendar
     *
     * @var string
     */
    public $calendar;
}
