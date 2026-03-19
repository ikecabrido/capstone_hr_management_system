/**
 * Card-Based Filtering System
 * Reusable JavaScript for card layouts with search, filter, and sort functionality
 */

class CardFilter {
    constructor(options) {
        this.containerId = options.containerId || 'cardsContainer';
        this.searchInputId = options.searchInputId || 'searchInput';
        this.filterSelectId = options.filterSelectId || 'filterSelect';
        this.sortSelectId = options.sortSelectId || 'sortSelect';
        this.emptyStateId = options.emptyStateId || 'emptyState';
        this.cardClass = options.cardClass || 'filter-card';
        this.searchFields = options.searchFields || ['title', 'description'];
        this.filterField = options.filterField || 'status';
        this.sortField = options.sortField || 'date';
        
        this.init();
    }
    
    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.bindEvents());
        } else {
            this.bindEvents();
        }
    }
    
    bindEvents() {
        const searchInput = document.getElementById(this.searchInputId);
        const filterSelect = document.getElementById(this.filterSelectId);
        const sortSelect = document.getElementById(this.sortSelectId);
        
        if (searchInput) {
            searchInput.addEventListener('input', () => this.filterCards());
        }
        
        if (filterSelect) {
            filterSelect.addEventListener('change', () => this.filterCards());
        }
        
        if (sortSelect) {
            sortSelect.addEventListener('change', () => this.sortCards());
        }
    }
    
    getCards() {
        const container = document.getElementById(this.containerId);
        if (!container) return [];
        return Array.from(container.querySelectorAll('.' + this.cardClass));
    }
    
    getSearchTerm() {
        const searchInput = document.getElementById(this.searchInputId);
        return searchInput ? searchInput.value.toLowerCase().trim() : '';
    }
    
    getFilterValue() {
        const filterSelect = document.getElementById(this.filterSelectId);
        return filterSelect ? filterSelect.value : 'all';
    }
    
    getSortValue() {
        const sortSelect = document.getElementById(this.sortSelectId);
        return sortSelect ? sortSelect.value : 'newest';
    }
    
    getCardData(card) {
        const data = {};
        this.searchFields.forEach(field => {
            const element = card.querySelector(`[data-${field}]`);
            if (element) {
                data[field] = element.getAttribute(`data-${field}`).toLowerCase();
            }
            // Also check the text content
            const textElement = card.querySelector(`.card-${field}`);
            if (textElement) {
                data[field] = textElement.textContent.toLowerCase();
            }
        });
        
        // Get filter field
        const filterElement = card.querySelector(`[data-${this.filterField}]`);
        if (filterElement) {
            data[this.filterField] = filterElement.getAttribute(`data-${this.filterField}`).toLowerCase();
        }
        
        // Get sort field (usually date)
        const sortElement = card.querySelector(`[data-${this.sortField}]`);
        if (sortElement) {
            data[this.sortField] = sortElement.getAttribute(`data-${this.sortField}`);
        }
        
        return data;
    }
    
    matchesSearch(card) {
        const searchTerm = this.getSearchTerm();
        if (!searchTerm) return true;
        
        const data = this.getCardData(card);
        
        return this.searchFields.some(field => {
            return data[field] && data[field].includes(searchTerm);
        });
    }
    
    matchesFilter(card) {
        const filterValue = this.getFilterValue();
        if (filterValue === 'all') return true;
        
        const data = this.getCardData(card);
        const cardFilterValue = data[this.filterField];
        
        if (!cardFilterValue) return true;
        
        return cardFilterValue === filterValue.toLowerCase();
    }
    
    filterCards() {
        const cards = this.getCards();
        let visibleCount = 0;
        
        cards.forEach(card => {
            const matchesSearch = this.matchesSearch(card);
            const matchesFilter = this.matchesFilter(card);
            
            if (matchesSearch && matchesFilter) {
                card.style.display = '';
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.style.display = 'none';
                card.classList.add('hidden');
            }
        });
        
        this.updateEmptyState(visibleCount);
        this.sortCards();
    }
    
    sortCards() {
        const cards = this.getCards();
        const sortValue = this.getSortValue();
        
        const container = document.getElementById(this.containerId);
        if (!container) return;
        
        // Sort the array
        const sortedCards = cards.sort((a, b) => {
            const dataA = this.getCardData(a);
            const dataB = this.getCardData(b);
            
            switch (sortValue) {
                case 'newest':
                    const dateA = new Date(dataA[this.sortField] || 0);
                    const dateB = new Date(dataB[this.sortField] || 0);
                    return dateB - dateA;
                case 'oldest':
                    const dateOldA = new Date(dataA[this.sortField] || 0);
                    const dateOldB = new Date(dataB[this.sortField] || 0);
                    return dateOldA - dateOldB;
                case 'az':
                    const titleA = dataA.title || '';
                    const titleB = dataB.title || '';
                    return titleA.localeCompare(titleB);
                case 'za':
                    const titleZA = dataA.title || '';
                    const titleZB = dataB.title || '';
                    return titleZB.localeCompare(titleZA);
                default:
                    return 0;
            }
        });
        
        // Re-append in sorted order
        sortedCards.forEach(card => {
            container.appendChild(card);
        });
    }
    
    updateEmptyState(visibleCount) {
        const emptyState = document.getElementById(this.emptyStateId);
        if (!emptyState) return;
        
        if (visibleCount === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }
    
    // Public method to refresh after dynamic content changes
    refresh() {
        this.filterCards();
    }
}

