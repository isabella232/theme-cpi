<?php

namespace CPI\Services;

use WP_Query;
use WP_Post;
use WP_Term;

use Timber\PostQuery;

use CPI\Models\CPIPost;
use CPI\Services\WordPressService;

class Redirects
{
    const LEGACY_TOPIC_URIS = array(
        '/politics',
        '/national-security',
        '/business',
        '/immigration',
        '/environment',
        '/accountability',
        '/health',
        '/inside-publici'
    );

    const LEGACY_NESTED_TOPIC_URIS = array(
        "/accountability-education",
        "/after-the-meltdown",
        "/alex-finley-analysis",
        "/betting-on-justice",
        "/big-oil-bad-air",
        "/blue-dogs",
        "/breathless-and-burdened",
        "/broadband",
        "/broken-government",
        "/buying-of-the-president",
        "/buying-of-the-president-2000",
        "/buying-of-the-president-2004",
        "/buying-the-senate-2014",
        "/carbon-wars",
        "/climate-change-lobby",
        "/coal-ash",
        "/cracking-the-codes",
        "/criminalizing-kids",
        "/danger-in-the-air",
        "/dangers-in-the-dust",
        "/debt-deception",
        "/democracy-ink",
        "/divine-intervention",
        "/dollars-and-dentists",
        "/education",
        "/environmental-justice-denied",
        "/exposed-decades-of-denial-on-poisons-environment",
        "/finance",
        "/fueling-fears",
        "/gun-wars",
        "/immigration-decoded",
        "/inside-publici",
        "/iraq-the-war-card",
        "/justice-obscured",
        "/juvenile-justice",
        "/local-voters-distant-donors-state-politics",
        "/looting-the-seas",
        "/looting-the-seas-ii",
        "/looting-the-seas-iii",
        "/manipulating-medicare",
        "/medicaid-under-the-influence-state-politics",
        "/medicare-advantage-money-grab",
        "/military-children-left-behind",
        "/military-children-left-behind-education",
        "/model-workplaces",
        "/murtha-method",
        "/mystery-in-the-fields",
        "/one-nation-under-debt",
        "/party-lines",
        "/pentagon-travel",
        "/perils-of-the-new-pesticides",
        "/poisoned-places",
        "/politics-of-poison",
        "/primary-source",
        "/profiles-in-patronage",
        "/profiting-from-prisoners",
        "/raw-deal",
        "/renegade-refineries",
        "/scared-red",
        "/science-for-sale",
        "/sexual-assault-on-campus",
        "/sexual-assault-on-campus-education",
        "/shadow-government",
        "/silent-partners",
        "/skin-and-bone",
        "/source-check",
        "/state-integrity-2012",
        "/state-integrity-2015",
        "/state-integrity-investigation",
        "/states-of-disclosure",
        "/stimulating-hypocrisy",
        "/takings-initiatives-accountability-project",
        "/the-gift-economy",
        "/the-great-mortgage-cover-up",
        "/the-misinformation-industry",
        "/the-panama-papers",
        "/the-transportation-lobby",
        "/the-truth-left-behind",
        "/the-water-barons",
        "/tobacco",
        "/toxic-clout",
        "/unequal-risk",
        "/up-in-arms",
        "/weed-rush",
        "/weekly-watchdog",
        "/well-connected",
        "/wendell-potter-commentary",
        "/who-bankrolls-congress",
        "/whos-behind-the-financial-meltdown",
        "/whos-calling-the-shots-in-state-politics",
        "/workers-rights"
    );

    const MICROSITE_TOPICS = array(
        // "/abandoned-in-america",
        // "/blowout",
        "/disclosure",
        "/the-trench",
        // "/united-states-of-petroleum",
        // "/nuclear-negligence",
        "/oil-education"
    );

