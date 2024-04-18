/**
 * JS file for admin area.
 */
document.querySelector("button#miusage_refresh_api")
?.addEventListener("click", function (e) {
    var request = new XMLHttpRequest();
    var params = `action=miusage_refresh_data&_ajaxnonce=${ajaxload_params.nonce}`;
    request.open("POST", ajaxload_params.ajax_url, true);
    request.setRequestHeader(
        "Content-Type",
        "application/x-www-form-urlencoded; charset=UTF-8"
    );
    request.onload = function ajaxLoad() {
        if (request.status >= 200 && request.status < 400) {
            var serverResponse = JSON.parse(request.responseText);
            var Obj = document.querySelector(".refresh_message");
            Obj.classList.add(serverResponse.data.status);
            Obj.innerHTML = serverResponse.data.msg; // replace element with contents of serverResponse
            Obj.style.opacity = '1';
            setTimeout(function () {
                //Obj.innerHTML = '';
                Obj.style.opacity = '0';
                Obj.classList.remove(serverResponse.data.status);
            }, 3000);
        }
    };

    request.send(params);
});
