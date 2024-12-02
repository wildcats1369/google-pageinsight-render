<?php
include_once("lib/google.php");
$url = 'https://www.m88.com';
$parser = new PagespeedInsightsParser($url);
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CSV CRUD</title>
    <link rel="stylesheet" href="assets/uikit-3.21.7/css/uikit.min.css">
    <script src="assets/uikit-3.21.7/js/uikit.min.js"></script>
    <script src="assets/uikit-3.21.7/js/uikit-icons.min.js"></script>
    <script src="assets/uikit-3.21.7/js/jquery.min.js"></script>
    <style>
        /* Custom CSS for a futuristic, minimal, and professional look */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #1a1a1a;
            color: #e0e0e0;
            margin: 0;
            padding: 0;
        }

        h1,
        h2 {
            color: #FFF;
        }

        .uk-container {
            margin-top: 20px;
            padding: 20px;
            background-color: #2a2a2a;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h1.uk-heading-line span {
            background-color: #3a3a3a;
            color: #fff;
            padding: 0 15px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .uk-accordion-title {
            background-color: #3a3a3a;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .uk-accordion-title:hover {
            background-color: #555;
        }

        .uk-accordion-content {
            background-color: #333;
            padding: 15px;
            border: 1px solid #444;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .uk-table {
            width: 100%;
            margin-top: 10px;
            table-layout: fixed;
            /* Ensures the table fits within the container */
            word-wrap: break-word;
            /* Ensures long words break to fit within the cell */
            border-collapse: collapse;
            /* Ensures borders are collapsed for a cleaner look */
            color: #e0e0e0;
        }

        .uk-table th,
        .uk-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #444;
            word-wrap: break-word;
            /* Ensures content wraps with in t he cell */
        }

        .uk-table th {
            background-color: #444;
            font-weight: bold;
            color: #e0e0e0;
        }

        .uk-table tbody tr:nth-child(even) {
            background-color: #3a3a3a;
            /* Alternating row colors */
        }

        .uk-table tbody tr:hover {
            background-color: #444;
            /* Hover effect for rows */
        }

        .uk-table-divider tbody tr:last-child td {
            border-bottom: none;
        }

        pre {
            white-space: pre-wrap;
            /* Wraps the content */
            word-wrap: break-word;
            /* Breaks long words */
            overflow: hidden;
            /* Hides overflow */
            background-color: #2a2a2a;
            padding: 10px;
            border-radius: 5px;
            color: #e0e0e0;
        }

        ul {
            list-style-type: none;
            /* Removes the default bullet points */
            padding-left: 0;
            /* Removes the default padding */
        }

        li {
            padding-left: 10px;
            /* Optional: Add some padding for better alignment */
        }

        .uk-badge-grey {
            background: #4e4f51;
        }

        /* Progress css */
        .radial-progress {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(#66bb6a 0% 33%, #ffa726 33% 66%, #ef5350 66% 100%);
            border: 1px solid grey;
            /* Added grey border */
        }

        .radial-progress .mask {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: white;
            clip-path: circle(45%);
            border: 1px solid grey;
            /* Added grey border to inner mask */
        }

        .radial-progress .inner-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            clip-path: circle(45%);
        }

        .radial-progress .percentile-value {
            position: absolute;
            color: #000;
            background-color: white;
            border-radius: 50%;
            width: 75px;
            height: 75px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            line-height: 75px;
            border: 1px solid grey;
            /* Added grey border to inner value circle */
        }

        /* .uk-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        } */

        #progress-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .radial-progress {
            margin: 20px 0;

            /* Add some margin for spacing */
        }
    </style>
</head>

