<aside class="sidebar" id="sidebar">
    <div class="d-flex flex-column">
        <button onclick="location.href='{{ url('/shop/vedomostne') }}'" class="category-button">Vedomostné hry</button>
        <button onclick="location.href='{{ url('/shop/karty') }}'" class="category-button">Kartové hry</button>
        <button onclick="location.href='{{ url('/shop/party') }}'" class="category-button">Party hry</button>
        <button onclick="location.href='{{ url('/shop/rodinne') }}'" class="category-button">Rodinné hry</button>
        <button onclick="location.href='{{ url('/shop/deti') }}'" class="category-button">Pre deti</button>
        <button onclick="location.href='{{ url('/shop/strategia') }}'" class="category-button">Štrategické hry</button>
        <button onclick="location.href='{{ url('/shop/puzzle') }}'" class="category-button">Puzzle</button>
        <button onclick="location.href='{{ url('/shop/pamat') }}'" class="category-button">Pamäťové hry</button>
    </div>
</aside>

<button class="sidebar-toggle" id="sidebarToggle" title="Toggle sidebar">&#9664;</button>

<script>
    const sidebar       = document.getElementById('sidebar');
    const toggle        = document.getElementById('sidebarToggle');
    const mainContent   = document.querySelector('.main-content');

    toggle.addEventListener('click', () => {
        const collapsed = sidebar.classList.toggle('collapsed');
        toggle.classList.toggle('collapsed', collapsed);
        toggle.innerHTML = collapsed ? '&#9654;' : '&#9664;';
        if (mainContent) mainContent.classList.toggle('expanded', collapsed);
    });
</script>
