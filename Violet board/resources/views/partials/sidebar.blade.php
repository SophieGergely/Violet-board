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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');

        // Measure natural width and lock it as explicit px value
        // so CSS transition from px -> 0 works correctly
        const w = sidebar.scrollWidth;
        sidebar.style.width = w + 'px';
        document.documentElement.style.setProperty('--sidebar-w', w + 'px');

        const toggle = document.getElementById('sidebarToggle');
        if (toggle) toggle.style.left = w + 'px';
    });
</script>
