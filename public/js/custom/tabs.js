
let tableDetail;
var tabFunctions = (function () {
    return {
        init: function (key, table) {
            tableDetail = $(`#${table}`).DataTable({
                processing: true,
                sDom: 'lrtip',
                serverSide: true,
                retrieve: true,
                bInfo: false,
                lengthMenu: [15, 25, 50, "All"],
                order: [[0, 'desc']],
                autoWidth: false
            });

            // $(`#${table} tbody`).on(
            //     "click", "td", function () {
            //         var tr = $(this).closest("tr");
            //         var row = tablePesanan.row(tr);
            //         showModal(row.data().detailRender, row.data().buttonDownload);
            //     }
            // );
        },
    };
})();