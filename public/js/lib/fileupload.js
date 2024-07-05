
// Transaction upload

$("#uploadTransactionForm").on("submit", function (e) {
    e.preventDefault();

    var routeUrl = $("#uploadTransactionForm #routeUrl").val();

    var frm = document.getElementById("uploadTransactionForm");
    var form_data = new FormData(frm);

    const table = $("#table").DataTable();

    $.ajax({
        type: "POST",
        url: routeUrl,
        processData: false,
        contentType: false,
        cache: false,
        data: form_data,
        success: function (response) {
            $("#uploadTransaction").modal("hide");
            table.ajax.reload();
            showToast("success", "Transaction Uploaded");
            $("#transactionUpload").val('');
        },
    });
});
