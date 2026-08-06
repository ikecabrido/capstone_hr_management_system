<?php
/**
 * Form Protection Utility
 * 
 * Prevents duplicate form submissions using multiple strategies:
 * 1. Session-based token tracking
 * 2. POST-Redirect-GET (PRG) pattern
 * 3. Submission state management
 * 4. Data deduplication using content hash
 */

class FormProtection {
    
    /**
     * Session key prefix for form tokens
     */
    const TOKEN_PREFIX = 'form_token_';
    
    /**
     * Session key prefix for submission tracking
     */
    const SUBMISSION_PREFIX = 'form_submission_';
    
    /**
     * Session key for PRG redirect data
     */
    const PRG_DATA_KEY = 'prg_data';
    
    /**
     * Default token lifetime (in seconds)
     */
    const DEFAULT_TOKEN_LIFETIME = 3600;
    
    /**
     * Initialize session if not already started
     */
    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Generate a unique form token
     * 
     * @param string $formId Unique identifier for the form
     * @param int $lifetime Token lifetime in seconds
     * @return string Generated token
     */
    public static function generateToken($formId, $lifetime = self::DEFAULT_TOKEN_LIFETIME) {
        self::initSession();
        
        // Generate cryptographically secure token
        $token = bin2hex(random_bytes(32));
        $expiry = time() + $lifetime;
        
        // Store token in session
        $_SESSION[self::TOKEN_PREFIX . $formId] = [
            'token' => $token,
            'expiry' => $expiry,
            'created' => time()
        ];
        
        return $token;
    }
    
    /**
     * Validate form token
     * 
     * @param string $formId Unique identifier for the form
     * @param string $token Token to validate
     * @param bool $consume If true, invalidate token after validation
     * @return bool True if token is valid
     */
    public static function validateToken($formId, $token, $consume = true) {
        self::initSession();
        
        $storedToken = $_SESSION[self::TOKEN_PREFIX . $formId] ?? null;
        
        // Check if token exists and is not expired
        if (!$storedToken) {
            return false;
        }
        
        if (time() > $storedToken['expiry']) {
            unset($_SESSION[self::TOKEN_PREFIX . $formId]);
            return false;
        }
        
        // Validate token using timing-safe comparison
        if (!hash_equals($storedToken['token'], $token)) {
            return false;
        }
        
        // Consume token if requested
        if ($consume) {
            unset($_SESSION[self::TOKEN_PREFIX . $formId]);
        }
        
        return true;
    }
    
    /**
     * Get form token for embedding in form
     * 
     * @param string $formId Unique identifier for the form
     * @return string Generated token
     */
    public static function getToken($formId) {
        return self::generateToken($formId);
    }
    
