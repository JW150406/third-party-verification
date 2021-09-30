<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sales agent activity messages
    |--------------------------------------------------------------------------
    |
    */
    'success' => [
        'messages' => [
            'clock_in' => "Clock in successfully.",
            'break_in' => "Break in successfully.",
            'arrival_in' => "Arrival time created successfully.",
            'clock_out' => "Clock out successfully.",
            'break_out' => "Break out successfully.",
            'arrival_out' => "Departure successfully.",
        ]
    ],
    'error' => [
        'messages' => [
            'clock_in' => "Sales agent already clock in.",
            'break_in' => "Sales agent already in break.",
            'arrival_in' => "Sales agent already arrival",
            'clock_out' => "Clock in activity not found !!!",
            'break_out' => "Break in activity not found !!!",
            'arrival_out' => "Arrival activity not found !!!",
        ]
    ]
    

];
