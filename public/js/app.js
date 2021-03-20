
function page(num){

    $('#pageNumber').val(num);
    $('#searchSubmit').click();

}

function asyncLoad(){
    $('.async-load').each(function(i){
        if(this.attributes["data-async-load"]!=''){
            var uri = this.attributes['data-async-load'].value;
            $.get(uri ,function(data){
                this.attributes["src"] = uri;
            });
        }
    });
}