/**
 * Initialize card filter for a specific page
 */
function initCardFilter(options) {
    return new CardFilter(options);
}

/**
 * Create card HTML template
 */
function createCardHtml(data) {
    const {
        id,
        title,
        description,
        status,
        statusLabel,
        icon,
        date,
        details = []
    } = data;
    
    const statusClass = getStatusClass(status);
    
    let detailsHtml = details.map(d => `
        <div class="card-detail-item">
            <span class="detail-label">${d.label}:</span>
            <span class="detail-value">${d.value}</span>
        </div>
    `).join('');
    
    return `
        <div class="card filter-card" data-id="${id}" data-status="${status}" data-date="${date}" data-title="${title}">
            <div class="card-body">
                <div class="card-header-row">
                    <div class="card-icon">
                        <i class="${icon}"></i>
                    </div>
                    <span class="badge badge-${statusClass}">${statusLabel}</span>
                </div>
                <h5 class="card-title">${title}</h5>
                <p class="card-description">${description}</p>
                <div class="card-details">
                    ${detailsHtml}
                </div>
                <div class="card-meta">
                    <span class="card-date"><i class="far fa-calendar-alt"></i> ${date}</span>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-primary view-details-btn" data-id="${id}">
                    <i class="fas fa-eye"></i> View Details
                </button>
            </div>
        </div>
    `;
}

/**
 * Get status class based on status value
 */
function getStatusClass(status) {
    const statusMap = {
        'approved': 'success',
        'compliant': 'success',
        'active': 'success',
        'completed': 'success',
        'resolved': 'success',
        'pending': 'warning',
        'at risk': 'warning',
        'at_risk': 'warning',
        'processing': 'warning',
        'rejected': 'danger',
        'non-compliant': 'danger',
        'non_compliant': 'danger',
        'inactive': 'secondary',
        'cancelled': 'secondary',
        'expired': 'secondary'
    };
    
    return statusMap[status] || 'secondary';
}

/**
 * Show card details in modal
 */
function showCardDetails(data, modalId = 'detailsModal') {
    let modal = document.getElementById(modalId);
    
    if (!modal) {
        // Create modal if it doesn't exist
        modal = createDetailsModal(modalId);
        document.body.appendChild(modal.querySelector('.modal').parentNode);
    }
    
    // Populate modal with data
    const modalBody = modal.querySelector('.modal-body');
    if (modalBody) {
        modalBody.innerHTML = `
            <div class="details-content">
                <div class="details-header">
                    <div class="details-icon">
                        <i class="${data.icon}"></i>
                    </div>
                    <div class="details-title">
                        <h4>${data.title}</h4>
                        <span class="badge badge-${getStatusClass(data.status)}">${data.statusLabel}</span>
                    </div>
                </div>
                <div class="details-body">
                    <p class="details-description">${data.description}</p>
                    <hr>
                    <h6>Complete Details</h6>
                    <div class="details-list">
                        ${data.details.map(d => `
                            <div class="detail-row">
                                <span class="detail-label">${d.label}</span>
                                <span class="detail-value">${d.value}</span>
                            </div>
                        `).join('')}
                    </div>
                    ${data.documents ? `
                        <hr>
                        <h6>Attached Documents</h6>
                        <div class="details-documents">
                            ${data.documents.map(doc => `
                                <a href="${doc.path}" target="_blank" class="btn btn-sm btn-outline-primary mr-2 mb-2">
                                    <i class="fas fa-file"></i> ${doc.name}
                                </a>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }
    
    // Show modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

/**
 * Create details modal template
 */
function createDetailsModal(modalId) {
    const div = document.createElement('div');
    div.innerHTML = `
        <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    return div;
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { CardFilter, initCardFilter, createCardHtml, showCardDetails, getStatusClass };
}
