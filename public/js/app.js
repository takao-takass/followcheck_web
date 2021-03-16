
function page(num){

    $('#pageNumber').val(num);
    $('#searchSubmit').click();

}

function asyncLoad(){
    $('.async-load').each(function(i){
        if(this.attributes["data-async-load"]!=''){
            this.attributes["src"] = this.attributes["data-async-load"];
        }
    });
}