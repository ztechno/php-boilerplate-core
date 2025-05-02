<?php

use Core\Page;
use Core\Request;

function crudRoute($path, $tableName)
{
    $params = ['table' => $tableName];
    if(isset($_GET['filter']))
    {
        $params['filter'] = $_GET['filter'];
    }
    return routeTo($path, $params);
}

// echo startWith('crud/index', 'crud/');

Page::pushHead('<link rel="stylesheet" href="'.asset('assets/crud/css/styles.css').'" />');

function buildSearch($fields)
{
    $search  = Request::get('search.value', '');
    if(!empty($search))
    {
        $_where = [];
        foreach($fields as $col)
        {
            $_where[] = "$col LIKE '%$search%'";
        }

        return " (".implode(' OR ',$_where).") ";
    }

    return "";
}

function buildFilter()
{
    $filter  = Request::get('filter', []);
    if($filter)
    {
        $filter_query = [];
        foreach($filter as $f_key => $f_value)
        {
            $filter_query[] = "$f_key = '$f_value'";
        }

        $filter_query = implode(' AND ', $filter_query);

        return $filter_query;
    }

    return "";
}