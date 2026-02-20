<?php

class Paginator
{
    public int $total;
    public int $page;
    public int $perPage;
    public string $basePath;

    public function __construct(int $total, array $opts = [])
    {
        $this->total = max(0, $total);
        $this->perPage = max(1, (int)($opts['per_page'] ?? 20));
        $this->page = max(1, (int)($opts['page'] ?? 1));
        $this->basePath = $opts['base_path'] ?? '';

        $totalPages = $this->getTotalPages();
        if ($totalPages > 0 && $this->page > $totalPages) {
            $this->page = $totalPages;
        }
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    public function getLimit(): int
    {
        return $this->perPage;
    }

    public function getTotalPages(): int
    {
        return (int)ceil($this->total / $this->perPage);
    }




    // ----------------------------------------------------------
    // 🔹 Render paginación mejorada
    // ----------------------------------------------------------
    public function render(): string
    {
        $totalPages = $this->getTotalPages();
        if ($totalPages <= 1) return '';

        $window = 2; // páginas a cada lado

        $start = max(1, $this->page - $window);
        $end   = min($totalPages, $this->page + $window);

        $html = '<nav><ul class="pagination justify-content-center">';

        // 🔹 Botón anterior
        if ($this->page > 1) {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="'
                . $this->basePath . '/' . ($this->page - 1) . '/' . $this->perPage
                . '">«</a>';
            $html .= '</li>';
        }

        // 🔹 Primera página + ...
        if ($start > 1) {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="'
                . $this->basePath . '/1/' . $this->perPage
                . '">1</a></li>';

            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // 🔹 Ventana central
        for ($i = $start; $i <= $end; $i++) {
            $active = $i === $this->page ? ' active' : '';

            $html .= '<li class="page-item' . $active . '">';
            $html .= '<a class="page-link" href="'
                . $this->basePath . '/' . $i . '/' . $this->perPage
                . '">' . $i . '</a>';
            $html .= '</li>';
        }

        // 🔹 Última página + ...
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="'
                . $this->basePath . '/' . $totalPages . '/' . $this->perPage
                . '">' . $totalPages . '</a>';
            $html .= '</li>';
        }

        // 🔹 Botón siguiente
        if ($this->page < $totalPages) {
            $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="'
                . $this->basePath . '/' . ($this->page + 1) . '/' . $this->perPage
                . '">»</a>';
            $html .= '</li>';
        }

        $html .= '</ul></nav>';

        return $html;
    }







    // ----------------------------------------------------------
    // 🔹 Resumen de resultados
    // ----------------------------------------------------------
    public function summary(): string
    {
        if ($this->total === 0) {
            return '<span class="text-muted small">Sin resultados</span>';
        }

        $from = $this->getOffset() + 1;
        $to   = min($this->total, $this->getOffset() + $this->getLimit());

        return '<span class="text-muted small">
        Mostrando ' . number_format($from, 0, ',', '.') .
            ' – ' . number_format($to, 0, ',', '.') .
            ' de ' . number_format($this->total, 0, ',', '.') .
            ' registros
    </span>';
    }
}