<body>
    <!-- Header Container -->
    <div class="uk-section  uk-padding">
        <div class="uk-container uk-margin-top">
            <h1><span>PageSpeed Insights Results</span></h1>
            <h2>URL: <?php echo $url; ?></h2>
            <?php echo $parser->displayPageSpeedResults(); ?>
        </div>
    </div>
    <div class="uk-section uk-padding">
        <div class="uk-container uk-margin-top">
            <h1><span>Loading Experience</span></h1>
            <div class="" uk-grid>
                <?php
                foreach ($parser->page_speed_result['loadingExperience']['metrics'] as $key => $value) {
                    echo $parser->renderExperienceMetrics($key, $value);
                }
                ?>
            </div>
        </div>
    </div>

    <div class="uk-section uk-padding">
        <div class="uk-container uk-margin-top">
            <h1><span>Origin Loading Experience</span></h1>
            <div class="" uk-grid>
                <?php
                foreach ($parser->page_speed_result['originLoadingExperience']['metrics'] as $key => $value) {
                    echo $parser->renderExperienceMetrics($key, $value);
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div class="uk-section  uk-padding">
        <div class="uk-container uk-margin-top">
            <h2>Performance Audits</h2>
            <ul class="uk-accordion" uk-accordion>
                <?php


                foreach ($parser->audit_types as $audit_type) {

                    // echo '<li id-="#' . $audit_type . '"><div class="uk-accordion-title">' . ucwords(str_replace('-', ' ', $audit_type)) . '</div>';
                    echo '<li id="' . $audit_type . '"><div class="uk-accordion-title">' . $parser->audits[$audit_type]['title'] . '</div>';
                    echo '<div class="uk-accordion-content">
            <pre><code>' . $parser->audits[$audit_type]['description'] . '</code></pre>
            ' . $parser->audits[$audit_type]['html'] . '</div>
            </li>';

                }
                // echo "<pre>";
                // var_dump(array_unique($parser->field_types));
                ?>
            </ul>
        </div>
    </div>

    <script>
        function openAccordionFromHash() {
            // Check if there's a hash in the URL
            if (window.location.hash) {
                // Get the hash value
                var hash = window.location.hash.substring(1);
                // Find the corresponding accordion item
                var $target = $('#' + hash);
                if ($target.length) {
                    // Open the accordion item
                    UIkit.accordion($target.closest('.uk-accordion')).toggle($target);
                }
            }
        }

        $(document).ready(function () {
            // Open accordion on page load
            openAccordionFromHash();

            // Open accordion on hash change
            $(window).on('hashchange', function () {
                openAccordionFromHash();
            });

            // Radial
            // $('.radial-progress').each(function () {
            //     const $this = $(this);
            //     const percentile = parseFloat($this.data('percentile'));
            //     const distributions = JSON.parse($this.attr('data-distribution'));

            //     createRadialProgressBar($this, percentile, distributions);
            // });

            // function createRadialProgressBar(element, percentile, distributions) {
            //     const maxDistribution = Math.max(...distributions.map(dist => dist.max !== null ? dist.max : 0));
            //     const angle = (percentile / maxDistribution) * 360;

            //     let color = '#42a5f5'; // Default color
            //     if (percentile <= 10) {
            //         color = '#66bb6a'; // Green
            //     } else if (percentile <= 25) {
            //         color = '#ffa726'; // Orange
            //     } else {
            //         color = '#ef5350'; // Red
            //     }

            //     const innerColor = shadeColor(color, -20); // Slightly darker shade for inner ring

            //     element.find('.inner-ring').css('background', `conic-gradient(${innerColor} 0deg, ${innerColor} ${angle}deg, transparent ${angle}deg)`);
            //     element.find('.percentile-value').text(`${percentile}`);
            // }

            // function shadeColor(color, percent) {
            //     const num = parseInt(color.slice(1), 16),
            //         amt = Math.round(2.55 * percent),
            //         R = (num >> 16) + amt,
            //         G = (num >> 8 & 0x00FF) + amt,
            //         B = (num & 0x0000FF) + amt;
            //     return `#${(0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 + (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 + (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1).toUpperCase()}`;
            // }

            //Radial 2
            $(document).ready(function () {
                $('.radial-progress').each(function () {
                    const $this = $(this);
                    const percentile = parseFloat($this.data('percentile'));
                    const distributions = JSON.parse($this.attr('data-distribution'));

                    createRadialProgressBar($this, percentile, distributions);
                });

                function createRadialProgressBar(element, percentile, distributions) {
                    let angle = 0;
                    let gradientParts = [];
                    distributions[2].max = calculateThirdMax(distributions);
                    console.log(distributions);
                    const ranges = calculateDynamicRanges(distributions);
                    colors = ['#66bb6a', '#ffa726', '#ef5350'];
                    let c = 0;
                    totalProportion = 0;
                    let maxDistribution = Math.max(...distributions.map(dist => dist.max !== null ? dist.max : 0));

                    distributions.forEach(dist => {
                        let proportion = dist.max / maxDistribution;
                        const proportionAngle = proportion * 360;
                        console.log(c, proportion, proportionAngle);

                        totalProportion += proportion;
                        let rangeColor = colors[c];
                        console.log(`${rangeColor} ${angle}deg, ${rangeColor} ${proportionAngle}deg`);
                        gradientParts.push(`${rangeColor} ${angle}deg, ${rangeColor} ${proportionAngle}deg`);
                        angle = proportionAngle;
                        // console.log(dist.proportion, totalProportion, angle);
                        c++;
                    });

                    const gradient = `conic-gradient(${gradientParts.join(', ')})`;
                    element.css('background', gradient);

                    // Calculate the inner ring angle based on the percentile 
                    for (const dist of distributions) {
                        if (percentile >= dist.min && (dist.max === null || percentile <= dist.max)) {
                            const range = (dist.max === null ? percentile : dist.max) - dist.min;
                            const positionInRange = percentile - dist.min;
                            // angle = (positionInRange / range) * 360;
                            break;
                        }
                    }

                    angle = calculatePercentileAngle(percentile, distributions);

                    const innerColor = shadeColor(getColorForRange(percentile, percentile, ranges), -20); // Slightly darker shade for inner ring
                    element.find('.inner-ring').css('background', `conic-gradient(${innerColor} 0deg, ${innerColor} ${angle}deg, transparent ${angle}deg)`);
                    element.find('.percentile-value').text(`${percentile}`);
                }

                function calculateDynamicRanges(distributions) {
                    let ranges = [];
                    distributions.forEach(dist => {
                        if (dist.max !== null) {
                            ranges.push(dist.max);
                        }
                    });
                    return ranges;
                }

                function getColorForRange(min, max, ranges) {
                    const colors = ['#66bb6a', '#ffa726', '#ef5350']; // Green, Orange, Red

                    if (max === null) {
                        return colors[2]; // Red for the last range
                    } else if (max <= ranges[0]) {
                        return colors[0]; // Green for the first range
                    } else if (max <= ranges[1]) {
                        return colors[1]; // Orange for the second range
                    } else {
                        return colors[2]; // Red for the third range
                    }
                }

                function shadeColor(color, percent) {
                    const num = parseInt(color.slice(1), 16),
                        amt = Math.round(2.55 * percent),
                        R = (num >> 16) + amt,
                        G = (num >> 8 & 0x00FF) + amt,
                        B = (num & 0x0000FF) + amt;
                    return `#${(0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 + (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 + (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1).toUpperCase()}`;
                }
            });

            function calculatePercentileAngle(percentile, distributions) {
                // Step 1: Get the max distribution of all distributions
                let maxDistribution = Math.max(...distributions.map(dist => dist.max !== null ? dist.max : 0));

                // Step 2: Get the proportion of the percentile based on the max distribution
                let proportion = percentile / maxDistribution;

                // Step 3: Convert that proportion into an angle
                let angle = proportion * 360;
                console.log(percentile, maxDistribution, proportion, angle);
                console.log('x');
                return angle;
            }

            function calculateThirdMax(distributions) {
                // Calculate the total range of distributions with defined max values
                const totalRange = distributions
                    .filter(dist => dist.max !== null)
                    .reduce((sum, dist) => sum + (dist.max - dist.min), 0);

                // Get the proportion of the third distribution
                const thirdMaxProportion = distributions[distributions.length - 1].proportion;

                // Calculate the third max value
                const thirdMax = thirdMaxProportion * totalRange + distributions[distributions.length - 1].min;

                return thirdMax;
            }


        });
    </script>



</body>

</html>