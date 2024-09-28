<?php

function set_search_field_integer($text)
{
    if ($text == "varyantlı") {
        return 1;
    } elseif ($text == "tekli") {
        return 0;
    }
}
