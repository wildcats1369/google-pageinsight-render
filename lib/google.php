<?php
require __DIR__ . '/../vendor/autoload.php';

use Google\Client;
use Google\Service\PagespeedInsights;

class PagespeedInsightsParser
{
    var $url, $audits, $page_speed_result, $field_types;
    var $audit_types = [];
    function __construct($url)
    {
        $this->url = $url;
        $this->getPageSpeedInsights();
        // $this->openAudit();
        $this->parsePageInsights();

    }

    function parsePageInsights()
    {
        foreach ($this->page_speed_result['lighthouseResult']['audits'] as $key => $value) {
            if (empty($value['details'])) {
                continue;
            }
            if (empty($value['details']['items']) && empty($value['details']['nodes'])) {
                continue;
            }
            $this->audit_types[] = $key;
            $this->audits[$key]['title'] = $value['title'];
            $this->audits[$key]['description'] = $value['description'];
            $type = (isset($value['details']['type'])) ? $value['details']['type'] : 'none';
            // echo "<h1>" . $value['title'] . ':' . $key . ' : ' . $type . '</h1><br/>';
            if ($key == 'final-screenshot') {
                echo "<pre>";
                var_dump(json_encode($value, JSON_PRETTY_PRINT));
            }
            if (isset($value['details']['type'])) {
                $this->audits[$key]['html'] = $this->renderAuditDetails($key);
            } else {
                $this->audits[$key]['html'] = isset($value['displayValue']) ? "<pre>{$value['displayValue']}</pre>" : '<pre>-- no report available -- </pre>';
            }
        }
    }

    function renderExperienceMetrics($key, $data)
    {
        // key: CUMULATIVE_LAYOUT_SHIFT_SCORE
        // data:  {
        //         "category": "AVERAGE",
        //         "formFactor": null,
        //         "median": null,
        //         "metricId": null,
        //         "percentile": 15,
        //         "distributions": [
        //             {
        //                 "max": 10,
        //                 "min": 0,
        //                 "proportion": 0.4809
        //             },
        //             {
        //                 "max": 25,
        //                 "min": 10,
        //                 "proportion": 0.4786
        //             },
        //             {
        //                 "max": null,
        //                 "min": 25,
        //                 "proportion": 0.0405
        //             }
        //         ]
        //     }
        // <div id="progress-container" class="uk-card uk-card-body">
        //     <div class="">THIS IS LABEL</div>
        //     <div class="radial-progress uk-box-shadow-medium" data-percentile="7"
        //         data-distribution='[{"min": 0, "max": 10, "proportion": 0.4809}, {"min": 10, "max": 25, "proportion": 0.4786}, {"min": 25, "max": null, "proportion": 0.0405}]'>
        //         <div class="mask"></div>
        //         <div class="inner-ring"></div>
        //         <div class="percentile-value"></div>

        //     </div>
        //     <div>Label</div>

        $distribution = json_encode($data['distributions']);

        $percentile = $data['percentile'];
        $label = ucwords(strtolower(str_replace('_', ' ', $key)));

        $html = '<div id="progress-container" class="uk-card uk-card-body uk-card-dark uk-width-1-3">
            <div class="">' . $label . '</div>
            <div class="radial-progress uk-box-shadow-medium" 
                data-percentile="' . htmlspecialchars($percentile, ENT_QUOTES, 'UTF-8') . '"
        data-distribution="' . htmlspecialchars($distribution, ENT_QUOTES, 'UTF-8') . '">
                <div class="mask"></div>
                <div class="inner-ring"></div>
                <div class="percentile-value"></div>
            </div>
           
            </div>';

        return $html;
    }

    function renderAuditDetails($key)
    {
        $html = '';
        $value = $this->page_speed_result['lighthouseResult']['audits'][$key];
        if (empty($value['details'])) {
            return;
        }
        if (empty($value['details']['items']) && empty($value['details']['nodes'])) {
            return;
        }
        $type = (isset($value['details']['type'])) ? $value['details']['type'] : 'none';
        if (isset($value['details']['type'])) {
            $xvalue = $value['details'] ?? $value['displayValue'];
            $fname = str_replace('-', '_', $key);
            $types[] = $value['details']['type'];

            $html .= $this->renderData($value['details'], $value['title']);
        } else {
            $html .= isset($value['displayValue']) ? "<pre>{$value['displayValue']}</pre>" : '<pre>-- no report available -- </pre>';
        }

        return $html;

    }


