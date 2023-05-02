<?php
ini_set('memory_limit', '-1');
$enablelogging = 0;
$enablecounter = 1;

$adipath = "file.adi";

$image = [
    "path" => "card.jpg",
    "forcedownload" => false,
    "resize" => [
        "enabled" => true,
        "width" => 1600,
        "height" => 900
    ]
];

// HTML Table
$table = [
    "fields" => [
        [
            "value" => "call",
            "title" => "Callsign",
            "size" => 2
        ],
        [
            "value" => "qso_date",
            "title" => "Date",
            "size" => 1
        ],
        [
            "value" => "time_on",
            "title" => "UTC Time",
            "size" => 1
        ],
        [
            "value" => "band",
            "title" => "Band",
            "size" => 1
        ],
        [
            "value" => "rst_sent",
            "title" => "Report",
            "size" => 1
        ],
        [
            "value" => "mode",
            "title" => "Mode",
            "size" => 1
        ]
    ]
];

// Frame on an image
$frame = [
    "foreground" => "#000000",
    "background" => "#FFFFFF",
    "fontsize" => 24,
    "pos" => [
        "x" => 0,
        "y" => 0
    ],
    "length" => 900,
    "padding" => 7,
    "fields" => [
        [
            "value" => "call",
            "title" => "Confirming QSO with",
            "size" => 2
        ],
        [
            "value" => "qso_date",
            "title" => "Date",
            "size" => 1
        ],
        [
            "value" => "time_on",
            "title" => "UTC Time",
            "size" => 1
        ],
        [
            "value" => "band",
            "title" => "Band",
            "size" => 1
        ],
        [
            "value" => "rst_sent",
            "title" => "Report",
            "size" => 1
        ],
        [
            "value" => "mode",
            "title" => "Mode",
            "size" => 1
        ]
    ]
];

?>
