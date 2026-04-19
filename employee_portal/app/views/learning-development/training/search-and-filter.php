<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">

            <div class="col-md-10">
                <form id="ajaxSearchForm" method="GET" action="index.php">
                    <input type="hidden" name="url" value="training-program-index">

                    <div class="row g-3 align-items-end">
                        <div class="col-md-9">
                            <input type="text" name="search" class="form-control" placeholder="Search trainings..." 
                                value="<?php echo htmlspecialchars($searchQuery); ?>">
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-secondary w-100" onclick="clearFilters()">
                    Clear
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    function clearFilters() {
        const searchInput = document.querySelector('input[name="search"]');
        const statusSelect = document.querySelector('select[name="status"]');
        const container = document.querySelector('#program-container');

        if (searchInput) searchInput.value = '';
        if (statusSelect) statusSelect.value = '';

        fetch("index.php?url=training-program-index")
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newPrograms = doc.querySelector('#program-container');

                if (newPrograms && container) {
                    container.innerHTML = newPrograms.innerHTML;
                }

                window.history.replaceState({}, '', 'index.php?url=training-program-index');
            })
            .catch(err => console.error('Clear error:', err));
    }

    function initAjaxSearch() {
        const form = document.getElementById('ajaxSearchForm');
        const container = document.querySelector('#program-container');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();

            container.innerHTML = "<div class='text-center py-5'>Loading...</div>";

            fetch("index.php?" + params)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newContent = doc.querySelector('#program-container');

                    if (newContent) {
                        container.innerHTML = newContent.innerHTML;

                        window.history.pushState({}, '', 'index.php?' + params);
                    }
                })
                .catch(err => {
                    console.error('Search error:', err);
                });
        });
    }

    document.addEventListener('DOMContentLoaded', initAjaxSearch);
</script>