    /**
     * Check if form was already submitted (based on token)
     * 
     * @param string $formId Unique identifier for the form
     * @return bool True if form was already submitted
     */
    public static function isFormSubmitted($formId) {
        self::initSession();
        
        $storedToken = $_SESSION[self::TOKEN_PREFIX . $formId] ?? null;
        
        // If no token exists, form hasn't been submitted or token was consumed
        if (!$storedToken) {
            return false;
        }
        
        // Check if token is expired
        if (time() > $storedToken['expiry']) {
            unset($_SESSION[self::TOKEN_PREFIX . $formId]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Track form submission by content hash
     * Useful for detecting duplicate data submissions
     * 
     * @param string $formId Unique identifier for the form
     * @param array $data Form data to create hash from
     * @param int $lifetime How long to track this submission (seconds)
     * @return string Content hash
     */
    public static function trackSubmission($formId, $data, $lifetime = 3600) {
        self::initSession();
        
        // Create content hash from serialized data
        $hash = hash('sha256', serialize($data));
        
        $_SESSION[self::SUBMISSION_PREFIX . $formId] = [
            'hash' => $hash,
            'data' => $data,
            'expiry' => time() + $lifetime,
            'submitted_at' => time()
        ];
        
        return $hash;
    }
    
    /**
     * Check if identical submission already exists
     * 
     * @param string $formId Unique identifier for the form
     * @param array $data Form data to check
     * @return bool True if duplicate submission detected
     */
    public static function isDuplicateSubmission($formId, $data) {
        self::initSession();
        
        $submission = $_SESSION[self::SUBMISSION_PREFIX . $formId] ?? null;
        
        if (!$submission) {
            return false;
        }
        
        // Check if submission is expired
        if (time() > $submission['expiry']) {
            unset($_SESSION[self::SUBMISSION_PREFIX . $formId]);
            return false;
        }
        
        // Compare content hashes
        $newHash = hash('sha256', serialize($data));
        
        return $newHash === $submission['hash'];
    }
    
    /**
     * Get previous submission data (if exists)
     * 
     * @param string $formId Unique identifier for the form
     * @return array|null Previous submission data or null
     */
    public static function getPreviousSubmission($formId) {
        self::initSession();
        
        $submission = $_SESSION[self::SUBMISSION_PREFIX . $formId] ?? null;
        
        if (!$submission || time() > $submission['expiry']) {
            return null;
        }
        
        return $submission['data'];
    }
    
    /**
     * Clear form submission tracking
     * 
     * @param string $formId Unique identifier for the form
     */
    public static function clearSubmission($formId) {
        self::initSession();
        unset($_SESSION[self::SUBMISSION_PREFIX . $formId]);
        unset($_SESSION[self::TOKEN_PREFIX . $formId]);
    }
    
    /**
     * Store data for PRG redirect
     * 
     * @param string $key Data key
     * @param mixed $value Data to store
     */
    public static function setPRGData($key, $value) {
        self::initSession();
        
        if (!isset($_SESSION[self::PRG_DATA_KEY])) {
            $_SESSION[self::PRG_DATA_KEY] = [];
        }
        
        $_SESSION[self::PRG_DATA_KEY][$key] = $value;
    }
    
    /**
     * Get PRG redirect data
     * 
     * @param string|null $key Specific key to retrieve, or null for all data
     * @return mixed Stored data or null
     */
    public static function getPRGData($key = null) {
        self::initSession();
        
        if (!isset($_SESSION[self::PRG_DATA_KEY])) {
            return null;
        }
        
        if ($key === null) {
            return $_SESSION[self::PRG_DATA_KEY];
        }
        
        return $_SESSION[self::PRG_DATA_KEY][$key] ?? null;
    }
    
    /**
     * Clear PRG data
     * 
     * @param string|null $key Specific key to clear, or null to clear all
     */
    public static function clearPRGData($key = null) {
        self::initSession();
        
        if ($key === null) {
            unset($_SESSION[self::PRG_DATA_KEY]);
        } else {
            unset($_SESSION[self::PRG_DATA_KEY][$key]);
        }
    }
    
    /**
     * Perform POST-Redirect-GET
     * Should be called after successful form processing
     * 
     * @param string $redirectUrl URL to redirect to
     * @param array $data Optional data to pass via session
     * @param string $messageKey Optional key for success/error message
     * @param string $message Optional message
     */
    public static function redirectAfterSubmit($redirectUrl, $data = [], $messageKey = null, $message = null) {
        self::initSession();
        
        // Store any data for the redirect
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                self::setPRGData($key, $value);
            }
        }
        
        // Store message if provided
        if ($messageKey && $message) {
            self::setPRGData($messageKey, $message);
        }
        
        // Perform redirect
        header("Location: " . $redirectUrl);
        exit;
    }
    
    /**
     * Check and clear one-time PRG messages
     * Call this on page load to handle messages from redirect
     * 
     * @param string $messageKey Message key to retrieve and clear
     * @return string|null Message if exists
     */
    public static function consumePRGMessage($messageKey) {
        $message = self::getPRGData($messageKey);
        
        if ($message !== null) {
            self::clearPRGData($messageKey);
        }
        
        return $message;
    }
    
    /**
     * Generate hidden form fields for form protection
     * 
     * @param string $formId Unique identifier for the form
     * @return string HTML hidden input fields
     */
    public static function generateHiddenFields($formId) {
        $token = self::getToken($formId);
        
        return '<input type="hidden" name="form_protection_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Get JavaScript code to prevent double submission on client side
     * 
     * @return string JavaScript code
     */
    public static function getClientSideScript() {
        return <<<'JS'
<script>
(function() {
    'use strict';
    
    // Track forms that are being submitted
    var submittingForms = new WeakSet();
    
    // Check if page was loaded via back/forward navigation
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // Page was loaded from cache (back button)
            // Re-enable all disabled submit buttons
            document.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function(btn) {
                btn.disabled = false;
                btn.dataset.wasDisabled = 'true';
            });
        }
    });
    
    // Prevent form double submission
    document.addEventListener('submit', function(event) {
        var form = event.target;
        
        // Skip if not a POST form or already submitting
        if (submittingForms.has(form)) {
            event.preventDefault();
            return false;
        }
        
        // Mark form as submitting
        submittingForms.add(form);
        
        // Disable submit buttons to prevent double click
        var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitButtons.forEach(function(btn) {
            btn.disabled = true;
            btn.dataset.originalText = btn.innerText || btn.value;
            btn.innerText = btn.innerText || btn.value;
        });
        
        // Store form data in sessionStorage for reload recovery
        if (window.sessionStorage) {
            var formData = new FormData(form);
            var data = {};
            formData.forEach(function(value, key) {
                data[key] = value;
            });
            sessionStorage.setItem('lastSubmittedForm_' + form.action, JSON.stringify({
                data: data,
                timestamp: Date.now()
            }));
        }
        
        // Re-enable buttons after a timeout as a fallback
        setTimeout(function() {
            submitButtons.forEach(function(btn) {
                btn.disabled = false;
            });
            submittingForms.delete(form);
        }, 10000); // 10 second timeout fallback
    });
    
    // Clear session storage on successful submission
    window.addEventListener('beforeunload', function() {
        // This doesn't actually do anything, just for reference
    });
})();
JS;
    }
}
