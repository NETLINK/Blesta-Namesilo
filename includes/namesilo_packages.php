<?php
/**
 * Namesilo Packages Management
 *
 * @copyright Copyright (c) 2020, Phillips Data, Inc.
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @package   blesta.components.modules.namesilo.packages
 */
class NamesiloPackages extends Namesilo
{
    /**
     * @var array All POST data
     */
    public $post;

    /**
     * Initializes the class
     */
    public function __construct()
    {
        parent::__construct();

        set_time_limit(60 * 60 * 15); // 15 minutes

        // Load the required models
        Loader::loadModels($this, ['ModuleManager', 'Packages']);

        // Set the post fields
        $this->post = $_POST;
    }

    public function process($vars)
    {
        Loader::loadModels($this, ['Languages']);

        $vars = array_merge($vars, $this->post);
        unset($vars['_csrf_token']);

        // Fetch packages map
        $module_row_id = isset($vars['module_row']) ? $vars['module_row'] : 0;
        $packages_map = $this->getPackagesMap($module_row_id);

        // Process packages
        foreach ($vars['pricing'] as $tld => $pricing) {
            if (isset($pricing['tld']) && (bool)$pricing['tld']) {
                // Get price rows for the current package
                $price_rows = $this->getPriceRows($pricing);

                // Get module row
                $module_row = $this->ModuleManager->getRow($module_row_id);

                // Build parameters
                $tld = '.' . trim($tld, '.');
                $params = [
                    'names' => $this->parseNameTags($vars['names'], $tld),
                    'descriptions' => $this->parseDescriptionTags($vars['descriptions'], $tld),
                    'status' => 'active',
                    'qty_unlimited' => 'true',
                    'upgrades_use_renewal' => isset($vars['upgrades_use_renewal']) ? $vars['upgrades_use_renewal'] : 0,
                    'module_id' => $module_row->module_id,
                    'module_row' => $module_row_id,
                    'pricing' => $price_rows,
                    'taxable' => isset($vars['taxable']) ? $vars['taxable'] : 0,
                    'meta' => [
                        'type' => 'domain',
                        'tlds' => [$tld]
                    ],
                    'select_group_type' => 'existing',
                    'groups' => [
                        isset($vars['package_group']) ? $vars['package_group'] : 0
                    ],
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
                $packages_map[$tld] = $this->savePackage($params, isset($packages_map[$tld]) ? $packages_map[$tld] : null);

                if (!empty($this->Packages->Input->errors())) {
                    return $this->Packages->Input->errors();
                }
            } else {
                unset($vars['pricing'][$tld]);
            }
        }

        $this->savePackagesMap($packages_map, $module_row_id);
        $this->savePackagesVars($vars, $module_row_id);

        return true;
    }

    public function getPackagesMap($module_row_id)
    {
        $query = $this->Record->select('value')
            ->from('module_row_meta')
            ->where('module_row_meta.module_row_id', '=', $module_row_id)
            ->where('module_row_meta.key', '=', 'packages_map')
            ->fetch();

        if (!empty($query->value)) {
            return json_decode($query->value, true);
        }

        return [];
    }

    public function savePackagesMap($packages_map, $module_row_id)
    {
        $query = $this->Record->select()
            ->from('module_row_meta')
            ->where('module_row_meta.module_row_id', '=', $module_row_id)
            ->where('module_row_meta.key', '=', 'packages_map')
            ->numResults();

        $fields = [
            'module_row_id' => $module_row_id,
            'key' => 'packages_map',
            'value' => json_encode($packages_map)
        ];

        if ($query) {
            return $this->Record->where('module_row_meta.module_row_id', '=', $module_row_id)
                ->where('module_row_meta.key', '=', 'packages_map')
                ->update('module_row_meta', $fields);
        }

        return $this->Record->insert('module_row_meta', $fields);
    }

    public function getPackagesVars($module_row_id)
    {
        $query = $this->Record->select('value')
            ->from('module_row_meta')
            ->where('module_row_meta.module_row_id', '=', $module_row_id)
            ->where('module_row_meta.key', '=', 'packages_vars')
            ->fetch();

        if (!empty($query->value)) {
            return json_decode($query->value, true);
        }

        return [];
    }

    public function savePackagesVars($vars, $module_row_id)
    {
        $query = $this->Record->select()
            ->from('module_row_meta')
            ->where('module_row_meta.module_row_id', '=', $module_row_id)
            ->where('module_row_meta.key', '=', 'packages_vars')
            ->numResults();

        // Get TLD prices
        $prices = $this->getPrices();

        foreach ($vars['pricing'] as $tld => $pricing) {
            foreach ($pricing as $currency => $price_row) {
                if ($currency !== 'tld') {
                    $vars['pricing'][$tld][$currency]['previous_registration_price'] = $prices[$tld][$currency]->registration;
                    $vars['pricing'][$tld][$currency]['previous_renewal_price'] = $prices[$tld][$currency]->renew;
                }
            }
        }

        $fields = [
            'module_row_id' => $module_row_id,
            'key' => 'packages_vars',
            'value' => json_encode($vars)
        ];


        if ($query) {
            return $this->Record->where('module_row_meta.module_row_id', '=', $module_row_id)
                ->where('module_row_meta.key', '=', 'packages_vars')
                ->update('module_row_meta', $fields);
        }

        return $this->Record->insert('module_row_meta', $fields);
    }

    private function savePackage($params, $package_id = null)
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

    private function getPriceRows($pricing)
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
            $descriptions[$key]['html'] = $parser->parseString($description['html'], $parser_options_html)->render($tags);
            $descriptions[$key]['text'] = $parser->parseString($description['text'], $parser_options_text)->render($tags);
        }

        return $descriptions;
    }
}