    const MICROSITES = array(
        "https://apps.publicintegrity.org/abandoned-in-america/yazoo-education-history/" => "/race-relations-in-yazoo-city-mississippi-a-brief-history",
        "https://apps.publicintegrity.org/abandoned-in-america/forgotten-and-failing/" => "/forgotten-and-failing-black-students-languish-as-a-mississippi-town-reckons-with-its-painful-past",
        "https://apps.publicintegrity.org/abandoned-in-america/no-place-to-call-home/" => "/st-louis-poorest-residents-ask-why-cant-our-houses-be-homes",
        "https://apps.publicintegrity.org/abandoned-in-america/train-off-track/" => "/high-speed-rail-could-transform-fresno-will-trump-get-on-the-train",
        "https://apps.publicintegrity.org/abandoned-in-america/ballot-box-barriers/" => "/what-stands-in-the-way-of-native-american-voters",
        "https://apps.publicintegrity.org/abandoned-in-america/disastrous-recovery/" => "/hope-to-hopelessness-will-government-step-up-after-second-storm",
        "https://apps.publicintegrity.org/abandoned-in-america/walled-off/" => "/how-trumps-wall-could-kill-a-texas-border-town",
        "https://apps.publicintegrity.org/abandoned-in-america/border-closing-history/" => "/the-closing-of-an-international-border-a-brief-history",
        "https://apps.publicintegrity.org/blowout/us-energy-dominance/" => "/how-washington-unleashed-fossil-fuel-exports-and-sold-out-on-climate",
        "https://apps.publicintegrity.org/blowout/" => "/as-oil-and-gas-exports-surge-west-texas-becomes-the-worlds-extraction-colony",
        "https://apps.publicintegrity.org/tax-breaks-the-favored-few/" => "/congress-snuck-dozens-of-tax-breaks-into-the-budget-deal-heres-where-they-went",
        "https://apps.publicintegrity.org/the-trench/" => "/death-by-suffocation-under-a-pile-of-dirt-jim-spencers-on-the-job-death-shows-the-weakness-of-americas-worker-safety-laws",
        "https://apps.publicintegrity.org/united-states-of-petroleum/" => "/the-united-states-of-petroleum-governments-secret-alliance-with-big-oil",
        "https://apps.publicintegrity.org/disclosure/statehouses/" => "/find-your-state-legislators-financial-interests",
        "https://apps.publicintegrity.org/nuclear-negligence/shipping-violations/" => "/nuclear-weapons-contractors-repeatedly-violate-shipping-rules-for-dangerous-materials",
        "https://apps.publicintegrity.org/nuclear-negligence/repeated-warnings/" => "/repeated-radiation-warnings-go-unheeded-at-sensitive-idaho-nuclear-plant",
        "https://apps.publicintegrity.org/nuclear-negligence/inhaled-uranium/" => "/more-than-30-nuclear-experts-inhale-uranium-after-radiation-alarms-at-a-weapons-site-are-switched-off",
        "https://apps.publicintegrity.org/nuclear-negligence/light-penalties/" => "/light-penalties-and-lax-oversight-encourage-weak-safety-culture-at-nuclear-weapons-labs",
        "https://apps.publicintegrity.org/nuclear-negligence/delayed-warheads/" => "/safety-problems-at-a-los-alamos-laboratory-delay-u-s-nuclear-warhead-testing-and-production",
        "https://apps.publicintegrity.org/nuclear-negligence/near-disaster/" => "/a-near-disaster-at-a-federal-nuclear-weapons-laboratory-takes-a-hidden-toll-on-americas-arsenal",
        "https://apps.publicintegrity.org/oil-education/" => "/oils-pipeline-to-americas-schools",
        "/abandoned-in-america/yazoo-education-history",
        "/abandoned-in-america/forgotten-and-failing",
        "/abandoned-in-america/no-place-to-call-home",
        "/abandoned-in-america/train-off-track",
        "/abandoned-in-america/ballot-box-barriers",
        "/abandoned-in-america/disastrous-recovery",
        "/abandoned-in-america/walled-off",
        "/abandoned-in-america/border-closing-history",
        "/blowout/us-energy-dominance",
        "/disclosure/statehouses",
        "/tax-breaks-the-favored-few",
        "/united-states-of-petroleum/venue-of-last-resort",
        "/united-states-of-petroleum/fueling-dissent",
        "/united-states-of-petroleum/century-of-influence",
        "/disclosure/statehouses",
        "/nuclear-negligence/shipping-violations",
        "/nuclear-negligence/repeated-warnings",
        "/nuclear-negligence/inhaled-uranium",
        "/nuclear-negligence/light-penalties",
        "/nuclear-negligence/delayed-warheads",
        "/nuclear-negligence/near-disaster",
    );


    /**
     * Add filter for redirect
     *
     * @return void
     */
    public function __construct()
    {
        add_filter('template_redirect', array($this, 'redirect_override'));
    }

