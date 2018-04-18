<?php

$show_more_filter = new Twig_SimpleFilter('show_more', function ($string) {
    $string = strip_tags($string);
    $string = mb_substr($string, 0, 400, "UTF-8") . "...";
    return $string;
});

$view->getEnvironment()->addFilter($show_more_filter);

$curreny_filter = new Twig_SimpleFilter('currency', function ($string) {
    if($string == "1") {
        return "AZN";
    } else if ($string == "2") {
        return "USD";
    } else if ($string == "3") {
        return "EUR";
    }
});

$view->getEnvironment()->addFilter($curreny_filter);

$month_filter = new Twig_SimpleFilter('month', function ($string) {
    if($string == "1") {
        return "Yanvar";
    } else if ($string == "2") {
        return "Fevral";
    } else if ($string == "3") {
        return "Mart";
    } else if ($string == "4") {
        return "Aprel";
    } else if ($string == "5") {
        return "May";
    } else if ($string == "6") {
        return "İyun";
    } else if ($string == "7") {
        return "İyul";
    } else if ($string == "8") {
        return "Avqust";
    } else if ($string == "9") {
        return "Sentyabr";
    } else if ($string == "10") {
        return "Oktyabr";
    } else if ($string == "11") {
        return "Noyabr";
    } else if ($string == "12") {
        return "Dekabr";
    }
});

$view->getEnvironment()->addFilter($month_filter);

$insurance_type_filter = new Twig_SimpleFilter('insurance_type', function ($string) {
    if($string == "1") {
        return "Standart";
    } else if ($string == "2") {
        return "Qızıl";
    } else if ($string == "3") {
        return "Platin";
    } else if ($string == "4") {
        return "Limitsiz";
    }
});

$view->getEnvironment()->addFilter($insurance_type_filter);

$gender_filter = new Twig_SimpleFilter('gender', function ($string) {
    if($string == "m") {
        return "Kişi";
    } else if ($string == "f") {
        return "Qadın";
    }
});

$view->getEnvironment()->addFilter($gender_filter);

