<?php
/**
 * Namesilo Packages Management
 *
 * @copyright Copyright (c) 2020, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package blesta.components.modules.namesilo.packages
 */
class NamesiloPackages extends Namesilo
{
    /**
     * Initializes the class
     */
    public function __construct()
    {
        parent::__construct();

        set_time_limit(60 * 15); // 15 minutes

        // Load the required models
        Loader::loadModels($this, ['ModuleManager', 'Packages']);
    }

    /**
     * Creates or updates the packages for each of the enabled TLDs
     *
     * @param array $vars An array containing the posted data
     * @return bool True if all the packages has been created/updated successfully
     */
    public function process(array $vars)
    {
        Loader::loadModels($this, ['Languages']);

        // Fetch packages map
        $module_row_id = isset($vars['module_row']) ? $vars['module_row'] : 0;
        $tld_packages_map = $this->getPackagesMap($module_row_id);

        // Process packages
        if (!empty($vars['pricing'])) {
            foreach ($vars['pricing'] as $tld => $pricing) {
                if (isset($pricing['tld']) && (bool)$pricing['tld']) {
                    // Get price rows for the current package
                    $price_rows = $this->generatePricingRows($pricing);

                    // Get module row
                    $module_row = $this->ModuleManager->getRow($module_row_id);

                    // Set unset checkboxes
                    $fields = ['upgrades_use_renewal', 'taxable', 'price_enable_renews_all'];

                    foreach ($fields as $field) {
                        if (!isset($vars[$field])) {
                            $vars[$field] = 0;
                        }
                    }

                    // Build parameters
                    $tld = '.' . trim($tld, '.');
                    $params = [
                        'names' => $this->parseNameTags($vars['names'], $tld),
                        'descriptions' => $this->parseDescriptionTags($vars['descriptions'], $tld),
                        'status' => 'active',
                        'qty_unlimited' => 'true',
                        'upgrades_use_renewal' => $vars['upgrades_use_renewal'],
                        'module_id' => $module_row->module_id,
                        'module_row' => $module_row_id,
                        'pricing' => $price_rows,
                        'taxable' => $vars['taxable'],
                        'meta' => [
                            'type' => 'domain',
                            'tlds' => [$tld]
                        ],
                        'select_group_type' => 'existing',
                        'groups' => [$vars['package_group']],
                        'company_id' => Configure::get('Blesta.company_id')
                    ];
                    $params['meta'] = array_merge($params['meta'], $vars['meta']);

                    $languages = $this->Languages->getAll(Configure::get('Blesta.company_id'));

                    foreach ($languages as $language) {
                        $params['email_content'][] = [
                            'lang' => $language->code,
                            'html' => null,
                            'text' => null
                        ];
                    }

                    // Add or update the package
                    $tld_packages_map[$tld] = $this->savePackage(
                        $params,
                        isset($tld_packages_map[$tld]) ? $tld_packages_map[$tld] : null
                    );

                    if (($errors = $this->Packages->Input->errors())) {
                        return false;
                    }
                } else {
                    unset($vars['pricing'][$tld]);
                }
            }

            $this->savePackagesMap($tld_packages_map, $module_row_id);
            $this->saveSettings($vars, $module_row_id);

            return true;
        }

        return false;
    }

    /**
     * Fetches the packages map for the enabled TLDs
     *
     * @param int $module_row_id The ID of the module row to fetch the packages map
     * @return array An array of key/value pairs where each key is the TLD and each value is the package id
     */
    public function getPackagesMap($module_row_id)
    {
        $tld_packages_map = $this->Record->select('value')
            ->from('module_row_meta')
            ->where('module_row_meta.module_row_id', '=', $module_row_id)
            ->where('module_row_meta.key', '=', 'tld_packages_map')
            ->fetch();

        return !empty($tld_packages_map->value) ? json_decode($tld_packages_map->value, true) : [];
    }

    /**
     * Saves the packages map for the enabled TLDs
     *
     * @param array $vars An array containing the posted data
     * @param int $module_row_id The ID of the module row to save the packages map
     * @return PDOStatement An instance of the PDOStatement
     */
    public function savePackagesMap(array $vars, $module_row_id)
    {
        $packages_map = $this->getPackagesMap($module_row_id);
        $vars = array_merge($packages_map, $vars);

        $fields = [
            'module_row_id' => $module_row_id,
            'key' => 'tld_packages_map',
            'value' => json_encode($vars)
        ];

        return $this->Record->duplicate('module_row_meta.value', '=', $fields['value'])
            ->insert('module_row_meta', $fields);
    }

