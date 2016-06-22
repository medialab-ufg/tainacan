$(function () {
    $("#div_left").removeClass("col-md-2").addClass("col-md-6");
    $("#div_central").removeClass("col-md-10").addClass("col-md-6");
    $("#hypertree").css("height", $(window).width() / 2);
    $("#hypertree").fadeIn("slow");
    $("#rg_infovis").css("display", "none");
    $("#treemap").css("display", "none");
    $("#spacetree").css("display", "none");
    $("#dynatree").css("display", "none");
    //$("#dynatree").dynatree("getRoot").visit(function (node) {
        //node.select(false);
    //});
    init();
});