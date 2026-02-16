<?php
class Paginator
{
    public int $total;
    public int $page;
    public int $perPage;
    public int $maxWindow;
    public string $basePath;   // ej: /l5M6n7O8Pq (sin cola variable)
    public ?string $anchor;    // ej: 'listado'
    public string $mode;       // 'segments' | 'query'
    public array $queryParams; // solo se usa en modo 'query'

    public function __construct(int $total, array $opts = [])
    {
        $this->total       = max(0, $total);
        $this->mode        = $opts['mode'] ?? 'query';
        $this->perPage     = $this->sanitizePerPage($opts['per_page'] ?? 10);
        $this->page        = $this->sanitizePage($opts['page'] ?? 1);
        $this->maxWindow   = (int)($opts['max_window'] ?? 2);

        $rawBase           = (string)($opts['base_path'] ?? '/');
        // normaliza base (/l5M6n7O8Pq) sin trailing slash
        $path              = parse_url($rawBase, PHP_URL_PATH) ?? $rawBase;
        $this->basePath    = rtrim($path, '/');
        $this->anchor      = isset($opts['anchor']) ? (string)$opts['anchor'] : null;

        // Solo para modo query
        $this->queryParams = ($this->mode === 'query')
            ? ($opts['query_params'] ?? [])
            : [];

        // clamp a total
        $tp = $this->getTotalPages();
        if ($tp > 0) { $this->page = min(max(1, $this->page), $tp); }
        else { $this->page = 1; }
    }

    public function getTotalPages(): int { return ($this->perPage > 0) ? (int)ceil($this->total / $this->perPage) : 0; }
    public function getOffset(): int { return max(0, ($this->page - 1) * $this->perPage); }
    public function getLimit(): int { return $this->perPage; }

    public function summary(string $label = 'Mostrando {from}–{to} de {total}'): string
    {
        if ($this->total === 0) return '<div class="text-muted small">Sin resultados</div>';
        $from = $this->getOffset() + 1;
        $to   = min($this->total, $this->getOffset() + $this->getLimit());
        $html = strtr($label, [
            '{from}'  => number_format($from, 0, ',', '.'),
            '{to}'    => number_format($to, 0, ',', '.'),
            '{total}' => number_format($this->total, 0, ',', '.'),
        ]);
        return '<div class="text-muted small">'.$html.'</div>';
    }

    public function render(?string $size = 'sm'): string
    {
        $tp = $this->getTotalPages();
        if ($tp <= 1) return '';

        $pages  = $this->buildWindowPages($tp);
        $ulClass = 'pagination'.($size ? ' pagination-'.$size : '');
        $h = '<nav aria-label="Paginación"><ul class="'.$ulClass.' mb-0">';

        $h .= $this->pageItem(1, '«', 'Primera', $this->page <= 1, true);
        $h .= $this->pageItem(max(1, $this->page - 1), '‹', 'Anterior', $this->page <= 1);

        foreach ($pages as $p) {
            if ($p === '...') $h .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
            else $h .= $this->pageItem($p, (string)$p, 'Página '.$p, false, false, $p === $this->page);
        }

        $h .= $this->pageItem(min($tp, $this->page + 1), '›', 'Siguiente', $this->page >= $tp);
        $h .= $this->pageItem($tp, '»', 'Última', $this->page >= $tp, true);

        $h .= '</ul></nav>';
        return $h;
    }

    // Selector “por página” compatible con modo SEGMENTS (sin query-string)
    public function renderPageSizeSelect(array $options = [10,20,50,100], string $label = 'Por página'): string
    {
        if ($this->mode === 'query') {
            // versión query-string (clásica)
            $f = '<form method="get" class="d-inline-flex align-items-center gap-2">';
            $f .= '<label class="form-label mb-0 small">'.$label.':</label>';
            $f .= '<select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">';
            foreach ($options as $opt) {
                $sel = ((int)$opt === (int)$this->perPage) ? ' selected' : '';
                $f .= '<option value="'.$opt.'"'.$sel.'>'.$opt.'</option>';
            }
            $f .= '</select>';
            foreach ($this->queryParams as $k => $v) {
                if ($k === 'per_page' || $k === 'page') continue;
                if (is_array($v)) foreach ($v as $vv) $f .= '<input type="hidden" name="'.htmlspecialchars($k).'[]" value="'.htmlspecialchars($vv).'">';
                else $f .= '<input type="hidden" name="'.htmlspecialchars($k).'" value="'.htmlspecialchars((string)$v).'">';
            }
            $f .= '</form>';
            return $f;
        }

        // versión segments: cambiamos la URL con JS (sin formularios GET)
        $id = 'pps_'.substr(md5($this->basePath.microtime(true)), 0, 8);
        $h  = '<div class="d-inline-flex align-items-center gap-2">';
        $h .= '<label class="form-label mb-0 small">'.$label.':</label>';
        $h .= '<select id="'.$id.'" class="form-select form-select-sm">';
        foreach ($options as $opt) {
            $sel = ((int)$opt === (int)$this->perPage) ? ' selected' : '';
            $h .= '<option value="'.$opt.'"'.$sel.'>'.$opt.'</option>';
        }
        $h .= '</select></div>';
        $goto1 = htmlspecialchars($this->buildHref(1, '{PP}'));
        $anchor = $this->anchor ? '#'.rawurlencode($this->anchor) : '';
        $h .= "<script>document.getElementById('$id').addEventListener('change',function(){var pp=this.value;var url='$goto1'.replace('{PP}',pp); if('$anchor'){url+= '$anchor';} window.location=url;});</script>";
        return $h;
    }

    // ===== internos =====
    protected function pageItem(int $page, string $text, string $aria, bool $disabled=false, bool $edge=false, bool $active=false): string
    {
        $li = 'page-item'.($disabled?' disabled':'').($active?' active':'');
        $href = ($disabled||$active) ? '#' : $this->buildHref($page, $this->perPage);
        $ariaAttr = ' aria-label="'.htmlspecialchars($aria).'"';
        $edgeAttr = $edge ? ' data-edge="1"' : '';
        return '<li class="'.$li.'"><a class="page-link" href="'.$href.'"'.$ariaAttr.$edgeAttr.'>'.$text.'</a></li>';
    }

    protected function buildWindowPages(int $tp): array
    {
        if ($tp <= (2 + 2*$this->maxWindow)) {
            $all = [];
            for ($i=1;$i<=$tp;$i++) $all[]=$i;
            return $all;
        }
        $pages = [1];
        $start = max(2, $this->page - $this->maxWindow);
        $end   = min($tp - 1, $this->page + $this->maxWindow);
        if ($start > 2) $pages[] = '...';
        for ($i=$start;$i<=$end;$i++) $pages[] = $i;
        if ($end < $tp - 1) $pages[] = '...';
        $pages[] = $tp;
        return $pages;
    }

    protected function buildHref(int $page, $perPage): string
    {
        if ($this->mode === 'segments') {
            // /mask/{page}/{perPage}
            $href = $this->basePath . '/' . max(1,$page) . '/' . max(1,(int)$perPage);
            return $href;
        }
        // query-string
        $params = $this->queryParams;
        $params['page'] = $page;
        $params['per_page'] = (int)$perPage;
        $qs = http_build_query($params);
        $href = $this->basePath . ($qs?('?'.$qs):'');
        return $href;
    }

    protected function sanitizePerPage($pp): int { $pp=(int)$pp; if ($pp<=0) $pp=20; if ($pp>500) $pp=500; return $pp; }
    protected function sanitizePage($p): int { $p=(int)$p; return $p<=0?1:$p; }
}