    /**
     * Fetches the packages settings
     *
     * @param $module_row_id The ID of the module row to fetch the packages settings
     * @return array An array containing all the packages settings
     */
    public function getSettings($module_row_id)
    {
        $settings = $this->Record->select('value')
            ->from('module_row_meta')
            ->where('module_row_meta.module_row_id', '=', $module_row_id)
            ->where('module_row_meta.key', '=', 'tld_packages_settings')
            ->fetch();
        $settings = !empty($settings->value) ? json_decode($settings->value, true) : [];

        // Fetch tld pricing
        $tld_pricing = $this->Record->select('value')
            ->from('module_row_meta')
            ->where('module_row_meta.module_row_id', '=', $module_row_id)
            ->where('module_row_meta.key', 'LIKE', 'tld_%_pricing')
            ->fetchAll();

        if (!empty($settings)) {
            $settings['pricing'] = [];
            foreach ($tld_pricing as $pricing) {
                $settings['pricing'] = array_merge($settings['pricing'], json_decode($pricing->value, true));
            }
        }

        return $settings;
    }

    /**
     * Saves the packages settings
     *
     * @param array $vars An array containing the posted data
     * @param int $module_row_id The ID of the module row to save the packages map
     * @return PDOStatement An instance of the PDOStatement
     */
    public function saveSettings(array $vars, $module_row_id)
    {
        // Save TLD pricing rows
        $prices = $this->getPrices();

        foreach ($vars['pricing'] as $tld => $pricing) {
            $tld_pricing = [];

            foreach ($pricing as $currency => $price_row) {
                if ($currency !== 'tld') {
                    // Set unset checkboxes
                    if (!isset($vars['pricing'][$tld][$currency]['price_enable_renews'])) {
                        $vars['pricing'][$tld][$currency]['price_enable_renews'] = 0;
                    }

                    $tld_pricing[$tld] = $vars['pricing'][$tld];
                    $tld_pricing[$tld][$currency]['previous_registration_price'] = $prices[$tld][$currency]->registration;
                    $tld_pricing[$tld][$currency]['previous_renewal_price'] = $prices[$tld][$currency]->renew;
                }
            }

            $fields = [
                'module_row_id' => $module_row_id,
                'key' => 'tld_' . trim($tld, '.') . '_pricing',
                'value' => json_encode($tld_pricing)
            ];

            $this->Record->duplicate('module_row_meta.value', '=', $fields['value'])
                ->insert('module_row_meta', $fields);
        }
        unset($vars['pricing']);

        // Save packages settings
        $fields = [
            'module_row_id' => $module_row_id,
            'key' => 'tld_packages_settings',
            'value' => json_encode($vars)
        ];

        return $this->Record->duplicate('module_row_meta.value', '=', $fields['value'])
            ->insert('module_row_meta', $fields);
    }

    /**
     * Creates a new package, if a package already exists it will be updated
     *
     * @param array $params An array of package information including:
     *  - names A list of names for the package in different languages
     *  - descriptions A list of descriptions in text and html for the
     *      package in different languages (optional, default NULL)
     *      - lang The language in ISO 636-1 2-char + "_" + ISO 3166-1 2-char (e.g. en_us)
     *      - text The text description in the specified language
     *      - html The HTML description in the specified language
     *  - status The status of this package, 'active', 'inactive', 'restricted' (optional, default 'active')
     *  - module_id The ID of the module this package belongs to (optional, default NULL)
     *  - module_row The module row this package belongs to (optional, default 0)
     *  - pricing A numerically indexed array of pricing info including:
     *      - term The term as an integer 1-65535 (period should be given if this is set; optional, default 1)
     *      - period The period, 'day', 'week', 'month', 'year', 'onetime' (optional, default 'month')
     *      - price The price of this term (optional, default 0.00)
     *      - setup_fee The setup fee for this package (optional, default 0.00)
     *      - cancel_fee The cancellation fee for this package (optional, default 0.00)
     *      - currency The ISO 4217 currency code for this pricing
     *  - taxable Whether or not this package is taxable (optional, default 0)
     *  - groups A numerically indexed array of package group assignments (optional)
     *  - company_id The ID of the company this package belongs to
     *  - * A set of miscellaneous fields to pass, in addition to the above
     *      fields, to the module when adding the package (optional)
     * @param int $package_id The package id to update, by default null to create a new one instead
     * @return mixed The package id if the package has been successfully saved, null otherwise
     */
    private function savePackage(array $params, $package_id = null)
    {
        if (is_null($package_id)) {
            // Add new package
            $package_id = $this->Packages->add($params);
        } else {
            // Update existing package
            $pricing_ids = [];
            $package = $this->Packages->get($package_id);

            foreach ($package->pricing as $pricing) {
                $pricing_ids[$pricing->currency] = $pricing->id;
            }
            unset($pricing);

            foreach ($params['pricing'] as $key => $pricing) {
                $params['pricing'][$key]['id'] = isset($pricing_ids[$pricing['currency']]) ? $pricing_ids[$pricing['currency']] : null;
            }
            unset($pricing);

            $params['email_content'] = json_decode(json_encode($package->email_content), true);

            $this->Packages->edit($package_id, $params);
        }

        return $package_id;
    }

