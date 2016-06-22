$(function () {
    
    $("#div_left").removeClass("col-md-2").addClass("col-md-6");
    $("#div_central").removeClass("col-md-10").addClass("col-md-6");
    $("#treemap").css("height", $(window).width() / 2);
    $("#treemap").fadeIn("slow");
    $("#spacetree").css("display", "none");
    $("#dynatree").css("display", "none");
    $("#rg_infovis").css("display", "none");
    $("#hypertree").css("display", "none");
//    $("#tree").dynatree("getRoot").visit(function (node) {
//        node.select(false);
//    });
    init();
});