    function getPageSpeedInsights()
    {
        if (is_file(__DIR__ . '/' . $this->getDomain() . '.json')) {
            $this->page_speed_result = json_decode(file_get_contents(__DIR__ . '/' . $this->getDomain() . '.json'), true);
        } else {
            $client = new Client();
            $client->setApplicationName('PageSpeed Insights');
            $client->setDeveloperKey('AIzaSyB1S28uVxhVlbs9Xe2WbS0yX5X2Q2QYoNU'); // Replace with your API key

            $service = new PagespeedInsights($client);
            $optParams = array('strategy' => 'mobile'); // You can use 'desktop' or 'mobile'

            $results = $service->pagespeedapi->runpagespeed($this->url, $optParams);
            $this->page_speed_result = json_decode(json_encode($results, JSON_PRETTY_PRINT), true);
            $ch = fopen(__DIR__ . '/' . $this->getDomain() . '.json', 'w');
            fwrite($ch, json_encode($results, JSON_PRETTY_PRINT));
            fclose($ch);
        }


    }

    function getDomain()
    {
        $parsedUrl = parse_url($this->url);
        $host = $parsedUrl['host'];

        // Remove 'www.' if it exists
        if (strpos($host, 'www.') === 0) {
            $host = substr($host, 4);
        }

        return $host;
    }

    function openAudit()
    {
        $this->page_speed_result = json_decode(file_get_contents(__DIR__ . '/audit.json'), true);
    }

    function displayPageSpeedResults()
    {    //#4e4f51
        $html = '';
        $html .= '<h2>Performance Metrics</h2>';
        $html .= '<ul>';

        foreach ($this->page_speed_result['lighthouseResult']['audits'] as $key => $value) {
            if (empty($value['displayValue'])) {
                continue;
            }
            $mark = (in_array($key, $this->audit_types)) ? '<a href="#' . $key . '"><span class="uk-badge uk-badge-grey uk-margin-small-right uk-icon" uk-icon="more"></span></a>' : '';
            $html .= '<li ><strong class="uk-text-bold uk-text-primary">' . $value['title'] . ':</strong> ' . $value['displayValue'] . ' ' . $mark . '</li>';

        }

        // echo '<ul>';
        // echo '<li>First Contentful Paint: ' . (isset($audits['first-contentful-paint']) ? $audits['first-contentful-paint']->displayValue : 'N/A') . '</li>';
        // echo "<li><pre>";
        // print_r($audits['first-contentful-paint']);
        // echo "</pre></li>";
        // echo '<li>Speed Index: ' . (isset($audits['speed-index']) ? $audits['speed-index']->displayValue : 'N/A') . '</li>';
        // echo '<li>Time to Interactive: ' . (isset($audits['interactive']) ? $audits['interactive']->displayValue : 'N/A') . '</li>';
        // echo '<li>First Meaningful Paint: ' . (isset($audits['first-meaningful-paint']) ? $audits['first-meaningful-paint']->displayValue : 'N/A') . '</li>';
        // echo '<li>First CPU Idle: ' . (isset($audits['first-cpu-idle']) ? $audits['first-cpu-idle']->displayValue : 'N/A') . '</li>';
        // echo '<li>Estimated Input Latency: ' . (isset($audits['estimated-input-latency']) ? $audits['estimated-input-latency']->displayValue : 'N/A') . '</li>';
        // $html .= '</ul>';
        return $html;
    }

    function displayNestedArray($array)
    {
        $result = '';
        if (empty($array)) {
            return;
        }
        if (! is_array($array) && ! is_object($array)) {
            $result .= $array;
            return;
        }
        $result .= '<ul class="uk-list uk-list-divider">';
        foreach ($array as $key => $value) {
            $value = (is_object($value)) ? json_decode(json_encode($value), true) : $value;
            if (is_array($value)) {
                $result .= '<li><strong class="uk-text-bold uk-text-primary">' . $this->camelToWords(htmlspecialchars($key)) . ':</strong>';
                $result .= $this->displayNestedArray($value); // Recursive call
                $result .= '</li>';
            } else {
                $result .= '<li><strong class="uk-text-bold uk-text-primary">' . $this->camelToWords(htmlspecialchars($key)) . ': </strong>' . htmlspecialchars($value) . '</li>';
            }
        }
        $result .= '</ul>';
        return $result;
    }

