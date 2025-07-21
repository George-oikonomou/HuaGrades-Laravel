<nav class="navbar navbar-expand-lg bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">HuaGrades</a>
        <div class="collapse navbar-collapse">
            <a href="https://www.dit.hua.gr" target="_blank" rel="noopener noreferrer">
                <img src="https://www.hua.gr/wp-content/uploads/2024/07/HUA-Logo-Blue-RGB.png"
                     alt="Harokopio Logo"
                     style="width: 300px; height: auto; cursor: pointer;">
            </a>



            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/leaderboard') }}">Leaderboard</a></li>
                <li class="nav-item" style="margin-left: 20px; cursor: pointer;">
                    <a class="nav-link" onclick="showInfo()">Website Info</a>
                </li>
            </ul>
        </div>

        <!-- Info Modal -->
        <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Πληροφορίες Ασφαλείας</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Κλείσιμο"></button>
                    </div>
                    <div class="modal-body">
                        ℹ️ <strong>Σημαντική Ενημέρωση:</strong><br><br>
                        Η παρούσα εφαρμογή <strong>δεν αποτελεί επίσημη υπηρεσία του Χαροκοπείου Πανεπιστημίου</strong>.<br><br>
                        Δημιουργήθηκε από ομάδα φοιτητών του έτους <strong>2022</strong> με σκοπό να παρέχει στατιστικά, συγκεντρωτικά δεδομένα και χρήσιμες λειτουργίες που αφορούν αποκλειστικά τους φοιτητές του ίδιου έτους.<br><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<style>
    /* NAVBAR BASE */
    .navbar {
        background: linear-gradient(135deg, #11111c, black);
        color: #c7d2fe !important;
    }

    /* BRAND LOGO */
    .navbar-brand {
        color: #c7d2fe !important;
        transition: text-shadow 0.3s ease, color 0.3s ease;
    }

    .navbar-brand:hover {
        text-shadow: 0 0 8px #c7d2fe, 0 0 16px #c7d2fe;
        color: #dbeafe !important;
    }

    /* NAV LINKS INTERACTIVITY */
    .nav-item .nav-link {
        position: relative;
        color: #c7d2fe !important;
        text-decoration: none;
        transition: color 0.3s ease;
        padding-bottom: 2px;
    }

    .nav-item .nav-link::after {
        content: "";
        position: absolute;
        width: 0%;
        height: 2px;
        bottom: 0;
        left: 0;
        background: #c7d2fe;
        transition: width 0.3s ease;
    }

    .nav-item .nav-link:hover::after {
        width: 100%;
    }

    .nav-item .nav-link:hover {
        color: #dbeafe !important;
    }

    /* MODAL STYLING */
    .modal-content {
        background: linear-gradient(135deg, #11111c, grey);
        color: #c7d2fe;
        border-radius: 12px;
        border: 1px solid #2e2e3e;
        box-shadow: 0 0 25px rgba(199, 210, 254, 0.1);
        animation: modalFadeIn 0.5s ease;
    }

    @keyframes modalFadeIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-content{
        z-index: 999;
    }
    .modal-header, .modal-footer {
        border: none;
    }

    .modal-title {
        font-weight: 600;
    }

    .modal-body {
        font-size: 0.95rem;
        line-height: 1.6;
        animation: flickerText 2s linear infinite alternate;
    }

    @keyframes flickerText {
        0%   { opacity: 1; }
        50%  { opacity: 0.95; }
        100% { opacity: 1; }
    }

    /* BUTTONS */
    .btn-secondary {
        background-color: #11111c;
        border-color: #2e2e3e;
        color: #c7d2fe;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background-color: rgb(0, 0, 0);
        border-color: #44445a;
        box-shadow: 0 0 8px #c7d2fe;
    }


    /* Optional: make the image responsive on smaller screens */
    @media (max-width: 768px) {
        .navbar img {
            width: 160px !important;
            margin-top: 10px;
        }
    }

</style>

<script>
    function showInfo() {
        const infoModal = new bootstrap.Modal(document.getElementById('infoModal'));
        infoModal.show();
    }
</script>
