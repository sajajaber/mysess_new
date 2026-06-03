<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    a {
        text-decoration: none;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #f4f7fa;
        color: #333;
    }

    .main {
        margin-left: 280px;
        padding: 20px;
        min-height: 100vh;
    }

    .stat-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .card {
        background: white;
        border-radius: 15px;
        box-shadow: #e2e8f0 0px 4px 6px;
        overflow: hidden;
        width: 100%;
        max-width: 100%;
    }

    .card-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h2 {
        font-size: 20px;
        margin: 0;
    }

    .card-header .btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .card-header .btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .card-body {
        padding: 25px;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f7fafc;
    }

    th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #4a5568;
        border-bottom: 2px solid #e2e8f0;
    }

    td {
        padding: 12px 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #4a5568;
    }

    tbody tr:hover {
        background: #f7fafc;
    }

    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        backdrop-filter: blur(3px);
    }

    .overlay.open {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal {
        background: white;
        border-radius: 15px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
    }

    /* modal animation, when it opens, it will slide in from the top and fade in */
    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        font-size: 20px;
        margin: 0;
    }

    .close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        transition: background 0.3s;
    }

    .close-btn:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .modal-body {
        padding: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #4a5568;
    }

    .form-group select,
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-group select:focus,
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .modal-body .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        margin-right: 10px;
        transition: transform 0.2s;
    }

    .modal-body .btn-primary:hover {
        transform: translateY(-2px);
    }

    .modal-body .btn {
        background: #e2e8f0;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.3s;
    }

    .modal-body .btn:hover {
        background: #cbd5e0;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s;
        }

        .main {
            margin-left: 0;
        }

        .topbar {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }

        .stat-cards {
            grid-template-columns: 1fr;
        }

        .card-header {
            flex-direction: column;
            gap: 10px;
        }

        table {
            font-size: 12px;
        }

        th,
        td {
            padding: 8px 10px;
        }
    }

    .text-center {
        text-align: center;
    }


    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }


    .topbar-right .btn-primary {
        background: transparent;
        color: #333;
        border: #f4f7fa solid 2px;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .topbar-right .btn-primary:hover {
        border: #2563eb solid 2px;
        color: #2563eb;
    }

    .sidebar-logo {
        color: #2563eb;
        padding-top: 40px;
        padding-bottom: 20px;
        text-align: center;
        font-size: 32px;
        font-weight: bold;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar a:hover {
        background: rgba(255, 255, 255, 0.1);
        font-weight: bold;
        border-left: 3px solid #2563eb;
        background-color: #cfdcee9f;
        color: rgb(21, 76, 148);
        transition-duration: 0.2s;
    }

    .stat-value {
        font-size: 48px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #2563eb;
    }

    .show-archived {
        background: #e5ebf7;
        color: #002d8d;
        border-color: #2563eb;
        border-radius: 6px;
        padding: 6px 12px;
        cursor: pointer;
        font-size: 14px;
        transition: transform 0.2s;
    }

    .filter-badge {
        color: red;
        font-size: 12px;
        font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        margin-left: 5px;
        font-weight: bold;
        font-style: italic;
    }

    .progress-bar-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 120px;
    }

    .progress-bar {
        height: 8px;
        background: linear-gradient(90deg, #3b82f6, #2563eb);
        border-radius: 4px;
        flex: 1;
        max-width: 80px;
        min-width: 4px;
    }

    .progress-label {
        font-size: 12px;
        font-weight: 600;
        color: #2563eb;
        white-space: nowrap;
    }

    .empty-state {
        text-align: center;
        color: #a0aec0;
        padding: 30px 0;
        font-size: 14px;
    }

    .admin-area .student-name,
    .admin-area a.student-name {
        color: #2563eb;
        font-weight: 600;
        text-decoration: none;
    }

    .admin-area .student-name:hover {
        text-decoration: underline;
    }

    .section-tab.active {
        background: linear-gradient(135deg, #3b82f6, #2563eb);

    }

    .section-tab:hover {
        background: linear-gradient(135deg, #3b83f690, #2564eb8e);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
    }

    .diagnosis-badge {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #3b82f6;
    }

    .profile-card__initials {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-size: 30px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 1px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #4a5568;
        font-size: 14px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        color: #4a5568;
        background: white;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 90px;
    }

    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .form-section-label {
        font-size: 13px;
        font-weight: 700;
        color: #4a5568;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
    }

    .form-section-label span {
        font-weight: 400;
        text-transform: none;
        color: #a0aec0;
        letter-spacing: 0;
    }
</style>