    function uses_passive_event_listeners($data)
    {
        echo "this is a custom function uses_passive_event_listeners<hr>";
    }

    function server_response_time($data)
    {
        foreach ($data['items'] as $val) {
            echo "<strong>Url: </strong> {$val['url']}<br>";
            echo "<strong>Response Time: </strong> {$val['responseTime']}<br>";
        }
    }

    function camelToDashed($camelStr)
    {
        $dashedStr = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $camelStr));
        return $dashedStr;
    }

    function camelToWords($str)
    {
        return ucwords(preg_replace('/([a-z])([A-Z])/', '$1 $2', $str));
    }

    function renderOpportunity($data)
    {
        $html = '<table class="uk-table uk-table-divider">';
        $html .= '<thead><tr>';

        foreach ($data['headings'] as $heading) {
            $label = ($heading['label']) ?? '';
            $html .= '<th>' . htmlspecialchars($label) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($data['items'] as $item) {
            $html .= '<tr>';
            foreach ($data['headings'] as $heading) {
                $key = $heading['key'];
                $valueType = $heading['valueType'] ?? 'N/A';
                $key_value = isset($item[$key]) ? (is_string($item[$key]) || is_numeric($item[$key]) ? $this->renderField($valueType, $item[$key]) : $this->displayNestedArray($item[$key])) : 'N/A';
                $html .= '<td>' . $key_value . '</td>';
                $this->field_types[] = $valueType;
            }
            $html .= '</tr>';

            if (isset($item['nodes']) && is_array($item['nodes'])) {
                $html .= '<tr><td colspan="' . count($data['headings']) . '">';
                $html .= $this->renderTable(['items' => $item['nodes'], 'headings' => $data['headings']]);
                $html .= '</td></tr>';
            }
        }

        $html .= '</tbody></table>';
        return $html;
    }

    function renderTable($data)
    {
        $html = '<table class="uk-table uk-table-divider">';
        $html .= '<thead><tr>';

        // Generate table headers
        foreach ($data['headings'] as $heading) {
            $label = ($heading['label']) ?? '';
            $html .= '<th>' . htmlspecialchars($label) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        // Generate table rows
        foreach ($data['items'] as $item) {
            $html .= '<tr>';
            foreach ($data['headings'] as $heading) {
                $key = $heading['key'];
                $valueType = $heading['valueType'] ?? 'N/A';
                $key_value = isset($item[$key]) ? (is_string($item[$key]) || is_numeric($item[$key]) ? $this->renderField($valueType, $item[$key]) : $this->displayNestedArray($item[$key]))
                    : 'N/A';
                $html .= '<td>' . $key_value . '</td>';
                $this->field_types[] = $valueType;
            }
            $html .= '</tr>';

            //Check for nested nodes
            if (isset($item['node']) && is_array($item['node'])) {
                $html .= $this->displayNestedArray($item['node']);
                // $html .= '<tr><td colspan="' . count($data['headings']) . '">';
                // $html .= renderNode($item['node']);
                // $html .= '</td></tr>';
            }
        }

        $html .= '</tbody></table>';
        return $html;
    }

    function renderNode($node)
    {
        $html = '<table class="uk-table uk-table-divider">';
        $html .= '<thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>';

        foreach ($node as $key => $value) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($this->camelToWords($key)) . '</td>';
            $html .= '<td>' . (is_string($value) || is_numeric($value) ? htmlspecialchars($value) : $this->displayNestedArray($value)) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        return $html;
    }

    function renderDebugData($data, $title)
    {

        if (empty($data['items'])) {
            return;
        }
        $html = '<table class="uk-table uk-table-divider">';
        $html .= '<thead><tr><th colspan="2">' . $title . '</th></tr></thead><tbody>';

        try {
            foreach ($data['items'][0] as $key => $value) {
                $html .= '<tr>';
                $html .= '<td><strong class="uk-text-bold uk-text-primary">' . htmlspecialchars($this->camelToWords($key)) . '</strong></td>';
                $html .= '<td>' . (is_string($value) || is_numeric($value) ? htmlspecialchars($value) : $this->displayNestedArray($value)) . '</td>';
                $html .= '</tr>';
            }
        } catch (\Exception $e) {
            echo "<pre><h1>ERROR DATA</h1>";
            var_dump($data);
            echo $e->getMessage();
            exit();
        }


        $html .= '</tbody></table>';
        return $html;
    }

    function renderFilmstrip($data)
    {
        $html = '<div class="uk-card uk-card-default uk-card-body">';
        foreach ($data['items'] as $item) {
            $html .= '<img src="' . htmlspecialchars($item['data']) . '" alt="Filmstrip Image" class="uk-thumbnail" style="width: 150px; " />';
        }
        $html .= '</div>';
        return $html;
    }

    function renderScreenshot($data)
    {
        return '<div class="uk-card uk-card-default uk-card-body"><img src="' . htmlspecialchars($data['data']) . '" alt="Screenshot" class="uk-thumbnail" style="width: 150px; height: 150px;" /></div>';
    }

    function renderCriticalRequestChain($data)
    {
        var_dump($data);
        $html = '<ul class="uk-list uk-list-divider">';
        foreach ($data['chains'] as $chain) {
            $html .= '<li>';
            $html .= '<ul>';
            foreach ($chain['children'] as $child) {
                $html .= '<li>' . htmlspecialchars($child['request']['url']) . '</li>';
            }
            $html .= '</ul>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    function renderTreemapData($data, $title)
    {
        $html = '<table class="uk-table uk-table-divider">';
        $html .= '<thead><tr><th colspan="2">' . $title . '</th></tr></thead><tbody>';

        foreach ($data['nodes'] as $node) {
            foreach ($node as $key => $value) {
                $html .= '<tr>';
                $html .= '<td><strong class="uk-text-bold uk-text-primary">' . htmlspecialchars($this->camelToWords($key)) . '</strong></td>';
                $html .= '<td>' . (is_string($value) || is_numeric($value) ? htmlspecialchars($value) : $this->displayNestedArray($value)) . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table>';
        return $html;
    }

    function renderList($data)
    {
        $html = '';
        foreach ($data['items'] as $item) {
            $html .= $this->renderTable($item);
        }
        return $html;
    }

    function renderData($data, $title)
    {
        switch ($data['type']) {
            case 'opportunity':
                return $this->renderOpportunity($data);
            case 'table':
                return $this->renderTable($data);
            case 'debugdata':
                return $this->renderDebugData($data, $title);
            case 'filmstrip':
                return $this->renderFilmstrip($data);
            case 'screenshot':
                return $this->renderScreenshot($data);
            case 'criticalrequestchain':
                // return $this->renderCriticalRequestChain($data);
                return $this->displayNestedArray($data);
            case 'treemap-data':
                return $this->renderTreemapData($data, $title);
            case 'list':
                return $this->renderList($data);
            default:
                return '<p>Unknown data type</p>';
        }
    }

    function renderField($type, $value)
    {
        $type = $this->dashedToCamelCase($type);
        if (method_exists($this, 'render' . $type)) {
            return $this->{'render' . $type}($value);
        } else {
            return htmlspecialchars($value);
        }
    }

    function renderUrl($url)
    {
        return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
    }

    function renderBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return number_format($bytes / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    function renderMs($ms)
    {
        $units = ['ms', 'sec', 'min', 'hr'];
        $values = [1, 1000, 60000, 3600000];

        if ($ms < 1) {
            return number_format($ms, 3, '.', ',') . ' ms';
        } elseif ($ms < 1000) {
            return number_format($ms, 2, '.', ',') . ' ms';
        }

        $power = floor(log($ms, 1000));
        $power = min($power, count($units) - 1); // Ensure power does not exceed the units array length

        return number_format($ms / $values[$power], 2, '.', ',') . ' ' . $units[$power];
    }

    // see how node works first

    function renderText($text)
    {
        return htmlspecialchars($text);
    }

    function renderTimespanMs($ms)
    {
        return $this->renderMs($ms);
    }

    function renderCode($code)
    {
        return '<pre><code>' . $code . '</code></pre>';
    }

    function renderSourceLocation($location)
    {
        return '<pre><code>' . $location . '</code></pre>';
    }



    function dashedToCamelCase($string)
    {
        // Replace dashes with spaces
        $str = str_replace('-', ' ', $string);

        // Capitalize the first letter of each word
        $str = ucwords($str);

        // Remove spaces
        $str = str_replace(' ', '', $str);

        return $str;
    }


}



// audit display type
// Array
// (
//     [0] => debugdata
//     [1] => opportunity
//     [2] => table
//     [4] => 
//     [9] => treemap-data
//     [23] => filmstrip
//     [33] => list
//     [41] => criticalrequestchain
//     [47] => screenshot
// )