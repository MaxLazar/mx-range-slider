<?php
/**
 * MX Range Slider plugin for Craft CMS 3.x.
 *
 * @see      https://www.wiseupstudio.com
 *
 * @copyright Copyright (c) 2019 MaxLazar
 */

namespace maxlazar\mxrangeslider\helpers;

use Craft;
use craft\helpers\StringHelper;

/**
 * @author    Max Lazar
 *
 * @since     3.1.0
 */
class Config
{
    // Constants
    // =========================================================================
    const LOCAL_CONFIG_DIR = 'settings';

    // Static Methods
    // =========================================================================

    /**
     * Loads a config file from, trying @craft/config first, then the plugin's.
     *
     *
     * @param string $filePath
     * @param null   $alias
     *
     * @return array
     */
    public static function getConfigFromFile(string $filePath, $alias = null): array
    {
        // Try craft/config first
        $path = self::getConfigFilePath('@config', $filePath);
        if (!file_exists($path)) {
            // Now try our own internal config
            $path = self::getConfigFilePath('@maxlazar/mxrangeslider', $filePath);
            if (!file_exists($path)) {
                if (!$alias) {
                    return [];
                }
                // Now the additional alias config
                $path = self::getConfigFilePath($alias, $filePath);
                if (!file_exists($path)) {
                    return [];
                }
            }
        }

        if (!\is_array($config = @include $path)) {
            return [];
        }

        // If it's not a multi-environment config, return the whole thing
        if (!array_key_exists('*', $config)) {
            return $config;
        }

    }

    /**
     * Load an array of config files, merging them together in a single array.
     *
     * @param array $filePaths
     *
     * @return array
     */
    public static function getMergedConfigFromFiles(array $filePaths): array
    {
        $mergedConfig = [];
        foreach ($filePaths as $filePath) {
            $mergedConfig = ArrayHelper::merge($mergedConfig, self::getConfigFromFile($filePath));
        }

        return $mergedConfig;
    }

    // Private Methods
    // =========================================================================

    /**
     * Return a path from an alias and a partial path.
     *
     * @param string $alias
     * @param string $filePath
     *
     * @return string
     */
    private static function getConfigFilePath(string $alias, string $filePath): string
    {
        $path = DIRECTORY_SEPARATOR.ltrim($filePath, DIRECTORY_SEPARATOR);
        $path = Craft::getAlias($alias)
            .DIRECTORY_SEPARATOR
            .self::LOCAL_CONFIG_DIR
            .str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path)
            .'.php';

        return $path;
    }
}
