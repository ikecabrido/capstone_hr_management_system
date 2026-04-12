<?php
/**
 * Search & Filter utility functions
 */

/**
 * Filter items by search keyword across multiple fields
 * @param array $items - Array of items to filter
 * @param string $searchTerm - Search keyword
 * @param array $searchFields - Fields to search in
 * @return array Filtered items
 */
function filterBySearch($items, $searchTerm, $searchFields = []) {
    if (empty($searchTerm) || empty($searchFields)) {
        return $items;
    }
    
    $searchLower = strtolower(trim($searchTerm));
    
    return array_filter($items, function($item) use ($searchLower, $searchFields) {
        foreach ($searchFields as $field) {
            if (isset($item[$field])) {
                $value = strtolower((string)$item[$field]);
                if (strpos($value, $searchLower) !== false) {
                    return true;
                }
            }
        }
        return false;
    });
}

/**
 * Filter items by status
 * @param array $items - Array of items to filter
 * @param string $status - Status to filter by
 * @param string $statusField - Field name containing status (default: 'status')
 * @return array Filtered items
 */
function filterByStatus($items, $status, $statusField = 'status') {
    if (empty($status)) {
        return $items;
    }
    
    return array_filter($items, function($item) use ($status, $statusField) {
        return isset($item[$statusField]) && $item[$statusField] === $status;
    });
}

/**
 * Filter items by date range
 * @param array $items - Array of items to filter
 * @param string $dateField - Field name containing date
 * @param string $startDate - Start date (Y-m-d format)
 * @param string $endDate - End date (Y-m-d format)
 * @return array Filtered items
 */
function filterByDateRange($items, $dateField, $startDate, $endDate) {
    if (empty($startDate) || empty($endDate)) {
        return $items;
    }
    
    return array_filter($items, function($item) use ($dateField, $startDate, $endDate) {
        if (!isset($item[$dateField])) return false;
        $itemDate = $item[$dateField];
        return $itemDate >= $startDate && $itemDate <= $endDate;
    });
}

/**
 * Paginate items
 * @param array $items - Array of items to paginate
 * @param int $page - Current page number (1-indexed)
 * @param int $perPage - Items per page
 * @return array ['items' => ..., 'totalPages' => ..., 'totalItems' => ..., 'currentPage' => ...]
 */
function paginateItems($items, $page = 1, $perPage = 12) {
    $totalItems = count($items);
    $totalPages = ceil($totalItems / $perPage);
    $page = max(1, min($page, $totalPages));
    
    $startIndex = ($page - 1) * $perPage;
    $paginatedItems = array_slice($items, $startIndex, $perPage);
    
    return [
        'items' => $paginatedItems,
        'totalPages' => $totalPages,
        'totalItems' => $totalItems,
        'currentPage' => $page,
        'perPage' => $perPage,
        'hasNextPage' => $page < $totalPages,
        'hasPrevPage' => $page > 1
    ];
}

/**
 * Sort items
 * @param array $items - Array of items to sort
 * @param string $sortBy - Field to sort by
 * @param string $order - 'asc' or 'desc'
 * @return array Sorted items
 */
function sortItems($items, $sortBy, $order = 'asc') {
    if (empty($sortBy)) {
        return $items;
    }
    
    usort($items, function($a, $b) use ($sortBy, $order) {
        $aVal = $a[$sortBy] ?? '';
        $bVal = $b[$sortBy] ?? '';
        
        if ($aVal == $bVal) return 0;
        
        $result = $aVal < $bVal ? -1 : 1;
        return $order === 'desc' ? -$result : $result;
    });
    
    return $items;
}
?>
