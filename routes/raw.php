<?php

    Route::get('raw/artikel', 'ItemController@raw');
    Route::get('raw/aufgaben', 'Todos\TodoController@raw');
    Route::get('raw/auftraege', 'OrderController@raw');
    Route::get('raw/kontakte', 'ContactController@raw');

?>