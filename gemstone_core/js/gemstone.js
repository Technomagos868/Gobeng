var Gemstone = {
    base_url: "http://localhost/web_intero/gemstone/",
    //base_url: "https://interoindonesia.com/gemstone/",
    //base_url: "https://intero.coralisstudio.com/gemstone/",
    GEMSTONE_VERSION: "3.6.0L",
    primeContainer: $("#gs-content"),
    messageTimeout: 3000,
    qsDelay: 500,
    qstimer: null,
    messageTimer: false,
    buttonList: false,
    is_maxed: false,
    navigate: function (targetPage, requireConfirmation, confirmMessage) {
        var confirmation = false;
        if (requireConfirmation == true) {
            if (confirmMessage == "" || confirmMessage == null) {
                confirmMessage = "Are you sure you want to perform this action?";
            }
            confirmation = confirm(confirmMessage);
        } else {
            confirmation = true;
        }
        if (confirmation == true) {
            var targetURL = base_url + targetPage;
            gsShowLoader();
            if (targetPage != "") {
                $.ajax({
                    url: targetURL,
                    type: "POST",
                    data: {
                        ajax: true
                    },
                    success: function (result) {
                        gsOnNavigateSuccess(result, targetPage);
                    },
                    error: function () {
                        gsNavigate("view_404");
                    }
                });
            }
        }
    }
}