    /**
     * Setup redirects
     *
     * @return void
     */
    public function redirect_override()
    {
        global $wp_query;

        $requestURI = $_SERVER['REQUEST_URI'];

        // Split requestURI into 2 parts and remove trailing `/`
        // i.e. `/politics/broken-government` becomes `array( '/politics', '/broken-government' );`
        $pattern = '/(\/[a-z \-]+)/m';
        $uriArray = preg_split($pattern, $requestURI, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        // Set uriCheck variable
        if (count($uriArray) >= 2 && $uriArray[1] !== "/") {
            $uriCheck = implode('', $uriArray);
        } elseif (count($uriArray) < 2 || $uriArray[1] === "/") {
            $uriCheck = $uriArray[0];
        }


        // Force redirects for legacy microsite archives

        // Handle microsites (as of Oct. 31st content import)
        // -- Handle microsite topics
        $microTopic = array_search($uriArray[0], self::MICROSITE_TOPICS, true);
        if (!$microTopic && count($uriArray) >= 2) {
            $microTopic = array_search($uriArray[1], self::MICROSITE_TOPICS, true);
        }

        if (is_int($microTopic)) {
            $this->triggerRedirect(self::MICROSITE_TOPICS[$microTopic], true);
        }

        // -- Set microKey variable
        $microKey = array_search($uriArray[0], self::MICROSITES, true);
        if (!$microKey && count($uriArray) >= 2) {
            $microKey = array_search($uriArray[1], self::MICROSITES, true);
        }

        // -- Handle legacy mircrosite paths
        if (is_int($microKey)) {
            $this->triggerRedirect($uriCheck, true);

        // -- Handle imported microsite slugs, pass in full URL as $microKey
        } elseif (is_string($microKey)) {
            $this->triggerRedirect($uriCheck, true, $microKey);
        }


        if ($wp_query->is_404) {
            // Temp fix for '/topics'
            if ($uriArray[0] === '/topics' && count($uriArray) === 1) {
                $this->triggerRedirect('/');
            }


            // ADDITIONAL CHECKS FOR WEIRD LEGACY SUB-TOPIC PATH STRUCTURES
            // Accountability
            if (array_search('/hate-in-america', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/accountability/hate-in-america');
            } else if (array_search('/secrecy-sale', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/accountability/secrecy-for-sale');
            } else if (array_search('/panama-papers', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/accountability/the-panama-papers');
            } else if (array_search('/truth-left-behind', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/accountability/the-truth-left-behind');
            // Business
            } else if (array_search('/after-meltdown', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/business/after-the-meltdown');
            } else if (array_search('/betting-justice', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/business/betting-on-justice');
            } else if (array_search('/profiting-prisoners', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/business/profiting-from-prisoners');
            } else if (array_search('/great-mortgage-cover', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/business/the-great-mortgage-cover-up');
            } else if (array_search('/whos-behind-financial-meltdown', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/business/whos-behind-the-financial-meltdown');
            // Education
            } else if (array_search('/sexual-assault-campus', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/business/sexual-assault-on-campus');
            // Environment
            } else if (array_search('/danger-air', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/danger-in-the-air');
            } else if (array_search('/politics-oil', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/energy/politics-of-oil');
            } else if (array_search('/exposed-decades-of-denial-on-poisons-environment', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/exposed-decades-of-denial-on-poisons-environment');
            } else if (array_search('/looting-seas', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/natural-resources/looting-the-seas');
            } else if (array_search('/looting-seas-i', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/natural-resources/looting-the-seas-i');
            } else if (array_search('/looting-seas-ii', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/natural-resources/looting-the-seas-ii');
            } else if (array_search('/looting-seas-iii', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/natural-resources/looting-the-seas-iii');
            } else if (array_search('/water-barons', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/natural-resources/the-water-barons');
            } else if (array_search('/politics-poison', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/politics-of-poison');
            } else if (array_search('/disaster-gulf', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/pollution/disaster-in-the-gulf');
            } else if (array_search('/science-sale', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/environment/science-for-sale');
            // Federal Politics
            } else if (array_search('/buying-president', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/federal-politics/buying-of-the-president');
            } else if (array_search('/buying-president-2000', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/federal-politics/buying-of-the-president-2000');
            } else if (array_search('/buying-president-2004', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/federal-politics/buying-of-the-president-2004');
            } else if (array_search('/buying-senate-2014', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/federal-politics/buying-the-senate-2014');
            } else if (array_search('/iraq-war-card', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/federal-politics/iraq-the-war-card');
            } else if (array_search('/profiles-patronage', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/federal-politics/profiles-in-patronage');
            } else if (array_search('/chairmen', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/federal-politics/the-chairmen');
            } else if (array_search('/misinformation-industry', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/federal-politics/the-misinformation-industry');
            } else if (array_search('/transportation-lobby', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/federal-politics/the-transportation-lobby');
            // Health
            } else if (array_search('/cracking-codes', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/health/cracking-the-codes');
            } else if (array_search('/dangers-dust', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/health/dangers-in-the-dust');
            } else if (array_search('/island-widows', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/health/island-of-the-widows');
            } else if (array_search('/mystery-fields', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/health/mystery-in-the-fields');
            } else if (array_search('/smoke-screen-part-ii', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/health/tobacco/smoke-screen-2');
            // National Security
            } else if (array_search('/homeland-security-boom-and-bust', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/national-security/homeland-security/homeland-security-boom-bust');
            } else if (array_search('/war-error', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/national-security/intelligence/war-on-error');
            } else if (array_search('/gift-economy', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/national-security/the-gift-economy');
            } else if (array_search('/outsourcing-pentagon', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/national-security/military/outsourcing-the-pentagon');
            } else if (array_search('/arms', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/national-security/up-in-arms');
            } else if (array_search('/windfalls-war', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/national-security/windfalls-of-war');
            // State Politics
            } else if (array_search('/medicaid-under-influence', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/state-politics/medicaid-under-the-influence');
            } else if (array_search('/who-s-calling-shots-state-politics', $uriArray, true) !== false) {
                $this->triggerRedirect('/topics/state-politics/whos-calling-the-shots-in-state-politics');
            }


            // Set URI to be used in conditionals
            if (count($uriArray) >= 2 && $uriArray[1] !== "/") {
                // $uriCheck = implode('', $uriArray);
                $mainTopic = array_search($uriArray[0], self::LEGACY_TOPIC_URIS, true);
                $nestedTopic = array_search($uriArray[1], self::LEGACY_NESTED_TOPIC_URIS, true);
                $metaVal = array($uriArray[0], $uriArray[1]);
            } elseif (count($uriArray) < 2 || $uriArray[1] === "/") {
                // $uriCheck = $uriArray[0];
                $mainTopic = array_search($uriArray[0], self::LEGACY_TOPIC_URIS, true);
                // Change slug to singular words for query
                $metaVal = preg_replace(array('/(\/)/', '/(\-)/'), array('', ' '), $uriArray[0]);
            }

            // Accounts for `/2018/10/18/55544` from legacy site, which prepends post-title slug
            $numberPath = preg_match('/(\/\d{4}\/\d{2}\/\d{2}\/\d+[^\/])/', $uriArray[0], $matches);

            // ONLY handle this logic for paths with 2 or less parts (i.e. '/go-here' or '/go-here/and-there')
            if (count($uriArray) <= 2) {

                // Handle special cases: State Politics, Federal Politics, Education, Workers Rights
                if ($uriCheck === '/politics/state-politics') {
                    $this->triggerRedirect('/topics/state-politics');
                } elseif ($uriCheck === '/politics/federal-politics' || $uriCheck === '/politics') {
                    $this->triggerRedirect('/topics/federal-politics');
                } elseif ($uriCheck === '/accountability/education') {
                    $this->triggerRedirect('/topics/education');
                } elseif ($uriCheck === '/environment/workers-rights') {
                    $this->triggerRedirect('/topics/workers-rights');

                // Handle other topic URIs
                // -- Search legacy main topics
                } elseif ($mainTopic !== false) {
                    if (count($uriArray) < 2 || $uriCheck !== "/") {
                        $this->triggerRedirect('/topics' . $requestURI);
                    // -- Search legacy nested topics
                    } elseif (count($uriArray) >= 2 && $nestedTopic !== false) {
                        $this->triggerRedirect('/topics' . $uriArray[1]);
                    }
                }
            }

            // echo "<pre style='font-family: monospace; font-size: 12px'>";
            // var_dump($mainTopic, $nestedTopic, $numberPath, $matches);
            // echo "</pre>";
            // die();

            // Individual Post redirect only with valid legacy paths
            if ($mainTopic || ($mainTopic && $nestedTopic) || !empty($numberPath)) {
                // Match post title slug if request URI contains legacy date/ID path
                if ($numberPath === 1) {
                    $args = array(
                        'post_status' => array('published'),
                        'order' => 'DESC',
                        'meta_key' => 'legacy_url',
                        'meta_value' => $matches[0],
                        'meta_compare' => 'RLIKE'
                    );

                // Set query_args based on metaVal
                } else if (is_array($metaVal)) {
                    $args = array(
                            'post_status' => array('published'),
                            'order' => 'DESC',
                            'meta_query' => array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'legacy_url',
                                    'value' => $metaVal,
                                    'compare' => 'LIKE'
                                ),
                                array(
                                    'key' => 'microsite_url',
                                    'value' => $metaVal,
                                    'compare' => 'LIKE'
                                )
                            )
                        );
                } else {
                    $args = array(
                            'post_status' => array('published'),
                            'order' => 'DESC',
                            'orderby' => 'relevance',
                            's' => $metaVal,
                            'meta_key' => 'microsite_url',
                            'meta_compare' => 'EXISTS'

                        );
                }

                // CPIPost query using above args
                $queryObj = new PostQuery($args, CPIPost::class);

                if (key_exists(0, $queryObj)) {
                    $post = $queryObj[0];

                    if ($post instanceof CPIPost) {
                        $redirectURI = $post->path;
                        $legacyURL = $post->get_field('legacy_url');
                        $microURL = $post->get_field('microsite_url');

                        // Prioritize redirect with microsite URL
                        if (!empty($microURL)) {
                            $this->triggerRedirect($requestURI, true, $microURL);
                        } elseif (!empty($legacyURL)) {
                            // Check if root in legacyURL matches microsite pattern
                            $microRoot = strpos($legacyURL, '//apps.publicintegrity.org');

                            // Redirect based on root
                            if ($microRoot !== false) {
                                $this->triggerRedirect($redirectURI, true, $legacyURL);

                            // Redirect to new single view if request URI matches legacy URI
                            } else {
                                $legacyURI = preg_replace('/(https:\/\/\S+\.\S+\.org)/i', '', $legacyURL);

                                $legacyURIArray = explode("/", $legacyURI);
                                $pathArr = explode("/", $_SERVER['REQUEST_URI']);
                                // Try to match based on legacy post ID
                                if ((sizeof($legacyURIArray) == 6) && (sizeof($pathArr) == 6) ) {
                                    $requestedID = $pathArr[4];
                                    $legacyId = $legacyURIArray[4];
                                    if ($requestedID === $legacyId) {
                                        $this->triggerRedirect($redirectURI);
                                    }
                                }

                                $this->urlMatchPath($legacyURI) ? $this->triggerRedirect($redirectURI) : '';
                            }
                        }
                    }
                }
            }
            // end Individual Post with valid path

            // before we fail, try redirect by just post id
            if (count($matches) > 0) {
                $urlArray = explode("/", $matches[0]);
                $legacyId = $urlArray[4];
                $legacyIdInt = intval($legacyId);

                if (is_int($legacyIdInt) && ($legacyIdInt > 0) ) {
                    $args = array(
                        'post_status' => array('published'),
                        'order' => 'DESC',
                        'meta_key' => 'legacy_url',
                        'meta_value' => $legacyId,
                        'meta_compare' => 'RLIKE'
                    );
                    $queryObj = new PostQuery($args, CPIPost::class);

                    if (key_exists(0, $queryObj)) {
                        $post = $queryObj[0];
                        $redirectURI = $post->path;
                        $this->triggerRedirect($redirectURI);
                    }
                }
            }

            WordPressService::abort_request(404);
        }
    }

    /**
     * Check for match based on PHP_URL_QUERY
     *
     * @param string $string String to match against
     *
     * @return boolean
     */
    private function urlMatch($string)
    {
        parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $output);
        $contentId = $output['id'];
        return $contentId == $string;
    }

    /**
     * Check for match based on $_SERVER['REQUEST_URI']
     *
     * @param string $string String to match against REQUEST_URI
     *
     * @return boolean
     */
    private function urlMatchPath($string)
    {
        $path = $_SERVER['REQUEST_URI'];
        return $path === $string;
    }

    /**
     * Triggers redirect
     *
     * @param string $uri       URI to append to CPI_DOMAIN
     * @param bool   $microsite Indicate if redirect is for microsites
     * @param string $microURL  Control for microsite slugs in WP from import
     *
     * @return void
     */
    private function triggerRedirect($uri, bool $microsite = false, string $microURL = '')
    {
        // TODO: Only redirect if the ID is an exact match
        if (!empty($microURL) && $microsite) {
            header('HTTP/1.0 200');
            header("Location: $microURL");
            die();
        } else if ($microsite) {
            header('HTTP/1.0 200');
            header('Location: https://apps.publicintegrity.org' . $uri);
            die();
        } else {
            global $wp_query;
            status_header(200);
            $wp_query->is_404 = false;

            header('HTTP/1.0 301 Moved Permanently');
            header('Location: ' . CPI_DOMAIN . $uri);
            die();
        }
    }
}
