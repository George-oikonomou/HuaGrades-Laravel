<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand navbar-brand-custom" href="{{ url('/') }}">HuaGrades</a>
        <div class="collapse navbar-collapse">
            <a href="https://www.dit.hua.gr" target="_blank" rel="noopener noreferrer">
                <img src="https://www.hua.gr/wp-content/uploads/2024/07/HUA-Logo-Blue-RGB.png"
                     alt="HUA Logo"
                     class="img-responsive clickable"
                >
            </a>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="{{ url('/leaderboard') }}">Leaderboard</a>
                </li>
                <li class="nav-item nav-info-link">
                    <a class="nav-link nav-link-custom" onclick="showInfo()">Website Info</a>
                </li>
            </ul>
        </div>

        <!-- Info Modal -->
        <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-custom">
                    <div class="modal-header borderless">
                        <h5 class="modal-title" id="infoModalLabel">Πληροφορίες Ασφαλείας</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Κλείσιμο"></button>
                    </div>
                    <div class="modal-body modal-text-flicker">
                        <strong>Σημαντική Ενημέρωση:</strong><hr>
                        Η παρούσα εφαρμογή <strong>δεν αποτελεί επίσημη υπηρεσία του Χαροκοπείου Πανεπιστημίου</strong>.<br><br>
                        Δημιουργήθηκε από ομάδα φοιτητών του έτους <strong>2022</strong> με σκοπό να παρέχει στατιστικά, συγκεντρωτικά δεδομένα και χρήσιμες λειτουργίες που αφορούν αποκλειστικά τους φοιτητές του ίδιου έτους.<br><br>
                    </div>
                    <div class="modal-footer borderless">
                        <button type="button" class="btn btn-secondary btn-modal" data-bs-dismiss="modal">Κλείσιμο</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<script>
    function showInfo() {
        const infoModal = new bootstrap.Modal(document.getElementById('infoModal'));
        infoModal.show();
    }
</script>
