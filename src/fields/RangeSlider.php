<?php
/**
 * MX Range Slider plugin for Craft CMS 3.x.
 *
 * Is an easy, flexible and responsive range slider for Craft CMS with tons of options.
 *
 * @see      https://www.wiseupstudio.com
 *
 * @copyright Copyright (c) 2019 Max Lazar
 */

namespace mx\mxrangeslider\fields;

use mx\mxrangeslider\assetbundles\rangesliderfield\RangeSliderFieldAsset;
use mx\mxrangeslider\helpers\Config as ConfigLoader;
use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use yii\db\Schema;
use craft\helpers\Json as JsonHelper;

/**
 * RangeSlider Field.
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for plugins to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
 *
 * @author    Max Lazar
 *
 * @since     3.1.0
 */
class RangeSlider extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * Some attribute.
     *
     * @var string
     */
    public $someAttribute = 'Some Default';
    public $options;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string the display name of this class
     */
    public static function displayName(): string
    {
        return Craft::t('mx-range-slider', 'Range Slider');
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
        ]);

        return $rules;
    }

    /**
     * Returns the column type that this field should get within the content table.
     *
     * This method will only be called if [[hasContentColumn()]] returns true.
     *
     * @return string The column type. [[\yii\db\QueryBuilder::getColumnType()]] will be called
     *                to convert the give column type to the physical one. For example, `string` will be converted
     *                as `varchar(255)` and `string(100)` becomes `varchar(100)`. `not null` will automatically be
     *                appended as well.
     *
     * @see \yii\db\QueryBuilder::getColumnType()
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * Normalizes the field’s value for use.
     *
     * This method is called when the field’s value is first accessed from the element. For example, the first time
     * `entry.myFieldHandle` is called from a template, or right before [[getInputHtml()]] is called. Whatever
     * this method returns is what `entry.myFieldHandle` will likewise return, and what [[getInputHtml()]]’s and
     * [[serializeValue()]]’s $value arguments will be set to.
     *
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        $data = array();

        if (is_string($value)) {
            $value = JsonHelper::decodeIfJson($value);
        }

        if (isset($value['from'])) {
            return $value;
        }

        $minmax = explode(';', $value);

        $minmax[1] = (!empty($value) && count($minmax) > 1) ? $minmax[1] : $minmax[0];
        $data['from'] = $minmax[0];
        $data['to'] = $minmax[1];
        $data['value'] = $minmax[0];

        return $data;
    }

    /**
     * Returns the component’s settings HTML.
     *
     * @return string|null
     */
    public function getSettingsHtml()
    {
        $FieldsConfig = ConfigLoader::getConfigFromFile('RangeSliderField');

        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'mx-range-slider/_components/fields/RangeSlider_settings',
            [
                'field' => $this,
                'FieldsConfig' => $FieldsConfig,
            ]
        );
    }

    /**
     * Returns the field’s input HTML.
     *
     * @param mixed                 $value   The field’s value. This will either be the [[normalizeValue() normalized value]],
     *                                       raw POST data (i.e. if there was a validation error), or null
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return string the input HTML
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $variables = [];

        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(RangeSliderFieldAsset::class);

        // Basic variables
        $variables['name'] = $this->handle;
        $variables['value'] = $value['from'].(('double' == $this->options['type']) ? ';'.$value['to'] : '');
        $variables['field'] = $this;

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $nameSpacedId = Craft::$app->getView()->namespaceInputId($id);
        $variables['id'] = $id;
        $variables['nameSpacedId'] = $nameSpacedId;

        $FieldsConfig = ConfigLoader::getConfigFromFile('RangeSliderField');

        Craft::$app->getView()->registerJs("var {$id}_cnf = {".$this->buildOptions($FieldsConfig)."};
        setTimeout(function() { $('#".$nameSpacedId."').ionRangeSlider( {$id}_cnf )}, 150);");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'mx-range-slider/_components/fields/RangeSlider_input',
            $variables
        );
    }

    public function buildOptions($data)
    {
        $js = '';

        foreach ($data  as $key => $value) {
            if (!isset($this->options[$key]) || $value['defaults'] == $this->options[$key]) {
                continue;
            }
            if ('string' == $value['type'] || 'number' == $value['type'] || 'select' == $value['type']) {
                $js .= $key.': "'.$this->options[$key].'",'."\n";
            }
            if ('boolean' == $value['type']) {
                $js .= $key.': '.('' == $this->options[$key] ? 'false' : 'true').', '."\n";
            }
            if ('function' == $value['type']) {
                $js .= $key.': function(data){'."\n".$this->options[$key]."\n".'},'."\n";
            }
        }

        return $js;
    }
}
