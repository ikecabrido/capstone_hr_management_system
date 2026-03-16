<section class="min-vh-100 d-flex align-items-center justify-content-center bg-gray-100 px-4">
    <div class="bg-white p-5 rounded-3 shadow-lg text-center animate-fade" style="max-width: 450px; width: 100%;">
        <h1 class="text-danger mb-3" style="font-size: 2rem; font-weight: 700;">Session Expired</h1>
        <p class="text-muted mb-4" style="font-size: 1rem;">
            <?= $message ?? "Your session expired. Please go back and try again." ?>
        </p>
        <a href="<?= $_SERVER['HTTP_REFERER'] ?? 'index.php?url=home' ?>"
           class="btn btn-primary px-4 py-2 fw-semibold animate-pulse"
           style="border-radius: 8px; transition: 0.3s;">
            Go Back
        </a>
    </div>
</section>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f3f4f6;
}

.btn-primary {
    background-color: #2563eb;
    border: none;
    color: #fff;
}
.btn-primary:hover {
    background-color: #1d4ed8;
    color: #fff;
}

@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(-10px); }
    100% { opacity: 1; transform: translateY(0); }
}

.animate-fade {
    animation: fadeIn 0.8s ease forwards;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.animate-pulse {
    animation: pulse 1.5s infinite;
}
</style>