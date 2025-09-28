<?php
require_once 'config/config.php';

$page_title = 'Frequently Asked Questions';
$page_description = 'Find answers to common questions about our products and services.';
$body_class = 'faq-page';

// Get FAQs from database
try {
    $db = getDB();
    $stmt = $db->query("
        SELECT * FROM faqs 
        WHERE is_active = 1 
        ORDER BY sort_order ASC, id ASC
    ");
    $faqs = $stmt->fetchAll();
    
    // Group FAQs by category
    $faq_categories = [];
    foreach ($faqs as $faq) {
        $category = $faq['category'] ?: 'General';
        if (!isset($faq_categories[$category])) {
            $faq_categories[$category] = [];
        }
        $faq_categories[$category][] = $faq;
    }
    
} catch (Exception $e) {
    error_log("FAQ page error: " . $e->getMessage());
    $faq_categories = [];
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item active">FAQ</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold mb-3">Frequently Asked Questions</h1>
        <p class="lead text-muted">
            Find quick answers to the most common questions about our products and services.
        </p>
    </div>
    
    <?php if (!empty($faq_categories)): ?>
        <div class="row">
            <!-- FAQ Categories Navigation -->
            <div class="col-lg-3">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list me-2"></i>
                            Categories
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($faq_categories as $category => $category_faqs): ?>
                                <a href="#category-<?php echo strtolower(str_replace(' ', '-', $category)); ?>" 
                                   class="list-group-item list-group-item-action">
                                    <i class="bi bi-chevron-right me-2"></i>
                                    <?php echo htmlspecialchars($category); ?>
                                    <span class="badge bg-primary rounded-pill float-end">
                                        <?php echo count($category_faqs); ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Content -->
            <div class="col-lg-9">
                <?php foreach ($faq_categories as $category => $category_faqs): ?>
                    <div class="mb-5" id="category-<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                        <h2 class="fw-bold mb-4 text-primary">
                            <i class="bi bi-folder me-2"></i>
                            <?php echo htmlspecialchars($category); ?>
                        </h2>
                        
                        <div class="accordion" id="accordion-<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                            <?php foreach ($category_faqs as $index => $faq): ?>
                                <div class="accordion-item">
                                    <h3 class="accordion-header" id="heading-<?php echo $faq['id']; ?>">
                                        <button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse-<?php echo $faq['id']; ?>" 
                                                aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                                aria-controls="collapse-<?php echo $faq['id']; ?>">
                                            <?php echo htmlspecialchars($faq['question']); ?>
                                        </button>
                                    </h3>
                                    <div id="collapse-<?php echo $faq['id']; ?>" 
                                         class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                                         aria-labelledby="heading-<?php echo $faq['id']; ?>" 
                                         data-bs-parent="#accordion-<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                                        <div class="accordion-body">
                                            <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Still Need Help Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center py-5">
                        <h3 class="fw-bold mb-3">Still need help?</h3>
                        <p class="lead mb-4">
                            Can't find the answer you're looking for? Our support team is here to help!
                        </p>
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="<?php echo SITE_URL; ?>/contact" class="btn btn-light btn-lg">
                                <i class="bi bi-envelope me-2"></i>
                                Contact Support
                            </a>
                            <a href="<?php echo SITE_URL; ?>/support" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-headset me-2"></i>
                                Help Center
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <!-- No FAQs -->
        <div class="text-center py-5">
            <i class="bi bi-question-circle display-1 text-muted mb-4"></i>
            <h3 class="text-muted mb-3">No FAQs available</h3>
            <p class="text-muted mb-4">
                We're working on adding frequently asked questions. In the meantime, feel free to contact us directly.
            </p>
            <a href="<?php echo SITE_URL; ?>/contact" class="btn btn-primary btn-lg">
                <i class="bi bi-envelope me-2"></i>
                Contact Us
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Search FAQ Modal -->
<div class="modal fade" id="searchFaqModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="faqSearchInput" placeholder="Search for answers...">
                    <button class="btn btn-primary" type="button" onclick="searchFAQs()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div id="searchResults"></div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Search Button -->
<button class="btn btn-primary btn-floating position-fixed bottom-0 end-0 m-4" 
        data-bs-toggle="modal" 
        data-bs-target="#searchFaqModal"
        title="Search FAQs">
    <i class="bi bi-search"></i>
</button>

<style>
.btn-floating {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
}

.accordion-button:not(.collapsed) {
    background-color: rgba(111, 66, 193, 0.1);
    color: #6f42c1;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
}

@media (max-width: 991.98px) {
    .sticky-top {
        position: static !important;
        margin-bottom: 2rem;
    }
}
</style>

<script>
// Smooth scrolling for category links
document.querySelectorAll('a[href^="#category-"]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// FAQ Search functionality
function searchFAQs() {
    const searchTerm = document.getElementById('faqSearchInput').value.toLowerCase();
    const resultsContainer = document.getElementById('searchResults');
    
    if (searchTerm.length < 2) {
        resultsContainer.innerHTML = '<p class="text-muted">Please enter at least 2 characters to search.</p>';
        return;
    }
    
    const allFAQs = document.querySelectorAll('.accordion-item');
    let results = [];
    
    allFAQs.forEach(item => {
        const question = item.querySelector('.accordion-button').textContent.toLowerCase();
        const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
        
        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
            results.push({
                question: item.querySelector('.accordion-button').textContent,
                answer: item.querySelector('.accordion-body').innerHTML,
                element: item
            });
        }
    });
    
    if (results.length === 0) {
        resultsContainer.innerHTML = '<p class="text-muted">No results found. Try different keywords.</p>';
    } else {
        let html = '<div class="search-results">';
        results.forEach((result, index) => {
            html += `
                <div class="card mb-2">
                    <div class="card-header">
                        <h6 class="mb-0">${result.question}</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${result.answer}</p>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        resultsContainer.innerHTML = html;
    }
}

// Search on Enter key
document.getElementById('faqSearchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchFAQs();
    }
});
</script>

<?php include 'includes/footer.php'; ?>