    /**
     * Generates the pricing rows for a specific TLD package
     *
     * @param array $pricing An array of key/value pairs where each key is the currency
     *  and each value is an array, containing:
     *  - price The registration price for the domain
     *  - price_enable_renews Whether or not the renewal price is enabled
     *  - price_renews The renewal price for the domain
     * @return array An numerically-indexed array containing the package pricing rows
     */
    private function generatePricingRows(array $pricing)
    {
        $package_pricing = [];

        foreach ($pricing as $currency => $price) {
            if ($currency !== 'tld') {
                $price_row = [
                    'term' => 1,
                    'period' => 'year',
                    'price' => $price['price'],
                    'currency' => $currency,
                    'price_enable_renews' => isset($price['price_enable_renews']) ? $price['price_enable_renews'] : 0,
                    'price_renews' => isset($price['price_enable_renews']) ? $price['price_renews'] : $price['price'],
                    'setup_fee' => 0,
                    'cancel_fee' => 0
                ];

                $package_pricing[] = $price_row;
            }
        }

        return $package_pricing;
    }

    /**
     * Parse the name tags, replacing them with the given TLD
     *
     * @param array $names A numerically-indexed array of the descriptions to parse, containing:
     *  - html The HTML version of the email content
     *  - text The text version of the email content
     * @param string $tld The TLD to be used on the tags
     * @return array A numerically-indexed array containing the parsed names
     */
    private function parseNameTags(array $names, $tld)
    {
        // Load the template parser
        $parser = new H2o();

        // Don't escape text
        $parser_options_text = Configure::get('Blesta.parser_options');
        $parser_options_text['autoescape'] = false;

        // Parse package name using template parser
        $tags = [
            'domain' => [
                'tld' => strtolower($tld),
                'tld_uppercase' => strtoupper($tld)
            ]
        ];

        foreach ($names as $key => $name) {
            $names[$key]['name'] = $parser->parseString($name['name'], $parser_options_text)->render($tags);
        }

        return $names;
    }

    /**
     * Parse the description tags, replacing them with the given TLD
     *
     * @param array $descriptions A numerically-indexed array of the descriptions to parse, containing:
     *  - html The HTML version of the email content
     *  - text The text version of the email content
     * @param string $tld The TLD to be used on the tags
     * @return array A numerically-indexed array containing the parsed descriptions
     */
    private function parseDescriptionTags(array $descriptions, $tld)
    {
        // Load the template parser
        $parser = new H2o();

        // Don't escape html
        $parser_options_html = Configure::get('Blesta.parser_options');
        $parser_options_html['autoescape'] = false;

        // Don't escape text
        $parser_options_text = Configure::get('Blesta.parser_options');
        $parser_options_text['autoescape'] = false;

        // Parse package name using template parser
        $tags = [
            'domain' => [
                'tld' => strtolower($tld),
                'tld_uppercase' => strtoupper($tld)
            ]
        ];

        foreach ($descriptions as $key => $description) {
            $descriptions[$key]['html'] = $parser->parseString(
                $description['html'],
                $parser_options_html
            )->render($tags);
            $descriptions[$key]['text'] = $parser->parseString(
                $description['text'],
                $parser_options_text
            )->render($tags);
        }

        return $descriptions;
    }

    /**
     * Return all errors
     *
     * @return mixed An array of error messages indexed as their field name, boolean false if no errors set
     */
    public function errors()
    {
        return $this->Packages->Input->errors();
    }
}
