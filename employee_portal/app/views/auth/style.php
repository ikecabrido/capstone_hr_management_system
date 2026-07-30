<style>
    /* =========================================
   FORM FIELD
========================================= */

    .form-field {
        width: 100%;
        margin-bottom: 18px;
    }

    .form-field label {
        display: block;
        margin-bottom: 7px;

        font-size: 14px;
        font-weight: 600;
        color: #343a40;
    }


    /* =========================================
   INPUT GROUP
========================================= */

    .custom-input-group {
        position: relative;

        display: flex;
        align-items: center;

        width: 100%;
        height: 48px;

        background: #ffffff;
        border: 1px solid #d8dde3;
        border-radius: 9px;

        overflow: hidden;

        transition: border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    /* Focus entire input */
    .custom-input-group:focus-within {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.10);
    }


    /* =========================================
   LEFT ICON
========================================= */

    .input-icon {
        width: 48px;
        min-width: 48px;
        height: 100%;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #6c757d;
        font-size: 15px;

        pointer-events: none;
    }

    .input-icon i {
        display: block;
        line-height: 1;
    }


    /* =========================================
   TEXT INPUT
========================================= */

    .custom-input-group input {
        flex: 1;

        min-width: 0;
        width: 100%;
        height: 100%;

        padding: 0 10px 0 0;

        border: none;
        outline: none;

        background: transparent;

        font-size: 14px;
        color: #343a40;
    }

    .custom-input-group input::placeholder {
        color: #adb5bd;
        font-size: 13px;
    }


    /* =========================================
   PASSWORD EYE BUTTON
========================================= */

    .password-toggle {
        flex: 0 0 48px;

        width: 48px;
        height: 48px;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 0;
        margin: 0;

        border: none;
        outline: none;

        background: transparent;

        color: #6c757d;

        cursor: pointer;

        transition: color 0.2s ease,
            background-color 0.2s ease;
    }

    .password-toggle:hover {
        color: #007bff;
        background-color: #f8f9fa;
    }

    .password-toggle:focus {
        outline: none;
        box-shadow: none;
    }

    .password-toggle i {
        display: block;

        margin: 0;
        padding: 0;

        font-size: 15px;
        line-height: 1;
    }


    /* =========================================
   LOGIN BUTTON
========================================= */

    .login-btn {
        width: 100%;
        height: 48px;

        margin-top: 5px;

        border: none;
        border-radius: 9px;

        background: #007bff;
        color: #ffffff;

        font-size: 14px;
        font-weight: 600;

        cursor: pointer;

        transition: all 0.2s ease;
    }

    .login-btn:hover {
        background: #0069d9;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.18);
    }

    .login-btn:active {
        transform: translateY(1px);
    }


    .custom-input-group .input-icon+input {
        margin-left: 0;
    }

    .custom-input-group button {
        margin-bottom: 0;
    }

    .forgot-password-btn {
        border: none;
        background: transparent;
        color: #64748b;
        /* Slate */
        font-size: 13px;
        cursor: pointer;
    }

    .forgot-password-btn span {
        color: #64748b;
        font-weight: 600;
    }

    .forgot-password-btn:hover {
        color: #475569;
    }

    .forgot-password-btn:hover span {
        color: #475569;
    }
</style>