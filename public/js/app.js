
function page(num){

    $('#pageNumber').val(num);
    $('#searchSubmit').click();

}

function asyncLoad(){
    $('.async-load').each(function(i){
        if(this.attributes["data-async-load"]!=''){
            var elm = this;
            var uri = this.attributes['data-async-load'].value;
            $.get(uri ,function(data){
                elm.setAttribute("src", uri);
            });
        }
    });
}