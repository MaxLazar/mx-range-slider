<?php
/**
 * MX Range Slider plugin for Craft CMS 3.x
 *
 * Is an easy, flexible and responsive range slider for Craft CMS with tons of options.
 *
 * @link      https://www.wiseupstudio.com
 * @copyright Copyright (c) 2019 Max Lazar
 */

namespace maxlazar\mxrangeslider\variables;

use maxlazar\mxrangeslider\MxRangeSlider;

use Craft;

/**
 * MX Range Slider Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.mxRangeSlider }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Max Lazar
 * @package   MxRangeSlider
 * @since     3.1.0
 */
class MxRangeSliderVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.mxRangeSlider.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.mxRangeSlider.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }
}
