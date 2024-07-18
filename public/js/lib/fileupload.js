// Transaction upload
$(function () {
    $("#uploadTransactionForm").on("submit", function (e) {
        e.preventDefault();

        if ($("#transactionUpload").val() == "") {
            showToast("error", "No file Selected!");
            return;
        }

        var routeUrl = $("#uploadTransactionForm #routeUrl").val();

        var frm = document.getElementById("uploadTransactionForm");
        var form_data = new FormData(frm);


        $.ajax({
            type: "POST",
            url: routeUrl,
            processData: false,
            contentType: false,
            destroy: true,
            cache: false,
            data: form_data,
            success: function (response) {
                console.log(response)
                $("#modalTableAuto")
                    .DataTable()
                    .clear()
                    .rows.add(response.data)
                    .draw();
                // modalTableAuto.clear().rows.add(response.data).draw();
                $("#table").DataTable().ajax.reload()
                // $("#modalTableAuto").DataTable().ajax.reload();
                // $("#modalTableAuto").DataTable().clear().draw();
                // $("#uploadTransaction").modal("hide");

                showToast("success", "Transaction Uploaded");
                $("#transactionUpload").val('');
            },
        });
    });
});
