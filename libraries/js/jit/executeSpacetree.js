$(function () {
    $("#div_left").removeClass("col-md-2").addClass("col-md-6");
    $("#div_central").removeClass("col-md-10").addClass("col-md-6");
    $("#spacetree").css("height", $(window).width() / 2);
    $("#spacetree").fadeIn("slow");
    $("#dynatree").css("display", "none");
    $("#rg_infovis").css("display", "none");
    $("#treemap").css("display", "none");
    $("#hypertree").css("display", "none");
//    $("#dynatree").dynatree("getRoot").visit(function (node) {
//        node.select(false);
//    });
    init();
});