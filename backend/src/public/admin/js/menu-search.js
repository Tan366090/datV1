document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.menu-search input');
    const menuItems = document.querySelectorAll('.nav-item');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            menuItems.forEach(item => {
                const link = item.querySelector('.nav-link');
                const text = link.textContent.toLowerCase();
                
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
}); 