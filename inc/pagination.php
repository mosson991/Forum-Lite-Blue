<?php

class Pagination {
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $urlPattern;

    public function __construct($totalItems, $itemsPerPage, $currentPage, $urlPattern) {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = $currentPage;
        $this->urlPattern = $urlPattern;
    }

    public function getTotalPages() {
        return ceil($this->totalItems / $this->itemsPerPage);
    }

    public function getOffset() {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    public function getLinks($numLinks = 5) {
        $totalPages = $this->getTotalPages();
        $links = [];

        if ($totalPages <= 1) {
            return $links;
        }

        $links['first'] = $this->getPageUrl(1);
        $links['last'] = $this->getPageUrl($totalPages);

        if ($this->currentPage > 1) {
            $links['prev'] = $this->getPageUrl($this->currentPage - 1);
        }

        if ($this->currentPage < $totalPages) {
            $links['next'] = $this->getPageUrl($this->currentPage + 1);
        }

        $startPage = max(1, $this->currentPage - floor($numLinks / 2));
        $endPage = min($totalPages, $startPage + $numLinks - 1);

        for ($i = $startPage; $i <= $endPage; $i++) {
            $links[$i] = $this->getPageUrl($i);
        }

        return $links;
    }

    private function getPageUrl($page) {
        return str_replace('{page}', $page, $this->urlPattern);
    }
}
