function alertmsg(id,type,message) {
    let close_button = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">\n" +
        "    <span aria-hidden=\"true\">&times;</span>\n" +
        "  </button>"
    document.getElementById(id).innerHTML="<strong>"+type.charAt(0).toUpperCase() + type.slice(1)+"!</strong> "+message+close_button
    document.getElementById(id).classList.add("alert-"+type)

}

function scrolltop() {
    $("html, body").animate({scrollTop : 0}, 1100)
}
