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
            /* Ensures content wraps within the cell */
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
    </style>
</head>

<body>
    <!-- Header Container -->
    <div class="uk-section  uk-padding">
        <div class="uk-container">
            <h1 class="uk-heading-line"><span>PageSpeed Insights Results</span></h1>
            <h2>URL: <?php echo $url; ?></h2>
            <?php echo $parser->displayPageSpeedResults(); ?>
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
        });
    </script>



</body>

</html>