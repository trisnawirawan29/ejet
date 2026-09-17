<script>
document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById(@json($tableId));
    const search = document.getElementById(@json($searchId));
    const pageSize = document.getElementById(@json($pageSizeId));
    let footer = document.getElementById(@json($footerId));

    if (!footer && table) {
        footer = document.createElement('div');
        footer.id = @json($footerId);
        footer.className = 'd-flex justify-content-between align-items-center mt-3 small text-muted';
        table.parentElement.append(footer);
    }

    if (!table || !search || !pageSize || !footer) return;

    const rows = [...table.querySelectorAll('tbody tr[data-row]')];
    let page = 1;
    let sort = { key: '', direction: 'asc' };
    const render = () => {
        const query = search.value.toLowerCase().trim();
        const filtered = rows.filter(row => !query || row.dataset.row.includes(query));
        if (sort.key) filtered.sort((a, b) => (a.dataset[sort.key] || '').localeCompare(b.dataset[sort.key] || '', undefined, { numeric: true }) * (sort.direction === 'asc' ? 1 : -1));
        const size = Number(pageSize.value);
        const pages = Math.max(1, Math.ceil(filtered.length / size));
        page = Math.min(page, pages);
        rows.forEach(row => { row.hidden = true; });
        filtered.slice((page - 1) * size, page * size).forEach(row => { row.hidden = false; });
        footer.innerHTML = `<span>${filtered.length} data ditemukan</span><span class="btn-group"><button class="btn btn-sm btn-outline-secondary" ${page === 1 ? 'disabled' : ''} data-page="${page - 1}"><i class="bi bi-chevron-left"></i></button><span class="btn btn-sm btn-outline-secondary disabled">${page} / ${pages}</span><button class="btn btn-sm btn-outline-secondary" ${page === pages ? 'disabled' : ''} data-page="${page + 1}"><i class="bi bi-chevron-right"></i></button></span>`;
        footer.querySelectorAll('[data-page]').forEach(button => button.addEventListener('click', () => { page = Number(button.dataset.page); render(); }));
    };
    search.addEventListener('input', () => { page = 1; render(); });
    pageSize.addEventListener('change', () => { page = 1; render(); });
    table.querySelectorAll('th[data-sort]').forEach(header => header.addEventListener('click', () => { sort = { key: header.dataset.sort, direction: sort.key === header.dataset.sort && sort.direction === 'asc' ? 'desc' : 'asc' }; render(); }));
    render();
});
</script>
