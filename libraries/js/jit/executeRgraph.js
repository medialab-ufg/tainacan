$(function () {
    $("#div_left").removeClass("col-md-2").addClass("col-md-6");
    $("#div_central").removeClass("col-md-10").addClass("col-md-6");
    $("#rg_infovis").css("height", $(window).width() / 2);
    $("#rg_infovis").fadeIn("slow");
    $("#spacetree").css("display", "none");
    $("#dynatree").css("display", "none");
    $("#treemap").css("display", "none");
    $("#hypertree").css("display", "none");
//    $("#tree").dynatree("getRoot").visit(function (node) {
//        node.select(false);
//    });
    init();
});