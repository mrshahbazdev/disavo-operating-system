<?php

Route::get('/', function () {
    $stats = [
        'modules'      => 6,
        'principles'   => 12,
        'learnings'    => 8,
        'active_edges' => 14,
        'reviews'      => 3,
    ];

    return view('welcome', compact('stats'));
});


