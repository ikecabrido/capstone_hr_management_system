<?php

function paginateItems($items, $page = 1, $perPage = 10)
{
    $total = count($items);
    $totalPages = ceil($total / $perPage);
    $offset = ($page - 1) * $perPage;

    return [
        'items' => array_slice($items, $offset, $perPage),
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'hasPrevPage' => $page > 1,
        'hasNextPage' => $page < $totalPages
    ];
}